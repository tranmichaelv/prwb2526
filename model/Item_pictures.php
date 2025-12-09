<?php

require_once "framework/Model.php";
require_once "utils/AppTime.php";

class Item_pictures extends Model {
    public function __construct(int $owner, int $priority, string $picture_path){

    }

    public static function get_all_pictures_by_item(int $id) : array {
        $query = self::execute("select * from item_pictures where item =:id",["id"=>$id]);
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $results[] = new Item_pictures($row["item"],$row["priority"],$row["picture_path"]);
        }
        return $results;
    }

    public static function get_header_path_by_item(int $id) : string{
        $query = self::execute("select * from item_pictures where item =:id and priority = 1",["id"=>$id]);
        $data = $query->fetch();
        return $data;
    }


}