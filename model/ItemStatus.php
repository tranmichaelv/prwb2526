<?php
require_once "framework/Model.php";
require_once "utils/AppTime.php";
require_once "utils/Utils.php";
require_once "model/Bids.php";


class ItemStatus extends Model {
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

    public function getEndAt(): string { return $this->end_at; }

    public function getBidCount(): int { return $this->bid_count; }

    public function getMaxBid(): string { return $this->max_bid; }

    public function isDirectSale(): bool { return $this->is_direct_sale; }

    public function isAuction(): bool { return $this->is_auction; }

    public function hasBuyNow(): bool { return $this->has_buy_now; }

    public function hasBids(): bool { return $this->has_bids; }

    public function isBuyNowReached(): bool { return $this->buy_now_reached; }

    public function isNotPurchasedDirectSale(): bool{ return $this->not_purchased_direct_sale; }

    // -------- Tout les items disponible --------
    public static function get_all_items(): array{
        $query = self::execute("SELECT v.*
                                FROM v_items_status v
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

    public static function get_filtered_items(array $items, string $query) : array {
        $list = [];
        $query = strtolower($query);
        foreach ($items as $item) {

            $match =
                str_contains(strtolower($item->getTitle()), $query) ||
                str_contains(strtolower($item->getDescription()), $query) ||
                str_contains(strtolower($item->getOwnerName()), $query);

            if ($match) {
                $list[] = $item;
            }
        }

        return $list;
    }

    // -------- "Items I'm Participating in" --------
    public static function get_bids_items_from_user(int $userId): array{
        $query = self::execute("SELECT v.*
                                FROM v_items_status v 
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
        $query = self::execute("SELECT v.*
                                FROM v_items_status v
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
                                and v.owner != :owner
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
                ];
            }
            
        }
    
        // Converti en objets Item
        $results = [];
        foreach ($items as $item) {
            
            $results[] = new ItemStatus(
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
            );
        }

        return $results;
    }


    public function get_highest_bidder_id() : ?int{
        return Bids::get_highest_bidder_from_item($this->getId());
    }


    // -------- "My Active Items" --------
    public static function get_user_items(int $userId) : array {
        // $query = self::execute("SELECT v.*, p.*
        $query = self::execute("SELECT v.*
                                FROM v_items_status v
                                    join users u on u.id = v.owner
                                    -- join item_pictures p on p.item = v.id
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
        $query = self::execute("SELECT v.*
                                FROM v_items_status v
                                    join users u on u.id = v.owner
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
        $query = self::execute("SELECT v.*
                                FROM v_items_status v
                                    join users u on u.id = v.owner
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

    // -------- "My Purchases" --------
    public static function get_user_purchases(int $userId) : array {
        $query = self::execute("SELECT v.*
                                FROM v_items_status v
                                    join bids b on b.item = v.id and v.max_bid = b.amount
                                where ((v.end_at <= :current_date and v.bid_count > 0)
                                or (( v.is_direct_sale = 1
                                    AND v.not_purchased_direct_sale = 0)
                                    OR
                                    ( v.is_auction = 1
                                    AND v.buy_now_reached = 1))) 
                                and b.owner = :id;"
                                ,["id"=>$userId, "current_date" =>(new DateTime(AppTime::get_current_datetime()))->format('Y-m-d H:i:s')]); 
        
        $data = $query->fetchAll();
        return self::fetch_items($data);
    }

    // -------- "TOTAL SPENT $$ AVERAGE TICKET PURCHASES" --------
    public static function get_total_and_average_of_purchases(int $userId) : array {
        $query = self::execute("SELECT sum(v.max_bid) as total, avg(v.max_bid) as average, count(*) as number_of_purchases
                                FROM v_items_status v
                                    join bids b on b.item = v.id and v.max_bid = b.amount
                                where ((v.end_at <= :current_date and v.bid_count > 0)
                                or (( v.is_direct_sale = 1
                                    AND v.not_purchased_direct_sale = 0)
                                    OR
                                    ( v.is_auction = 1
                                    AND v.buy_now_reached = 1))) 
                                and b.owner = :id;"
                                ,["id"=>$userId, "current_date" =>(new DateTime(AppTime::get_current_datetime()))->format('Y-m-d H:i:s')]); 
        
        $data = $query->fetchAll();
        $result = [];
        foreach ($data as $row) {
            $result[] = [
                "total" => Utils::convert_format_number_for_bids((float)$row["total"]),
                "average" => Utils::convert_format_number_for_bids((float)$row["average"]),
                "number_of_purchases" => (int)$row["number_of_purchases"]
            ];
        }
        return $result;
    }

    // -------- "TOP SELLER" --------
    public static function get_top_seller(int $userId) : string {
        $query = self::execute("SELECT u.pseudo
                                FROM v_items_status v
                                    join users u on u.id = v.owner
                                    join bids b on b.item = v.id and v.max_bid = b.amount
                                where ((v.end_at <= :current_date and v.bid_count > 0)
                                or (( v.is_direct_sale = 1
                                    AND v.not_purchased_direct_sale = 0)
                                    OR
                                    ( v.is_auction = 1
                                    AND v.buy_now_reached = 1))) 
                                and b.owner = :id
                                group by u.id, u.pseudo
                                Limit 1;"
                                ,["id"=>$userId, "current_date" =>(new DateTime(AppTime::get_current_datetime()))->format('Y-m-d H:i:s')]); 
        
        $data = $query->fetch();
        return $data["pseudo"];
    }

    // -------- "My Completed Sales" --------
    public static function get_user_completed_sales(int $userId) : array {
        $query = self::execute("SELECT v.*
                                FROM v_items_status v
                                    join bids b on b.item = v.id and v.max_bid = b.amount
                                where ((v.end_at <= :current_date and v.bid_count > 0)
                                or (( v.is_direct_sale = 1
                                    AND v.not_purchased_direct_sale = 0)
                                    OR
                                    ( v.is_auction = 1
                                    AND v.buy_now_reached = 1))) 
                                and v.owner = :id;"
                                ,["id"=>$userId, "current_date" =>(new DateTime(AppTime::get_current_datetime()))->format('Y-m-d H:i:s')]); 
        
        $data = $query->fetchAll();
        return self::fetch_items($data);
    }


    // -------- "TOP BUYER" --------
    public static function get_top_bidder(int $userId) : string {
        $query = self::execute("SELECT u.pseudo
                                FROM v_items_status v
                                    join bids b on b.item = v.id and v.max_bid = b.amount
                                    join users u on u.id = b.owner
                                where ((v.end_at <= :current_date and v.bid_count > 0)
                                or (( v.is_direct_sale = 1
                                    AND v.not_purchased_direct_sale = 0)
                                    OR
                                    ( v.is_auction = 1
                                    AND v.buy_now_reached = 1))) 
                                and v.owner = :id
                                group by u.id, u.pseudo
                                Limit 1;"
                                ,["id"=>$userId, "current_date" =>(new DateTime(AppTime::get_current_datetime()))->format('Y-m-d H:i:s')]); 
        
        $data = $query->fetch();
        return $data["pseudo"];
    }

     // -------- "TOTAL REVENUE $$ AVERAGE TICKET SALES" --------
     public static function get_total_and_average_of_sales(int $userId) : array {
        $query = self::execute("SELECT sum(v.max_bid) as total, avg(v.max_bid) as average, count(*) as number_of_purchases
                                FROM v_items_status v
                                    join bids b on b.item = v.id and v.max_bid = b.amount
                                where ((v.end_at <= :current_date and v.bid_count > 0)
                                or (( v.is_direct_sale = 1
                                    AND v.not_purchased_direct_sale = 0)
                                    OR
                                    ( v.is_auction = 1
                                    AND v.buy_now_reached = 1))) 
                                and v.owner = :id;"
                                ,["id"=>$userId, "current_date" =>(new DateTime(AppTime::get_current_datetime()))->format('Y-m-d H:i:s')]); 
        
        $data = $query->fetchAll();
        $result = [];
        foreach ($data as $row) {
            $result[] = [
                "total" => Utils::convert_format_number_for_bids((float)$row["total"]),
                "average" => Utils::convert_format_number_for_bids((float)$row["average"]),
                "number_of_purchases" => (int)$row["number_of_purchases"]
            ];
        }
        return $result;
    }



    /*========  Renvoie un Objet Item grace à l'ID fourni  ========*/
    public static function get_item_by_id($id) : ?ItemStatus {
        
        $query = self::execute("SELECT * FROM v_items_status WHERE id = :id", 
        ['id' => $id]
        );
        $Item = $query->fetch(); 

        if ($query->rowCount() == 0){
            return null;
            // die ("Voir la méthode get_item_by_id dans Items Status ");
        } else {
            return new ItemStatus(
                $Item["id"], 
                $Item["title"], 
                $Item["description"],
                $Item["owner"],  
                $Item["created_at"],
                $Item["duration_days"], 
                $Item["end_at"],
                $Item["bid_count"],
                $Item["is_direct_sale"], 
                $Item["is_auction"],
                $Item["has_buy_now"],
                $Item["has_bids"],
                $Item["buy_now_reached"],
                $Item["not_purchased_direct_sale"],
                $Item['buy_now_price'],
                $Item['starting_bid'],
                $Item['max_bid']
            );
        }
    }


    /*====== L'image principale =======*/
    public function get_main_picture_by_item_id() : ?string {
        
        $query = self::execute(

            "SELECT ip.picture_path
            FROM item_pictures ip 
            WHERE ip.item = :id 
            ORDER BY ip.priority ASC", 

            ['id' => $this->id]
        );

        $results = $query->fetch();

        return $results ? $results["picture_path"] : null;
    }


    /*===== Images secondaires  *Additional images*  ======*/
    public function get_all_pictures_by_item_id() : array {
        
        $query = self::execute(
            "SELECT ip.picture_path, ip.priority
            FROM item_pictures as ip
            WHERE ip.item = :id
            ORDER BY ip.priority ASC",
            ['id' => $this->id]
        );

        $results = $query->fetchAll(PDO::FETCH_ASSOC);

       return $results;
    
    }
    
    public function get_picture_by_priority(int $priority, int $item_id) : string {

        $query = self::execute(
            "SELECT ip.picture_path
            FROM item_pictures ip 
            WHERE ip.priority = :priority 
            AND ip.item = :item", 
            ["priority" => $priority, "item" => $item_id]
        );

        $pic = $query->fetch();
        return $pic['picture_path'];
    }

    public function get_first_picture_path() : ?string {

        $query = self::execute(
            "SELECT ip.picture_path
            FROM item_pictures ip 
            WHERE ip.priority = 1 
            AND ip.item = :item", 
            ["item" => $this->getId()]
        );

        $pic = $query->fetchColumn();
        return $pic;
    }

    public function get_number_of_pics() : int{
        return count(ItemPicture::get_by_item($this->getId()));
    }

    // Refactorisation a partir d'ici // 
    // je construie déjà un item complet lors de l'appel de get_item_by_id(),
    //  donc je peux acceder aux autres méthode 
    /*
    public static function get_current_bid_by_item_id($id) : ?ItemStatus {

        $query = self::execute(
            "SELECT * 
            FROM v_items_status
            WHERE id = :id", 
            ["id" => $id]
        );

        $results = $query->fetch(PDO::FETCH_ASSOC);

        if ($query->rowCount()==0) {
            return null;
        } else {
            return new ItemStatus(
                $results["id"], 
                $results["title"], 
                $results["description"],
                $results["owner"],  
                $results["created_at"],
                $results["duration_days"], 
                $results["end_at"],
                $results["bid_count"],
                $results["is_direct_sale"], 
                $results["is_auction"],
                $results["has_buy_now"],
                $results["has_bids"],
                $results["buy_now_reached"],
                $results["not_purchased_direct_sale"],
                $results['buy_now_price'],
                $results['starting_bid'],
                $results['max_bid']
            );
        }
    }*/
    
    public function get_bids_history() : array {

        $query = self::execute(
            "SELECT 
                u.id,
                u.pseudo,
                b.amount,
                b.created_at
                FROM bids b
                    JOIN users u ON u.id = b.owner
                WHERE b.item = :id
                ORDER BY b.created_at DESC;", 
            ["id" =>$this->getId() ]
        ); 

        $bids = $query->fetchAll();
        return $bids ;
    } 

    public function get_seller_information() : ?array {
        
        $query = self::execute(
            "SELECT u.id, u.pseudo, u.role
            FROM users u 
            WHERE u.id = (SELECT v.owner
                        FROM v_items_status v 
                        WHERE v.id = :id )",
            ["id" => $this->getId()]
        );

        $user_infos = $query->fetch();
        if (!$user_infos) {
            return null;
        } else {
            return $user_infos;
        }
    }
    
    public function bid_is_closed() : bool {

        if($this->has_buy_now==0){
            if(AppTime::get_current_datetime()>$this->end_at){
                return true;
            }
       } else {
        if($this->isBuyNowReached()==1 || AppTime::get_current_datetime()>$this->end_at )
            return true;
        }

       return false;
    }

    public function user_is_owner(int $current_user_id) : bool {

        $owner = User::get_owner_infos_by_item_id($this->getId()); 
        return $owner["id"] == $current_user_id;
        
    }

    public function buy_now_price_exist() : bool {
        
        if ($this->getId() !== null && $this->has_buy_now) {
            return true;
        }
        return false;
    }

    public function has_bids() : bool {
    
        if ($this->has_bids==1) {
            return true;
        }
        return false;
    } 
    
    public function has_buy_now() : bool {

        if ($this->has_buy_now==1) {
            return true;
        }
        return false;
    }
    public function buy_now_reached() : bool {

        if ($this->buy_now_reached) {
            return true;
        }
        return false;
    }

    public function is_auction() : bool {

        if ($this->has_buy_now==0) {
            return true;
        }
        return false;
    }
    // ========= Faire une enchère ou acheter un Item ========== //

    public static function bid($owner,$item_id,$amount) : bool {

        
        // pas besoin de vérifier si ouvert/fermée 
        $item = self::get_item_by_id($item_id);
        $current = $item->hasBids() ? (float) $item->getMaxBid(): (float) $item->getStartingBid();
        
        // si je viens d'une annonce qui n'a pas d'enchère enocre, 
        // l'utilisateur a le droit de mettre un prix qui est égale au starting_at
        $canInsert = $current === (float) $item->getStartingBid() ? true : false;
       
        $now = AppTime::get_current_datetime();
        if ($amount > $current || $canInsert == true) {
            self::execute("INSERT INTO bids (owner, item, created_at, amount) VALUES (:owner, :item, :created_at, :amount)", 
                [
                    "owner" => $owner->getId(),
                    "item" => $item->getId(), 
                    "created_at" => $now, 
                    "amount" => $amount
                ]
            ); 
            return true;
            
        }  
        return false;

    }

    /*
    public static function buy($owner,$item_id,$amount) : bool {
        
        $item = self::get_item_by_id($item_id);
        $current = $item->getBuyNowPrice();

        if ($amount >= $current) {
            self::execute(
                "INSERT INTO bids (owner, item, created_at, amount) VALUES (:owner, :item, :created_at, :amount)", 
                [
                    "owner" => $owner,
                    "item" => $item_id,
                    "created_at" => AppTime::get_current_datetime(), 
                    "amount" => $amount
                ]
            );
            return true;
        }
        return false;

    }
    */

}