<?php

require_once "framework/Model.php";
require_once "framework/Configuration.php";
require_once "utils/AppTime.php";


class Item extends Model {

     private ?float $instant_purchase = null;
     private ?float $sale_price = null;  

    public function __construct(
        private ?int $id,
        private string $title,
        private string $description,
        private int $owner,
        private int $duration_days,
        private ?float $starting_bid = null,
        private ?float $buy_now_price = null, // valeur finale dans la BD
        private ?string $created_at = null,
            

    ) {
        $this->created_at = $this->created_at ?? AppTime::get_current_datetime();
      }

    public function get_id(): ?int {
         return $this->id; 
    }

    public function get_title(): string {
         return $this->title; 
    }

    public function set_title(string $title): void {
         $this->title = $title; 
     }

    public function get_description(): string {
         return $this->description; 
    }
    public function set_description(string $description): void { 
        $this->description = $description; 
    }

    public function get_owner_id(): int {
         return $this->owner; 
    }

    public function get_created_at(): string {
         return $this->created_at; 
    }

    public function get_duration_days(): int {
         return $this->duration_days; 
    }
    public function set_duration_days(int $d): void {
         $this->duration_days = $d; 
    }

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

        if($has_bid){
           // si buy_now est rempli, il doit être > 0 et doit être > starting_bid (si pas null)
            if($has_instant && $has_bid && $this->instant_purchase <= $this->starting_bid) {
               $errors['buy_now_price'][] = "Buy now price must be strictly higher than the starting bid";
            }    
        

          $this->buy_now_price = $has_instant ? $this->instant_purchase : null;

        } else {
    
              if (!$has_sale) {
               $errors['sale_price'][] = "An ad without a bid must have a buy now price.";
               return $errors;
              }

                //  sale_price devient buy_now_price
               $this->buy_now_price = $this->sale_price;
          }

        // Une annonce sans enchère doit avoir un buy_now_price
       // if(!$has_bid && !$has_instant) {
       //        $errors['buy_now_price'][] = "An ad without a bid must have a buy now price";
      //  }


