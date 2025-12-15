<?php 

require_once 'model/Item.php';
require_once "model/User.php";
require_once "model/Picture.php";
require_once "model/Bid.php";

require_once 'framework/View.php';
require_once 'framework/Controller.php';


class ControllerOpenItem extends Controller {


    
    // je m'occupe des items dans je dois vérifier si je dois faire quelque chose avec la DB

    public function index(): void {
        $this->itemDetails();
    }

    public function itemDetails() : void {
        
        $errors = [];
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
        $bid = Bid::get_current_bid_by_item_id($id);
        $seller = Bid::get_seller_information($id);

        $bids_history = Item::get_bids_history($id);

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
            $errors [] = "";
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
            "bids" => $bids_history,
            "errors" => $errors
        ]);

    }
}


?>