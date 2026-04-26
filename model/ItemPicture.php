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

    public function validate() : array {

        $errors = [];

        // item valide et existant
        if ($this->item <= 0){
            $errors[] = " Invalide Item.";
        } else {
            $it = Item::get_by_id($this->item);
            if ($it === null){
                $errors[] = " This item does not exist.";
            }
        }

        // priority > 0
        if ($this->priority <= 0){
            $errors[] = "Priority must be strictly higher than 0.";
        }

        // picture_path valide + fichier existe
        if ($this->picture_path === null || trim($this->picture_path) === "") {
            $errors[] = "Missing image path.";
        } else if (!file_exists($this->picture_path)) {
            $errors[] = "The image file does not exist on the server.";
          }

        if(empty($errors)){
            $stmt = self::execute(
                "SELECT picture_path FROM item_pictures WHERE item = :item AND priority = :p",
                [":item" => $this->item, ":p" => $this->priority]
            );

            $row = $stmt->fetch();

            // si une ligne existe déjà à cette priorité, ce n'est pas autorisé
            if($row){
                $errors[] = "Two photos in the same ad cannot have the same priority.";
            }
        }
        
        return $errors;
    }


    public function persist() : array {
        $errors = $this->validate();
        if (!empty($errors)) return $errors;

        self::execute(
            "INSERT INTO item_pictures(item,priority,picture_path) VALUES (:item, :p, :path)",
            [":item" => $this->item, ":p" => $this->priority, ":path" => $this->picture_path]

        );
        return [];
    }


    public function delete(): void {
        // supprime record
        self::execute("DELETE FROM item_pictures WHERE item = :item AND priority = :p", [
            ":item" => $this->item,
            ":p" => $this->priority
        ]);
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

    public static function get_by_item_and_priority(int $itemId, int $priority) : ?ItemPicture {
        $stmt = self::execute(
            "SELECT * FROM item_pictures WHERE item = :item AND priority = :p",
            [
                ":item" => $itemId, 
                ":p" => $priority
            ]
        );

        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new ItemPicture(
            item: $row["item"],
            priority: $row["priority"],
            picture_path: $row["picture_path"]
        );
    }

    public static function reindex_after_delete(int $itemId, int $deletedPriority): void {
    self::execute(
        "UPDATE item_pictures
         SET priority = priority - 1
         WHERE item = :item
         AND priority > :priority",
        [
            ":item" => $itemId,
            ":priority" => $deletedPriority
        ]
    );
}
}