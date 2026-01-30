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

    // avant de faire une enchère sur un Item vérifier : 
         
        // doit être strictement 
            // supérieur au montant de l'offre la plus élevée déjà faite sur l'annonce <=> amount > max_bid 
            // (ou supérieure à starting_bid si aucune offre n'a encore été faite <=> if has_bids is false -> amount > starting_bid 
            // ou égale à buy_now_price dans le cadre d'un achat immédiat) <=> if buy_now_price is not null -> amount = buy_now_price 

        // created_at doit être une date/heure comprise entre 
        // la date/heure de création de l'annonce (items.created_at) <=> date given to insert >= created_at
        // et sa clôture (durée terminée ou prix d'achat immédiat atteint) <=> date given to insert <= end_at OR buy_now_reached

    

}