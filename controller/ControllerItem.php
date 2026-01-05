<?php

require_once 'model/User.php';
require_once 'model/Item.php';
require_once 'model/Item_pictures.php';
require_once "framework/Controller.php";
require_once "utils/Utils.php";

require_once "model/Picture.php";
require_once "model/ItemSaleStatus.php";
require_once 'framework/View.php';
require_once 'utils/AppTime.php';
class ControllerItem extends Controller {

    private ?ItemSaleStatus $bid = null;

    public function index() : void {
        $this->browse();
    }

    // Page item/browse
    public function browse() : void {
        $user = $this->get_user_or_false();
        $page = "browse";
        // Si user n'est pas vide, connecte au browser de l'user sinon "guest login"
        if($user){
            $items_other = $user->get_my_other_items();    
            $items_user = $user->get_my_bids_items();

            (new View("browse"))->show(["items" => $items_other, "items_user" => $items_user, "user" => $user, "page" => $page]);
        } else{
            $all_items = Item::get_all_items();

            (new View("browse"))->show(["all_items" => $all_items, "user" => $user, "page" => $page]);
        }
    }

    // Page item/myIems
    public function myItems() : void {
        $user = $this->get_user_or_false();

        $page = "myItems";
        $active_items = $user->get_my_items();   
        $closed_unsold_items = $user->get_my_closed_unsold_items();
        $sold_items = $user->get_my_sold_items();

        (new View("list"))->show(["active_items" => $active_items, "closed_unsold_items" => $closed_unsold_items, "sold_items" => $sold_items, "user" => $user, "page" => $page]);
    }

    // Page item/sales
    public function sales() : void {
        $user = $this->get_user_or_false();

        if($user){
        $page = "sales";
        $sales = $user->get_my_sales(); 
        $topPerson = $user->get_my_purchases_top_bidder();  
       
        $totalAndAverage = $user->get_my_sales_total_and_average(); 
        (new View("sales"))->show(["sales" => $sales, "topPerson" => $topPerson, "totalAndAverage"=> $totalAndAverage, "user" => $user, "page" => $page]);
        } else {
            $this->redirect("user", "login");
        }
    }

    // Page item/purchases
    public function purchases() : void {
        $user = $this->get_user_or_false();

        if($user){
            $page = "purchases";
            $purchases = $user->get_my_purchases();   
            $topPerson = $user->get_my_purchases_top_seller();

            $totalAndAverage = $user->get_my_purchases_total_and_average(); 
            (new View("purchases"))->show(["purchases" => $purchases, "topPerson" => $topPerson, "totalAndAverage"=> $totalAndAverage, "user" => $user, "page" => $page]);
        } else {
            $this->redirect("user", "login");
        }
    }

    

    /*------------ Ici le contenur de open Item ----------------*/

    public function open() : void {
        //$this->redirect("openitem");
        //$this->redirect("openitem","index",$_GET["param1"]);
        $this->itemDetails();
    } 

    /*
    public function index(): void {
        $this->itemDetails();
    }
    */

    public function itemDetails(): void {
        //$id = $_GET["param1"] ?? null;
        $id = isset($_GET['param1']) ? (int) $_GET['param1'] : null;
        
        $errors = [];
        //print_r($id);
        //die();
        if ($id == null) {
            $errors[] = "Le controller a besoin d'un id pour charger la vue open item";
        }

        $current_user = $this->get_user_or_redirect();

        //$itemData = $this->loadItemData($id, $current_user);
        $item = Item::get_item_by_id($id);
        //$bidData = $this->loadBidData($id, $current_user);
        $bid = $this->get_bid($id);
        $seller = User::get_users_by_id($item->getOwner());
        // Fusion des erreurs
        //$errors = array_merge($errors, $itemData['errors'], $bidData['errors']);
        //$itemData['errors'] = $errors;
        //$bidData['errors'] = []; // éviter écrasement lors du merge

        
        //(new View("open_item"))->show(array_merge($itemData, $bidData));
        (new View("open_item"))->show(["item" => $item, "bid" => $bid, "seller" => $seller]);
    }

    private function get_bid(int $id): ?ItemSaleStatus {
        if ($this->bid === null) {
            $this->bid = ItemSaleStatus::get_current_bid_by_item_id($id);
        }
        return $this->bid;
    }
    
    private function get_current_time(): ?string {
        return AppTime::get_current_datetime();
    }

    /*========= Chargements des données de l'Item ===========*/
    
    /*private function loadItemData(int $id, ?User $current_user): array {
        $errors = [];

        $item = Item::get_item_by_id($id);
        $main_picture = $item?->get_main_picture_by_item_id();
        $all_pictures = $item?->get_all_pictures_by_item_id();

        if (!$item) $errors[] = "Aucun item retrouvé";
        if (!$main_picture) $errors[] = "Aucune image principale";
        if (!$all_pictures) $errors[] = "Pas d'images retrouvées pour cet item";

        return [
            'item' => $item,
            'main_picture' => $main_picture,
            'pictures' => $all_pictures,
            'user' => $current_user,
            'errors' => $errors
        ];
    }*/

    /*======== Chargement des données de l'enchère =========*/
    /*
    private function loadBidData(int $id, ?User $current_user): array {
        $errors = [];
        $bid = $this->get_bid($id);

        if (!$bid) {
            $errors[] = "Aucune enchère pour le moment";
        }

        $seller = $bid?->get_seller_information();
        if (!$seller) $errors[] = "Aucune information sur le vendeur";

        $bids_history = $bid?->get_bids_history();
        if (!$bids_history) $errors[] = "Problème avec l'historique des enchères";

        return [
            'current_bid' => $bid?->getCurrentBid(),
            'starting_bid' => $bid?->getStartingBid(),
            'bid_is_closed' => $bid?->bid_is_closed() ?? true,
            'user_is_owner' => $current_user ? $bid?->user_is_owner($current_user) : false,
            'has_buy_now' => $bid?->buy_now_price_exist() ?? false,
            'has_bids' => $bid?->has_bids() ?? false,
            'buy_now_reached' => $bid?->buy_now_reached() ?? false,
            'is_direct_sale' => $bid?->is_direct_sale() ?? false,
            'seller' => $seller,
            'bids_history' => $bids_history,
            'errors' => $errors
        ];
    } */

}