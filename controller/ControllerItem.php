<?php

require_once 'model/User.php';
require_once 'model/Item.php';
require_once 'model/Item_pictures.php';
require_once "framework/Controller.php";
require_once "utils/Utils.php";

class ControllerItem extends Controller {
    public function index() : void {
        $this->browse();
    }

    // Page item/browse
    public function browse() : void {
        $user = $this->get_user_or_false();
        
        // Si user n'est pas vide, connecte au browser de l'user sinon "guest login"
        if($user){
            $items_other = $user->get_my_other_items();    
            $items_user = $user->get_my_bids_items();

            (new View("browse"))->show(["items" => $items_other, "items_user" => $items_user, "user" => $user]);
        } else{
            $all_items = Item::get_all_items();

            (new View("browse"))->show(["all_items" => $all_items, "user" => $user]);
        }
    }

     // Page item/myIems
    public function myItems() : void {
        $user = $this->get_user_or_false();

        $active_items = $user->get_my_items();   
        $closed_unsold_items = $user->get_my_closed_unsold_items();
        $sold_items = $user->get_my_sold_items();

        (new View("list"))->show(["active_items" => $active_items, "closed_unsold_items" => $closed_unsold_items, "sold_items" => $sold_items, "user" => $user]);
    }
}