<?php

require_once 'model/User.php';
require_once 'model/Item.php';
require_once 'model/Item_pictures.php';
require_once "framework/Controller.php";
require_once "utils/Utils.php";

class ControllerBrowseItems extends Controller {
    public function index() : void {
        $this->browse();
    }

    public function browse() : void {
        $user = $_SESSION['user'];
        // print_r($user);
        // die();
        if($user->id != 0){
            $items_other = $user->get_other_items();    
            $items_user = $user->get_my_items();
            (new View("browseItems"))->show(["items" => $items_other, "items_user" => $items_user]);
        } else{
            $all_items = Item::get_all_items();
            (new View("browseItems"))->show(["all_items" => $all_items]);
        }
    }
}