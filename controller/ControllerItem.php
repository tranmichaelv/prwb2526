<?php

require_once 'model/User.php';
require_once 'model/Item.php';
require_once 'model/Item_pictures.php';
require_once "framework/Controller.php";
require_once "framework/Configuration.php";

class ControllerItem extends Controller {
    public function index() : void {
        $this->browse();
    }

    public function browse() : void {
        $items = Item::get_active_items();     
        $items_user = Item::get_all_bids_item_from_user($this->get_user_or_false()); 
        // print_r( $_SESSION['user']);
        // die();
        (new View("browse"))->show(["items" => $items, "items_user" => $items_user]);
    }

    public static function get_time_left(string $end_date): array{
    
        $endTimestamp = strtotime($end_date);
        $now = strtotime(AppTime::get_current_datetime());  
        
        $diff = $endTimestamp - $now;

        $days = floor($diff / 86400);
        $hours = floor(($diff % 86400) / 3600);
        $minutes = floor(($diff % 3600) / 60);      // reste → minutes
        $seconds = $diff % 60;                      // reste → secondes

        return [$days, $hours, $minutes, $seconds];
    }



}