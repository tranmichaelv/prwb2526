<?php

require_once "framework/Model.php";
require_once "utils/AppTime.php";

class Item extends Model {
    public function __construct(public int $id, public string $title, public string $description, public int $owner, public string $created_at,  public int  $duration_days, public string $end_date, public ?float $buy_now_price = null, public ?float $starting_bid = null, public ?string $picture_path = null, public ?string $priority = null, public ?int $number_of_pictures = null) {
        
    }

    public static function get_items() : array {
        $query = self::execute("select * from items order by created_at DESC",[]);
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $end_date = AppTime::get_end_period($row["created_at"], $row["duration_days"]);
            $results[] = new Item($row["id"], $row["title"], $row["description"], $row["owner"],$row["created_at"],$row["duration_days"], $end_date,$row["buy_now_price"],$row["starting_bid"], $row["picture_path"]);
        }
        return $results;
    }

    public static function get_items_and_pictures() : array{
        $query = self::execute("SELECT * FROM items i join item_pictures p on i.id = p.item WHERE p.priority = 1 order by created_at DESC",[]);
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $date = new DateTime($row["created_at"]);
            $end_date = AppTime::get_end_period($row["created_at"], $row["duration_days"]);
            $results[] = new Item($row["id"], $row["title"], $row["description"], $row["owner"],$date->format("Y-m-d H:i:s"),$row["duration_days"], $end_date,$row["buy_now_price"],$row["starting_bid"], $row["picture_path"]);
        }
        return $results;
    }

    // public static function get_item_by_id(int $id) : Item|false {
    //     $query = self::execute("SELECT * FROM items where id = :id", ["id"=>$id]);
    //     $row = $query->fetch();
    //     if ($query->rowCount() == 0) {
    //         return false;
    //     } else {
    //         return new Item($row["id"], $row["title"], $row["description"], $row["owner"],$row["created_at"],$row["duration_days"], $row["end_date"],$row["buy_now_price"],$row["starting_bid"], $row["picture_path"]);
    //     }
    // }

    public static function get_active_items() : array{
        $items = self::get_items_and_pictures();
        $results = [];
        // date actuelle avec offset
        $now = new DateTime(AppTime::get_current_datetime());

        foreach ($items as $item) {
            $end = new DateTime($item->end_date);
    
            if ($end > $now) {
                $results[] = $item; // pas besoin de recréer l'objet
            }
        }
        // foreach ($items as $item) {
        //     echo "created_at = {$item->created_at}<br>";
        //     echo "end_date = {$item->end_date}<br>";
        //     echo "now = " . $now->format("Y-m-d H:i:s") . "<br>";
        //     echo "<hr>";
        // }
        // die();
        return $results;
    }
}