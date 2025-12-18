<?php

require_once "framework/Model.php";
require_once "Bids.php";

class Item extends Model {
    // -------- Constructeur --------
    public function __construct(
        private int $id,
        private string $title,
        private string $description,
        private int $owner,
        private string $created_at,
        private int $duration_days,
        private string $end_at,
        private int $bid_count,
        private bool $is_direct_sale,
        private bool $is_auction,
        private bool $has_buy_now,
        private bool $has_bids,
        private bool $buy_now_reached,
        private bool $not_purchased_direct_sale,
        private ?string $buy_now_price = null,
        private ?string $starting_bid = null,
        private ?string $max_bid = null,
        private array $pictures = []
    ) {}

    // -------- Getters --------
    public function getId(): int { return $this->id; }

    public function getTitle(): string { return $this->title; }

    public function getDescription(): string { return $this->description; }

    public function getOwner(): int { return $this->owner; }

    public function getCreatedAt(): string { return $this->created_at; }

    public function getDurationDays(): int { return $this->duration_days; }

    public function getBuyNowPrice(): ?string { return $this->buy_now_price; }

    public function getStartingBid(): ?string { return $this->starting_bid; }

    public function getPictures(): array { return $this->pictures; }

    public function getEndAt(): string { return $this->end_at; }

    public function getBidCount(): int { return $this->bid_count; }

    public function getMaxBid(): string { return $this->max_bid; }

    public function isDirectSale(): bool { return $this->is_direct_sale; }

    public function isAuction(): bool { return $this->is_auction; }

    public function hasBuyNow(): bool { return $this->has_buy_now; }

    public function hasBids(): bool { return $this->has_bids; }

    public function isBuyNowReached(): bool { return $this->buy_now_reached; }

    public function isNotPurchasedDirectSale(): bool{ return $this->not_purchased_direct_sale; }

    public function setPictures(array $pictures): void { $this->pictures = $pictures; }

    // -------- Tout les items disponible --------
    public static function get_all_items(): array{
        $query = self::execute("SELECT v.*, p.*
                                FROM v_items_status v
                                    join item_pictures p on p.item = v.id
                                where v.end_at > :end_date 
                                and (
                                        ( is_direct_sale = 1
                                            AND not_purchased_direct_sale = 1)
                                        OR
                                        ( is_auction = 1
                                            AND buy_now_reached = 0)
                                    )
                                ORDER BY end_at ASC;",
                                ["end_date" =>(new DateTime(AppTime::get_current_datetime()))->format('Y-m-d H:i:s')]);
        $data = $query->fetchAll();
        return self::fetch_items($data);
    }

    // -------- "Items I'm Participating in" --------
    public static function get_bids_items_from_user(int $userId): array{
        $query = self::execute("SELECT v.*, p.*
                                FROM v_items_status v 
	                                join item_pictures p on p.item = v.id
                                    join bids b on b.item = v.id
                                where end_at > :end_date
                                and b.owner = :owner 
                                and (
                                        ( is_direct_sale = 1
                                            AND not_purchased_direct_sale = 1)
                                        OR
                                        ( is_auction = 1
                                            AND buy_now_reached = 0)
                                    )
                                order by end_at;"
                                ,["owner"=>$userId, "end_date" =>(new DateTime(AppTime::get_current_datetime()))->format('Y-m-d H:i:s')]);
    
        $data = $query->fetchAll();
        return self::fetch_items($data);
    }

    // -------- "Other Available Items" --------
    public static function get_other_items_from_user(int $userId): array{
        $query = self::execute("SELECT v.*, p.*
                                FROM v_items_status v
	                                join item_pictures p on p.item = v.id
                                where end_at > :end_date
                               -- Exclure items sur lesquels l'utilisateur a déjà bid
                                AND NOT EXISTS (
                                    SELECT 1 
                                    FROM bids b1
                                    WHERE b1.item = v.id
                                    AND b1.owner = :owner
                                )
                                and (
                                        ( is_direct_sale = 1
                                            AND not_purchased_direct_sale = 1)
                                        OR
                                        ( is_auction = 1
                                            AND buy_now_reached = 0)
                                    )
                                order by end_at;"
                                ,["owner"=>$userId, "end_date" =>(new DateTime(AppTime::get_current_datetime()))->format('Y-m-d H:i:s')]);
    
        $data = $query->fetchAll();
        return self::fetch_items($data);
    }

