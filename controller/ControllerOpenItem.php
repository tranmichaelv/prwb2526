<?php 

require_once 'model/Item.php';
require_once "model/User.php";
require_once "model/Picture.php";
require_once "model/Bid.php";

require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'utils/AppTime.php';


class ControllerOpenItem extends Controller {


    private ?Bid $bid;
    public ?float $end_at;
    
    // je m'occupe des items dans je dois vérifier si je dois faire quelque chose avec la DB

    public function index(): void {
        $this->itemDetails();
    }

    public function itemDetails() : void {
        
        $errors = [];
        $bid_is_closed = false;
        $connected_user_is_owner = false;
        $has_buy_now = false; 
        $has_bids = false; 
        $buy_now_reached = false; 

        $id = $_GET["param1"] ?? null;
        if (isset($_GET["param1"]) && $_GET["param1"] != "") {
            $id = $_GET["param1"];
        } else {
            //die("Le controller a besoin d'un id pour charger la vue open item");
            $errors [] = "Le controller a besoin d'un id pour charger la vue open item"; 
        }
        

        $item = Item::get_item_by_id($id);
        $main_picture = Item::get_main_picture_by_item_id($id);
        $all_pictures = Item::get_all_pictures_by_item_id($id);
        $seller = Bid::get_seller_information($id);
        $bids_history = Bid::get_bids_history($id);
        $bid = Bid::get_current_bid_by_item_id($id);
        
        // pour tester je mets now sur une date inf a la fin de l'enchère
        //$now = AppTime::get_current_datetime();

        
        // Différents formes du conteneur des enchères : 

        // Annonce ouverte, 
        // utilisateur non créateur,
        // pas de prix d'achat immédiat 
        // pas encore d'enchère :


        // Annonce ouverte ou pas 
        $bid_is_closed = $this->is_closed_bid($id);
        /*
        if ($bid !== null && $bid->end_at !== null) {
            $end_at = new DateTime($bid->end_at);
            $bid_is_closed = $now > $end_at;
        } 
        else {
            $end_at = null;
            $bid_is_closed = true; // aucune enchère donc fermée 
            $errors [] = "Aucune enchère pour le moment";
        }
        */

        // Utilisateur créateur ou non 
        $connected_user_is_owner = $this->user_is_owner($id);
        /*
        $connected_user = $this->get_user_or_false();
        if ($connected_user && $seller) {
            $connected_user_is_owner = $connected_user->id == $seller['id'];
        }
        */
        
        // Prix d'achat immédiat ou pas 
        $has_buy_now = $this->buy_now_price_exist($id);
        /*
        if ($bid !== null && $bid->has_buy_now) {
            $has_buy_now = true;
        }
        */
        
        // Enchères existantes ou pas 
        $has_bids = $this->has_bids($id);
        /*
         if ($bid !== null && $bid->has_bids) {
            $has_bids = true;
        }
        */
       
        $buy_now_reached = $this->buy_now_reached($id);


        $is_direct_sale = $this->is_direct_sale($id);


        // pour tester car j'ai pas encore inétgré le login 




        if ($item == null) {
            $errors [] = "Aucune Item retrouvé ";
        } 
        else if ($main_picture == null) {
            $errors [] = "Aucune image principale" ; 
        }
        else if ($all_pictures == null) {
            $errors [] = "Pas d'images retrouvées pour cet Item " ; 
        }
        else if ($bid == null) {
            $errors [] = "Aucune enchère pour le moment";
        } else if (!$seller) {
            $errors [] = "Aucune inforamtion sur le vendeur de cet Item";
        } else if ($bids_history == null) {
            $errors [] = "problème avec l'historique des enchères ";
        } 
    

        
        // dans le cas ou j'avais plusieurs model 
        //$owner = $item->owner();
        //$pictures = $item->pictures();
        //$bids = $item->bids();

        (new View("open_item")) -> show([
            "item" => $item,
            "main_picture" => $main_picture,
            "pictures" => $all_pictures, 
            "current_bid" => $bid,
            "seller" => $seller,
            "bids_history" => $bids_history,
            "bid_is_closed" => $bid_is_closed,
            "connected_user_is_owner" => $connected_user_is_owner,
            "has_buy_now" => $has_buy_now,
            "has_bids" => $has_bids,
            "buy_now_reached" => $buy_now_reached,
            "is_direct_sale" => $is_direct_sale,
            "errors" => $errors
        ]);

    }


    public function get_bid($id) : ?Bid {

        if ($this->bid === null) {
            $this->bid = Bid::get_current_bid_by_item_id($id);
        }

        return $this->bid;
    }

    public function get_current_time() : ?string {
        return AppTime::get_current_datetime();
    }


    public function is_closed_bid($id) : bool {

        $bid = $this->get_bid($id);
        $now = $this->get_current_time();

        if ($bid !== null && $bid->end_at !== null) {
            $end_at = new DateTime($bid->end_at);
            return $now > $end_at;
        } 
        else {
            $end_at = null;
            return true; // aucune enchère donc fermée 
            $errors [] = "Aucune enchère pour le moment";
        }
        
    }

    public function user_is_owner($id) : bool {

        $seller = Bid::get_seller_information($id);
        $connected_user = $this->get_user_or_false();

        if ($connected_user && $seller) {
            return $connected_user->id == $seller['id'];
        } 
        return false;
    }

    public function buy_now_price_exist($id) : bool {
        
        //$bid = Bid::get_current_bid_by_item_id($id);
        $bid = $this->get_bid($id);
        if ($bid !== null && $bid->has_buy_now) {
            return true;
        }
        return false;
    }

    public function has_bids($id) : bool {
        
        //$bid = Bid::get_current_bid_by_item_id($id);
        $bid = $this->get_bid($id);
        if ($bid !== null && $bid->has_bids) {
            return true;
        }
        return false;
    } 

    public function buy_now_reached($id) : bool {

        //$bid = Bid::get_current_bid_by_item_id($id);
        $bid = $this->get_bid($id);
        if ($bid !== null && $bid->buy_now_reached) {
            return true;
        }
        return false;
    }

    public function is_direct_sale($id) : bool {

        //$bid = Bid::get_current_bid_by_item_id($id);
        $bid = $this->get_bid($id);
        if ($bid !== null && $bid->is_direct_sale) {
            return true;
        }
        return false;
    }

}


?>