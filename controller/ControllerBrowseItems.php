<?php

require_once 'model/User.php';
require_once 'model/Item.php';
require_once 'model/Item_pictures.php';
require_once "framework/Controller.php";
require_once "framework/Configuration.php";

class ControllerBrowseItems extends Controller {
    public function index() : void {
        $this->browse();
    }

    public function browse() : void {
        // $user = $_SESSION['user'];
        // print_r($user);
        // die();
        $items_other = Item::get_other_items_from_user($this->get_user_or_false());     
        $items_user = Item::get_bids_items_from_user($this->get_user_or_false()); 
        // print_r( $_SESSION['user']);
        // die();
        (new View("browseItems"))->show(["items" => $items_other, "items_user" => $items_user]);
        // (new View("browseItems"))->show(["items_user" => $items_user]);
    }

    // -------- Temps restant avant la fin de l'annonce --------
    public static function get_time_left(string $end_date): array{
        
        $endTimestamp = strtotime($end_date);
        $now = strtotime(AppTime::get_current_datetime());  
        
        $diff = $endTimestamp - $now;

        $days = floor($diff / 86400);
        $hours = floor(($diff % 86400) / 3600);    
        $minutes = floor(($diff % 3600) / 60);     
        $seconds = $diff % 60;                     

        return [$days, $hours, $minutes, $seconds];
    }

    // -------- Retourne le plus grand bid d'un item ou rien --------
    public static function highest_bid(int $item) : ?array{
        // $user = $_SESSION['user'];
        return Bids::get_highest_bids_from_item($item);
    }

    // -------- Formate un prix de façon 00,00 --------
    public static function convert_format_number_for_bids(?float $amount) : string{
        if ($amount === null) {
            return " ";  
        }
        return number_format($amount,2,',','');
    }





}