        return $errors;
    }

    public function persist(): array {
     $errors =$this->validate();
     if (!empty($errors)){
          return $errors;
     }
          // on convertit les null en 0 pour la DB
     $starting_db = $this->starting_bid ?? 0.0;
     $buy_now_db  = $this->buy_now_price ?? 0.0;

     if($this->id == null) {
          $sql = "INSERT INTO items
                        (title, description, owner, created_at,
                         buy_now_price, duration_days, starting_bid)
                    VALUES
                        (:title, :description, :owner, :created_at,
                         :buy_now_price, :duration_days, :starting_bid)";
          self::execute($sql, [
                ":title"         => $this->title,
                ":description"   => $this->description,
                ":owner"         => $this->owner,
                ":created_at"    => $this->created_at,
                ":buy_now_price" => $buy_now_db,
                ":duration_days" => $this->duration_days,
                ":starting_bid"  => $starting_db,
            ]);

            $this->id = self::lastInsertId();               

     
     }  else {
            $sql = "UPDATE items SET
                        title = :title,
                        description = :description,
                        buy_now_price = :buy_now_price,
                        duration_days = :duration_days,
                        starting_bid = :starting_bid
                    WHERE id = :id";
            self::execute($sql, [
                ":title"         => $this->title,
                ":description"   => $this->description,
                ":buy_now_price" => $buy_now_db,
                ":duration_days" => $this->duration_days,
                ":starting_bid"  => $starting_db,
                ":id"            => $this->id,
            ]);
          }

        return [];
    }

    public function delete(): void {
        if ($this->id === null) return;

        // récuperer les images (pour supp les fichiers)
        $pics = ItemPicture::get_by_item($this->id);

        foreach ($pics as $p){
          $main = $p->get_picture_path();
          $thumb = preg_replace('/\.jpg$/', '_thumbnail.jpg', $main);

          if (file_exists($main)) unlink($main);
          if (file_exists($thumb)) unlink($thumb);
        }

        self::execute("DELETE FROM item_pictures WHERE item = :id",[":id" => $this->id]);
            
        self::execute("DELETE FROM bids WHERE item = :id", [":id" => $this->id]);

        self::execute("DELETE FROM items WHERE id = :id", [":id" => $this->id]);
        
        $this->id = null;
    }

    // récupère un item depuis la bd à partir de son id ou return null
    public static function get_by_id(int $id): ?Item {
          $sql = "SELECT * FROM items WHERE id = :id";
          $stmt = self::execute($sql, [":id" => $id]);
          $row = $stmt->fetch();
          if (!$row) return null;

         $item = new Item(
               id: (int)$row["id"],
               title: $row["title"],
               description: $row["description"] ?? "",
               owner: (int)$row["owner"],
               duration_days: (int)$row["duration_days"],
               starting_bid: $row["starting_bid"] !== null ? (float)$row["starting_bid"] : null,
               buy_now_price: $row["buy_now_price"] !== null ? (float)$row["buy_now_price"] : null,
               created_at: $row["created_at"]
          );

          $starting = (float)$row["starting_bid"];
          $buy = $row["buy_now_price"] !== null ? (float)$row["buy_now_price"] : null;

          if ($starting > 0) {
               // Auction : buy_now_price = instant purchase
               $item->set_instant_purchase($buy);
               $item->set_sale_price(null);
          } else {
               // Direct sale : buy_now_price = sale price
               $item->set_sale_price($buy);
               $item->set_instant_purchase(null);
            }

       return $item;
    }

    //  Vérifie si un titre existe déjà pour un owner
    private static function title_exists_for_owner(string $title, int $owner, ?int $exclude_id = null) : bool {
          $sql = "SELECT COUNT(*) AS cnt FROM items
               WHERE owner = :owner AND title = :title";
          
          $params = [
               ":owner" => $owner,
               ":title" => $title,
          ];
          if($exclude_id !== null){
               $sql .= " AND id <> :id";
               $params[":id"] = $exclude_id;
          }  

          $stmt = self::execute($sql,$params);
          $row = $stmt->fetch();
          return $row && (int)$row["cnt"] > 0;
    }

    // respecter les 10 chiffres max (dont 2 après la virgule)
    private static function fits_decimal_10_2(float $value) : bool {
          $abs = abs($value);
          return $abs < 100000000 && round($abs,2) == $abs;
    }

    /*========  Renvoie un Objet Item grace à l'ID fourni  ========*/
    public static function get_item_by_id($id) : ?Item {
        
        $error = [];
        $query = self::execute("SELECT * FROM v_items_status WHERE id = :id", 
        ['id' => $id]
        );
        $Item = $query->fetch(); 

        if ($query->rowCount() == 0){
            return null;
        } else {
            return new Item(
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
                Utils::convert_format_number_for_bids($Item['starting_bid']),
                Utils::convert_format_number_for_bids($Item['max_bid']),
                /*
                $Item["buy_now_price"],
                $Item["starting_bid"],
                $Item["max_bid"] 
                */
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
    public function get_all_pictures_by_item_id() : ?array {
        
        $query = self::execute(
            "SELECT ip.picture_path
            FROM item_pictures as ip
            WHERE ip.item = :id
            ORDER BY ip.priority ASC",
            ['id' => $this->id]
        );

        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        if ($query->rowCount() == 0) {
            return null;
        } else {
            return $results;
        } 
    }
    

    // ========= Faire une enchère ou acheter un Item ========== //

    public static function bid($owner,$item_id,$amount) : bool {

        //var_dump($item, $owner, $amount);
        //die (" bien arrivé sur bid dans Item ");
        // pas besoin de vérifier si ouvert/fermée 
        $item = self::get_item_by_id($item_id);
        $current = $item->hasBids() ? (float) $item->getMaxBid(): (float) $item->getStartingBid();
       
        
       

        $now = AppTime::get_current_datetime();
        if ($amount > $current) {
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
}