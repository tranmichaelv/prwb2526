<?php 

require_once "framework/Model.php";

class ItemSaleStatus extends Model {

    // tout les méthodes dont j'aurai besoin pour gérer l'enchère 
    // .....

    public function __construct(

        private int $item_id,
        private ?float $current_bid,
        private ?float $starting_bid,
        private ?string $end_at,
        private ?float $buy_now_price,
        private bool $has_buy_now, 
        private bool $has_bids,
        private bool $buy_now_reached,
        private bool $is_direct_sale

    ) {}


    public function getItemId() : int { return $this->item_id; }

    public function getCurrentBid() : ?float { return $this->current_bid; }

    public function getStartingBid() : float {return $this->starting_bid;}

    public function getEndAt() : string { return $this->end_at; }

    public function getBuyNowPrice() : float { return $this->buy_now_price; }

    public function getHasBuyNow() : bool { return $this->has_buy_now; }

    public function getHasBids() : bool { return $this->has_bids; }

    public function getBuyNowReached() : bool { return $this->buy_now_reached;}

    public function getIsDirectSale() : bool { return $this->is_direct_sale; }



    public static function get_current_bid_by_item_id($id) : ?ItemSaleStatus {

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
            return new ItemSaleStatus(
                $results["id"],
                $results["max_bid"], 
                $results["starting_bid"],
                $results["end_at"],
                $results["buy_now_price"],
                $results["has_buy_now"],
                $results["has_bids"],
                $results["buy_now_reached"],
                $results["is_direct_sale"]
            );
        }
    }

    public function get_bids_by_item_id() : ?array {

        $query = self::execute(
            "SELECT * 
            FROM bids 
            WHERE item = :id", 

            ["id" => $this->item_id]
        );

        $bids = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!$bids) {
            return null;
        } else {
            return $bids;
        }
    }
    
    public function get_seller_information() : ?array {
        
        $query = self::execute(
            "SELECT u.id, u.pseudo, u.role
            FROM users u 
            WHERE u.id = (SELECT v.owner
                        FROM v_items_status v 
                        WHERE v.id = :id )",
            ["id" => $this->item_id]
        );

        $userInfos = $query->fetch();
        if (!$userInfos) {
            return null;
        } else {
            return $userInfos;
        }
    }

    
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
            ["id" =>$this->item_id ]
        ); 

        $bids = $query->fetchAll();
        return $bids ;
    } 



    public function bid_is_closed() : bool {

        if($this->has_buy_now==0){
            if(AppTime::get_current_datetime()>$this->end_at){
                return true;
            }
       } else {
        if($this->buy_now_reached()==1 || AppTime::get_current_datetime()>$this->end_at )
            return true;
        }

       return false;
    }

    /*public function closed_unslod() : bool {

        if ()
    }*/

    public function user_is_owner(User $current_user) : bool {

        $owner = User::get_owner_infos_by_item_id($this->getItemId()); // ajouter la méthode dans le modèle 
        return $owner["id"] == $current_user->getId();
        
    }

    public function buy_now_price_exist() : bool {
        
        if ($this->item_id !== null && $this->has_buy_now) {
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

   
    
    /*
    public static function get_current_bid_by_item_id_original($id) : ?Bid {

        $query = self::execute(
            "SELECT * 
            FROM v_items_status
            WHERE id = :id", 
            ["id" => $id]
        );

        $results = $query->fetch(PDO::FETCH_ASSOC);

        if (!$results) {
            return null;
        } else {
            return new Bid(
                $results["max_bid"], 
                $results["starting_bid"],
                $results["end_at"],
                $results["buy_now_price"],
                $results[""],
                $results[""],
                $results[""], 
                $results
                
            );
        }
    } */
} 

?>