<?php

require_once "framework/Model.php";
require_once "utils/AppTime.php";

class Bids extends Model {
    public function __construct(private int $owner, private int $item, private string $created_at, private float $amount){

    }

    // -------- Retourne un array du plus grand bid d'un item ou rien si il n'y en a pas --------
    public static function get_highest_bids_from_item(int $item) : ?array{
        $query = self::execute("SELECT *
                                FROM bids b
                                WHERE b.item = :item
                                ORDER BY b.amount DESC
                                limit 1;",["item"=>$item]);
        $result = $query->fetch();
        return $result ?: null;
    }

    // public static function get_highest_bidder_ID(int $item) : int {
    //     return self::get_highest_bids_from_item($item)['owner'];
    // }

    // public static function get_highest_bid_amount(int $item) : int {
    //     return self::get_highest_bids_from_item($item)['amount'];
    // }

    // public static function get_all_bids_by_user(int $owner) : array {
    //     $query = self::execute("SELECT b.*
    //                             FROM bids b
    //                             JOIN (
    //                                 SELECT item, MAX(amount) AS max_amount
    //                                 FROM bids
    //                                 WHERE owner = :owner
    //                                 GROUP BY item
    //                                 ) x ON b.item = x.item AND b.amount = x.max_amount
    //                             WHERE b.owner = :owner"
    //                             , ["owner" => $owner]);
    //     $data = $query->fetchAll();
    //     $results = [];
    //     foreach ($data as $row) {
    //         $results[] = new Bids($row["owner"],$row["item"],$row["created_at"], $row["amount"]);
    //     }
    //     return $results;
    // }

}