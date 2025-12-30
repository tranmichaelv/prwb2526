<?php

require_once "framework/Model.php";
require_once "utils/AppTime.php";

class Bids extends Model {
    public function __construct(
        private int $owner, 
        private int $item, 
        private string $created_at, 
        private float $amount
    ) {}
    // -------- Getters --------
     public function getOwner(): int { return $this->owner; }

     public function getItem(): int { return $this->item; }

     public function getCreatedAt(): string { return $this->created_at; }

     public function getAmount(): float { return $this->amount; }

    // -------- Retourne un id du plus grand bidder d'un item ou rien si il n'y en a pas --------
    public static function get_highest_bidder_from_item(int $item) : ?int{
        $query = self::execute("SELECT b.owner
                                FROM v_items_status v 
                                    join bids b on b.item = v.id
                                where v.id = :item
                                order by b.amount desc
                                Limit 1;",["item"=>$item]);
        $result = $query->fetch();
        return $result["owner"] ?? null;
    }

    // public static function get_all_bider_from_item(int $item) : array{
    //     $query = self::execute("SELECT b.owner 
    //                             FROM bids b
    //                                 join items i on b.item = i.id
    //                             where i.id = :item;", ["item"=>$item]);
    //     $result = $query->fetchAll();
    //     return $result;
    // }

}