    // -------- Transforme les requêtes SQL en Object 'Item' --------
    public static function fetch_items(array $data): array{
        $items = [];
        foreach ($data as $row) {
            $item_id = $row['id'];
    
            // Intialise si c'est la premiere fois
            if (!isset($items[$item_id])) {
                $items[$item_id] = [
                    'id'            => $row['id'],
                    'title'         => $row['title'],
                    'description'   => $row['description'],
                    'owner'         => $row['owner'],
                    'created_at'    => $row['created_at'],
                    'duration_days' => $row['duration_days'],
                    'end_at'        => $row['end_at'],
                    'bid_count'     => $row['bid_count'],
                    'is_direct_sale'=> (bool) $row['is_direct_sale'],
                    'is_auction'    => (bool) $row['is_auction'],
                    'has_buy_now'   => (bool) $row['has_buy_now'],
                    'has_bids'      => (bool) $row['has_bids'],
                    'buy_now_reached' => (bool) $row['buy_now_reached'],
                    'not_purchased_direct_sale'  => (bool) $row['not_purchased_direct_sale'],

                    'buy_now_price' => $row['buy_now_price'],
                    'starting_bid'  => $row['starting_bid'],
                    'max_bid'       => $row['max_bid'],
                    'pictures'      => []
                ];
            }
    
            
            // Ajouter les images
            $items[$item_id]['pictures'][] = [
                    "path"     => $row["picture_path"],
                    "priority" => $row["priority"]
            ];
            
        }
    
        // Converti en objets Item
        $results = [];
        foreach ($items as $item) {
            $results[] = new Item(
                $item['id'],
                $item['title'],
                $item['description'],
                $item['owner'],
                $item['created_at'],
                $item['duration_days'],
                $item['end_at'],
                $item['bid_count'],
                $item['is_direct_sale'],
                $item['is_auction'],
                $item['has_buy_now'],
                $item['has_bids'],
                $item['buy_now_reached'],
                $item['not_purchased_direct_sale'],
                Utils::convert_format_number_for_bids($item['buy_now_price']),
                Utils::convert_format_number_for_bids($item['starting_bid']),
                Utils::convert_format_number_for_bids($item['max_bid']),
                $item['pictures']
            );
        }

        // print_r($results);
        // die();
        return $results;
    }


    public function get_highest_bidder_id() : ?int{
        return Bids::get_highest_bidder_from_item($this->getId());
    }


    // -------- "My Active Items" --------
    public static function get_user_items(int $userId) : array {
        $query = self::execute("SELECT v.*, p.*
                                FROM v_items_status v
                                    join users u on u.id = v.owner
                                    join item_pictures p on p.item = v.id
                                where end_at > :current_date
                                and u.id = :id
                                and v.id not in(SELECT v.id
                                                from v_items_status v
                                                where v.max_bid = v.buy_now_price)
                                order by end_at;"
                                ,["id"=>$userId, "current_date" =>(new DateTime(AppTime::get_current_datetime()))->format('Y-m-d H:i:s')]); 
        
        $data = $query->fetchAll();
        return self::fetch_items($data);
    }

    // -------- "My Closed Unsold Items" --------
    public static function get_user_closed_unsold_items(int $userId) : array {
        $query = self::execute("SELECT v.*, p.*
                                FROM v_items_status v
                                    join users u on u.id = v.owner
                                    join item_pictures p on p.item = v.id
                                where end_at <= :current_date
                                and u.id = :id 
                                and v.has_bids = false 
                                and v.id not in(SELECT v.id
                                                from v_items_status v
                                                where v.max_bid = v.buy_now_price)
                                order by end_at;"
                                ,["id"=>$userId, "current_date" =>(new DateTime(AppTime::get_current_datetime()))->format('Y-m-d H:i:s')]); 
        
        $data = $query->fetchAll();
        return self::fetch_items($data);
    }

    // -------- "My Sold Items" --------
    public static function get_user_sold_items(int $userId) : array {
        $query = self::execute("SELECT v.*, p.*
                                FROM v_items_status v
                                    join users u on u.id = v.owner
                                    join item_pictures p on p.item = v.id
                                where end_at <= :current_date
                                and u.id = :id
                                and v.has_bids != false
                                or v.id in(SELECT v.id
                                                from v_items_status v
                                                    join users u on u.id = v.owner
                                                where v.max_bid = v.buy_now_price
                                                and u.id = :id)
                                order by end_at;"
                                ,["id"=>$userId, "current_date" =>(new DateTime(AppTime::get_current_datetime()))->format('Y-m-d H:i:s')]); 
    
        $data = $query->fetchAll();
        return self::fetch_items($data);
    }

    public function getTimeLeft() : array{
        return Utils::get_time_left($this->getEndAt(), $this->getDurationDays());
    }


    public function getOwnerName() : string {
        $query = self::execute("select u.pseudo
                                from items i 
                                    join users u on u.id = i.owner
                                where i.id = :id", ["id"=>$this->getId()]);
        $data = $query->fetch();
        return $data['pseudo'];                       
    }
}