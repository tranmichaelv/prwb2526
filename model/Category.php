<?php

require_once "framework/Model.php";
require_once "framework/Configuration.php";
require_once "utils/AppTime.php";


class Category extends Model {

    public function __construct(
        private ?int $id,
        private string $name,
        private int $priority 
    ) {}

    public function get_id() : ?int {
        return $this->id;
    }

    public function get_name() : string {
        return $this->name;
    }

    public function get_priority() : int {
        return $this->priority;
    }

    public function set_id(int $id) : void {
        $this->id = $id;
    }

    public function set_name(string $name) : void {
        $this->name = $name;
    }

    public function set_priority(int $priority) : void {
        $this->priority = $priority;
    }

    public function validate() : array {
        $errors = [];

        $name =  trim($this->name);
        $priority = $this->priority;

        $category_min_length = Configuration::get("category_min_length");
        $category_max_length = Configuration::get("category_max_length");

        if(mb_strlen($name) < $category_min_length || mb_strlen($name) > $category_max_length) {
            $errors['category_name'][] = "Category length must be between  $category_min_length and  $category_max_length characters";
        }

        if(self::category_exists_already($name, $this->id)) {
            $errors['category_name'][] = "This category already exists ";
        }

        if ($this->priority <= 0){
            $errors[] = "Priority must be strictly higher than 0.";
        }

        if(self::priority_exists_already($priority, $this->id)) {
            $errors[] = "Priority must be unique";
        }
        return $errors;
    }

    public function persist() : array {
        $errors = $this->validate();

        if(!empty($errors)) {
            return $errors;
        }
        if($this->id == null) {
            $sql = "INSERT INTO categories
                        (name, priority)
                    VALUES
                        (:name, :priority)";
            self::execute($sql,[
                ":name" => $this->name,
                ":priority" => $this->priority,
            ]);

            $this->id = self::lastInsertId();
        } else {
            $sql = "UPDATE categories SET
                        name = :name,
                        priority = :priority
                    WHERE id = :id";
            self::execute($sql,[
                ":name" => $this->name,
                ":priority" => $this->priority,
                ":id" => $this->id,
            ]);        
        }

        return [];
    }

    public function delete(): array {

        if ($this->is_used()) {
            return ["This category is used by one or more items and cannot be deleted."];
        }

        $oldPriority = $this->priority;

        self::execute(
            "DELETE FROM categories WHERE id = :id",
            [":id" => $this->id]
        );

        self::execute(
            "UPDATE categories
            SET priority = priority - 1
            WHERE priority > :priority",
            [":priority" => $oldPriority]
        );

        return [];
    }


    private static function category_exists_already(string $name, ?int $id = null) : bool  {
        $sql = "SELECT count(*) FROM categories WHERE name = :name AND id != :id";

        $stmt = self::execute($sql,[
            ":name" => $name,
            ":id" => $id ?? 0
        ]);

        return $stmt->fetchColumn() > 0;

    }

    private static function priority_exists_already(int $priority, ? int $id = null) : bool {
        $sql = "SELECT count(*) FROM categories WHERE priority = :priority AND id != :id";

        $stmt = self::execute($sql,[
            ":priority" => $priority,
            ":id" => $id ?? 0
        ]);

        return $stmt->fetchColumn() > 0;
    }

    public static function get_all_categories(): array {

        $sql = "SELECT id, name, priority
                FROM categories
                ORDER BY priority";

        $stmt = self::execute($sql, []);

        $rows = $stmt->fetchAll();

        $categories = [];

        foreach ($rows as $row) {
            $categories[] = new Category(
                id: $row["id"],
                name: $row["name"],
                priority:$row["priority"]
            );
        }

        return $categories;
    }

    public static function get_by_id(int $id): ?Category {

        $sql = "SELECT id, name, priority
                FROM categories
                WHERE id = :id";

        $stmt = self::execute($sql, [
            ":id" => $id
        ]);

        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new Category(
            id: $row["id"],
            name: $row["name"],
            priority: $row["priority"]
        );
    }

    public static function swap_priority(int $p1, int $p2) : void {

        self::execute("UPDATE categories SET priority = 0 WHERE priority = :p1",[":p1" => $p1]);

        self::execute("UPDATE categories SET priority = :p1 WHERE priority = :p2",[":p1" => $p1, ":p2" => $p2]);

        self::execute("UPDATE categories SET priority = :p2 WHERE priority = 0",[":p2" => $p2]);

    }

    public static function get_max_priority(): int {
            $stmt = self::execute( "SELECT COALESCE(MAX(priority),0) AS max_priority FROM categories", []);
                
            $row = $stmt->fetch();

            return $row["max_priority"];
    }

    public function is_used() : bool {
        return $this->count_items() > 0;
    }

    public function count_items() : int {
         $sql = "SELECT COUNT(*) FROM item_categories WHERE category = :id";

        $stmt = self::execute($sql,[":id" => $this->id]);

        return $stmt->fetchColumn() > 0;
    }


    public function to_array() : array {
       $count = $this->count_items();
       
       return [
        "id" => $this->id,
        "name" => $this->name,
        "priority" => $this->priority,
        "count" => $count,
        "can_delete" => $count === 0
       ];
    }

    public static function get_all_categories_as_array() : array {
        $categories = self::get_all_categories();
        $result = [];

        foreach($categories as $category) {
            $result[] = $category->to_array();
        }

        return $result;
    }

    public static function update_order( array $ids): void {
        $newPriority = 1;

        foreach ($ids as $id) {
            self::execute(
                "UPDATE categories
                SET priority = :temp
                WHERE id = :id",
                [
                    ":temp" => -$newPriority,
                    ":id" => (int)$id
                ]
            );

            $newPriority++;
        }

         self::execute(
            "UPDATE categories
            SET priority = -priority
            WHERE priority < 0", []
        );
    }
}