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

public function delete() : array {

    $sql = "SELECT COUNT(*) FROM item_categories  WHERE category = :id";
            
    $stmt = self::execute($sql, [
        ":id" => $this->id
    ]);

    $count = $stmt->fetchColumn();

    if ($count > 0) {
        return [];
    }

    self::execute(
        "DELETE FROM categories WHERE id = :id",
        [":id" => $this->id]
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

}