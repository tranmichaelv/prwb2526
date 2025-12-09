<?php

require_once "framework/Model.php";
require_once "utils/AppTime.php";

class Bids extends Model {
    public function __construct(public int $owner, public int $item, public string $created_at, public float $amount){

    }

    public static function get_all_bids_by_user(int $owner) : array {
        $query = self::execute("SELECT b.*
                                FROM bids b
                                JOIN (
                                    SELECT item, MAX(amount) AS max_amount
                                    FROM bids
                                    WHERE owner = :owner
                                    GROUP BY item
                                    ) x ON b.item = x.item AND b.amount = x.max_amount
                                WHERE b.owner = :owner"
                                , ["owner" => $owner]);
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $results[] = new Bids($row["owner"],$row["item"],$row["created_at"], $row["amount"]);
        }
        return $results;
    }

}