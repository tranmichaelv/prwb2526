<?php

use Dom\Document;

require_once "framework/Model.php";


class Bid extends Model {

    // tout les méthodes dont j'aurai besoin pour gérer l'enchère 
    // .....

    public function __construct(

        public ?float $current_bid,
        public ?float $buy_now_price

    ) {}

    // L'enchère en cours 
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
                $results["buy_now_price"]
            );
        }
    }

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
            "SELECT u.* 
            FROM v_items_status v JOIN users u ON v.owner = u.id
            WHERE v.id = :id",
            ["id" => $id]
        );

        $userInfos = $query->fetch();
        if (!$userInfos) {
            return null;
        } else {
            return $userInfos;
        }
    }
} 

?>