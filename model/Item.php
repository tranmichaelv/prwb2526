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
        private ?string $buy_now_price = null,
        private ?string $starting_bid = null,
        private array $pictures = [],
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

    // -------- Date fin d'une vente --------
    public static function get_end_period(string $date, int $amount): string{
        $dt = DateTime::createFromFormat('Y-m-d H:i:s', $date);
        $dt->modify("+{$amount} days");
        // print_r($dt);
        // die();
        return $dt->format('Y-m-d H:i:s');
    }

    // -------- Tout les items disponible --------
    public static function get_all_items(): array{
        $query = self::execute("SELECT i.*, p.*
                                FROM items i
                                    join item_pictures p on p.item = i.id
                                where DATE_ADD(i.created_at, INTERVAL i.duration_days DAY) > :end_date
                                and i.id not in(select i.id
                                               from bids b
                                                join items i on i.id = b.item
                                               where b.amount = i.buy_now_price)
                                order by DATE_ADD(i.created_at, INTERVAL i.duration_days DAY);",
                                ["end_date" =>(new DateTime(AppTime::get_current_datetime()))->format('Y-m-d H:i:s')]);
        $data = $query->fetchAll();
        return self::fetch_items($data);
    }

    // -------- "Items I'm Participating in" --------
    public static function get_bids_items_from_user(User $user): array{
        $query = self::execute("SELECT i.*, p.*
                                FROM items i
	                                join item_pictures p on p.item = i.id
                                    join bids b on b.item = i.id
                                where DATE_ADD(i.created_at, INTERVAL i.duration_days DAY) > :end_date
                                and b.owner = :owner 
                                and i.id not in(select i.id
                                               from bids b
                                                join items i on i.id = b.item
                                               where b.amount = i.buy_now_price)
                                order by DATE_ADD(i.created_at, INTERVAL i.duration_days DAY);"
                                ,["owner"=>$user->id, "end_date" =>(new DateTime(AppTime::get_current_datetime()))->format('Y-m-d H:i:s')]);
    
        $data = $query->fetchAll();
        return self::fetch_items($data);
    }

    // -------- "Other Available Items" --------
    public static function get_other_items_from_user(User $user): array{
        $query = self::execute("SELECT i.*, p.*
                                FROM items i
	                                join item_pictures p on p.item = i.id
                                where DATE_ADD(i.created_at, INTERVAL i.duration_days DAY) > :end_date
                               -- Exclure items sur lesquels l'utilisateur a déjà bid
                                AND NOT EXISTS (
                                    SELECT 1 
                                    FROM bids b1
                                    WHERE b1.item = i.id
                                    AND b1.owner = :owner
                                )

                                -- Exclure items où un bid == buy_now_price
                                AND NOT EXISTS (
                                    SELECT 1
                                    FROM bids b2
                                    WHERE b2.item = i.id
                                    AND i.buy_now_price IS NOT NULL
                                    AND b2.amount = i.buy_now_price
                                )
                                order by DATE_ADD(i.created_at, INTERVAL i.duration_days DAY);"
                                ,["owner"=>$user->id, "end_date" =>(new DateTime(AppTime::get_current_datetime()))->format('Y-m-d H:i:s')]);
    
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
                    'buy_now_price' => $row['buy_now_price'],
                    'starting_bid'  => $row['starting_bid'],
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
                Utils::convert_format_number_for_bids($item['buy_now_price']),
                Utils::convert_format_number_for_bids($item['starting_bid']),
                $item['pictures'] 
            );
        }

        // print_r($results);
        // die();
        return $results;
    }

    public function get_highest_bid(): ?string{
        return Bids::get_hightest_bid_from_item($this->getId()) ?? null;
    }

    public function get_highest_bidder() : ?int{
        return Bids::get_highest_bidder_from_item($this->getId());
    }

    public function is_auction() : bool{
        return Bids::is_auction($this->getId());
    }

    public function has_buy_now() : bool{
        return Bids::has_buy_now($this->getId());
    }

    public function has_bids() : bool{
        return Bids::has_bids($this->getId());
    }

    public function is_direct_sale() : bool{
        return Bids::is_direct_sale($this->getId());
    }

    public function is_bidder() : bool{
        $bidders = Bids::all_bider($this->getId());
        foreach($bidders as $bidder){
            if($bidder['owner'] == $_SESSION['user']->id){
                return true;
            }
        }
        return false;
    }

}