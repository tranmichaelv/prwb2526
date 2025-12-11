<?php

require_once "framework/Model.php";
require_once "utils/AppTime.php";

class Bids extends Model {
    public function __construct(private int $owner, private int $item, private string $created_at, private float $amount){

    }
     // -------- Getters --------
     public function getOwner(): int { return $this->owner; }

     public function getItem(): int { return $this->item; }

     public function getCreatedAt(): string { return $this->created_at; }

     public function getAmount(): float { return $this->amount; }

    // -------- Retourne un array du plus grand bid d'un item ou rien si il n'y en a pas --------
    public static function get_highest_bids_from_item(int $item) : ?Bids{
        $query = self::execute("SELECT *
                                FROM bids b
                                WHERE b.item = :item
                                ORDER BY b.amount DESC
                                limit 1;",["item"=>$item]);
        $result = $query->fetch();
        if (!$result) { return null; }
        return new Bids($result["owner"], $result["item"], $result["item"], $result["amount"]);
    }

}