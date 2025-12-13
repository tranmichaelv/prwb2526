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

    public static function get_hightest_bid_from_item(int $item) : ?string{
        $query = self::execute("SELECT max_bid
                                FROM v_items_status
                                where id = :item;",["item"=>$item]);
        $result = $query->fetch();
        return Utils::convert_format_number_for_bids($result["max_bid"]) ?? null;
    }

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

    public static function is_auction(int $item) : bool{
        $query = self::execute("SELECT is_auction
                                FROM v_items_status v 
                                where v.id = :item;", ["item"=>$item]);
        $result = $query->fetch();
        return intval($result["is_auction"]) === 1;
    }

    public static function has_bids(int $item) : bool{
        $query = self::execute("SELECT has_bids
                                FROM v_items_status v 
                                where v.id = :item;", ["item"=>$item]);
        $result = $query->fetch();
        return intval($result["has_bids"]) === 1;
    }

    public static function has_buy_now(int $item) : bool{
        $query = self::execute("SELECT has_buy_now
                                FROM v_items_status v 
                                where v.id = :item;", ["item"=>$item]);
        $result = $query->fetch();
        return intval($result["has_buy_now"]) === 1;
    }

    public static function is_direct_sale(int $item) : bool{
        $query = self::execute("SELECT is_direct_sale
                                FROM v_items_status v 
                                where v.id = :item;", ["item"=>$item]);
        $result = $query->fetch();
        return intval($result["is_direct_sale"]) === 1;
    }

    public static function all_bider(int $item) : array{
        $query = self::execute("SELECT b.owner 
                                FROM bids b
                                    join items i on b.item = i.id
                                where i.id = :item;", ["item"=>$item]);
        $result = $query->fetchAll();
        return $result;
    }


}