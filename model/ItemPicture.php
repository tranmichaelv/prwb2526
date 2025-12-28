<?php
require_once "framework/Model.php";

class ItemPicture extends Model {


    public function __construct(

        private int $item,
        private int $priority,
        private string $picture_path

    )
    {}

    public function get_item() : int {
        return $this->item;
    }
   
    public function get_priority() : int {
        return $this->priority;
    }

    public function get_picture_path() : string {
        return $this->picture_path;
    }

    public static function get_by_item(int $itemId) : array {
        $stmt = self::execute(
            "SELECT * FROM item_pictures WHERE item = :item ORDER BY priority",
            [":item" => $itemId]
        );
        $rows = $stmt->fetchAll();
        $pics = [];
        foreach($rows as $r) {
            $pics[] = new ItemPicture(
                item: (int)$r["item"],
                priority: (int)$r["priority"],
                picture_path: $r["picture_path"]
            );
        }
        return $pics;
    }

    public static function get_max_priority(int $itemId): int {
        $stmt = self::execute(
            "SELECT COALESCE(MAX(priority),0) AS m FROM item_pictures WHERE item = :item",
             [":item" => $itemId]
        );
        $row = $stmt->fetch();
        return(int)$row["m"];
    }

    public static function swap_priority(int $itemId, int $p1, int $p2): void {

        self::execute("UPDATE item_pictures SET priority = 0 WHERE item = :item AND priority = :p",
        [":item" => $itemId, ":p" => $p1]);
    
        self::execute("UPDATE item_pictures SET priority = :p1 WHERE item = :item AND priority = :p2", 
        [":item" => $itemId, ":p1" => $p1, ":p2" => $p2]);
       
        self::execute("UPDATE item_pictures SET priority = :p2 WHERE item = :item AND priority = 0", 
        [":item" => $itemId, ":p2" => $p2]);
    
    }

}