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

}