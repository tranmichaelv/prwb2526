<?php 

/*
require_once "framework/Model.php";

class Picture extends Model {


    // Les méthodes qui gèrent les images d'un Item 

    // .....


    public int $id;
    public string $url;


    public static function get_pictures_by_item_id($id) : array {
        // requette pour recup que les images asscocié a this qui l'item courant 
        $query = self::execute(
            "SELECT * 
            FROM item_pictures as ip
            WHERE ip.item = :id
            ORDER BY ip.priority ASC",
            ['id' => $id]
        );

        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        if ($query->rowCount() == 0) {
            die ("Acune image pour cet Item !!");
        } else {
            return $results;
        }
        
    }
}
    */
?>