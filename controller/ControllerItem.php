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
        (new View("browse"))->show(["items" => $items]);
    }

    public static function get_time_left(string $end_date): array{
    
        $endTimestamp = strtotime($end_date);
        $now = strtotime(AppTime::get_current_datetime());  
        
        $diff = $endTimestamp - $now;

        $days = floor($diff / 86400);
        $hours = floor(($diff % 86400) / 3600);
        return [$days, $hours];
    }



}