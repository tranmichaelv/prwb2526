<?php

require_once 'model/User.php';
require_once 'model/Item.php';
require_once 'model/Item_pictures.php';
require_once "framework/Controller.php";
require_once "framework/Configuration.php";
require_once "utils/Utils.php";

class ControllerBrowseItems extends Controller {
    public function index() : void {
        $this->browse();
    }

    public function browse() : void {
        $user = $_SESSION['user'];
        // print_r($user);
        // die();
        // $items_other = Item::get_other_items_from_user($this->get_user_or_false()); 
        $items_other = $user->get_other_items();    
        $items_user = $user->get_my_items();
        $all_items = Item::get_all_items();
        // print_r( $_SESSION['user']);
        // die();
        (new View("browseItems"))->show(["items" => $items_other, "items_user" => $items_user, "all_items" => $all_items]);
        // (new View("browseItems"))->show(["items_user" => $items_user]);
    }
}