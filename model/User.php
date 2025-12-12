<?php

require_once "framework/Model.php";

class User extends Model {

    // Toutes les méthodes pour gérer les utilisateurs
    // y compris les propriétaires des items et les autres users qui ont fait des enchères 
    // ....

     public function __construct(
        public int $id,
        public string $name,
        public string $email
    ) {}

    // return un user, donc je dois avoir un model Owner,
    // je dois créer un model User ou Owner parce que je gère le owner tout seul dans une partie 
    public static function get_owner_by_item_id($id) : User {

        $query = self::execute(
            "SELECT * 
            FROM v_items_status as vs JOIN users as u on u.id = vs.owner
            WHERE vs.id = :id", ['id' => $id]);

        $owner = $query->fetch(); // recup un owner

        if ($query->rowCount() == 0){
            die ("User non trouvé !!!!!");
        } else {
            return new User(
                $owner["id"], 
                $owner["full_name"],
                $owner["email"]
            );
        }
    }
}

?>