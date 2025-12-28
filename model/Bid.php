<?php

use Dom\Document;

require_once "framework/Model.php";


class Bid extends Model {

    // tout les méthodes dont j'aurai besoin pour gérer l'enchère 
    // .....

    public function __construct(

        public ?float $current_bid,
        public ?float $starting_bid,
        public ?string $end_at,
        public ?float $buy_now_price,
        public ?bool $has_buy_now, 
        public ?bool $has_bids,
        public ?bool $buy_now_reached,
        public ?bool $is_direct_sale

    ) {}

    public static function get_bids_by_item_id($id) : ?array {

        $query = self::execute(
            "SELECT * 
            FROM bids 
            WHERE item = :id", 

            ["id" => $id]
        );

        $bids = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!$bids) {
            return null;
        } else {
            return $bids;
        }
    }
    
    public static function get_seller_information($id) : ?array {
        
        $query = self::execute(
            "SELECT u.id, u.pseudo, u.role
            FROM users u 
            WHERE u.id = (SELECT v.owner
                        FROM v_items_status v 
                        WHERE v.id = :id )",
            ["id" => $id]
        );

        $userInfos = $query->fetch();
        if (!$userInfos) {
            return null;
        } else {
            return $userInfos;
        }
    }

     public static function get_bids_history($id) : ?array {

        $query = self::execute(
            "SELECT 
                u.pseudo,
                b.amount,
                b.created_at
                FROM bids b
                    JOIN users u ON u.id = b.owner
                WHERE b.item = :id
                ORDER BY b.created_at DESC;", 
            ["id" => $id ]
        ); 


        $bids = $query->fetchAll(PDO::FETCH_ASSOC);

        return $bids ?? null;
    } 


      


    public static function get_current_bid_by_item_id($id) : ?Bid {

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
                $results["has_buy_now"],
                $results["has_bids"],
                $results["buy_now_reached"],
                $results["is_direct_sale"]
            );
        }
    }

    public static function bid_is_closed($id) : bool {

        $query = self::execute(
            "SELECT v.*
            FROM v_items_status v
            WHERE v.id = :id
                AND (v.not_purchased_direct_sale
                    OR (v.is_auction
                    AND v.end_at > :now
                    AND (NOT v.has_buy_now OR NOT v.buy_now_reached)) 
                    );",
            ['now' => AppTime::get_current_datetime(), "id" => $id]
        );

        $query->fetchAll();

        if ($query->rowCount() !== 0) {
            return false;
        } 
        return true;
    }

    public static function user_is_owner_by_item_id($id) /*: bool*/ {

        
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