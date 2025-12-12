<?php 

require_once "framework/Model.php";


class Bid extends Model {

    // tout les méthodes dont j'aurai besoin pour gérer l'enchère 
    // .....


    // récup les enchères fait sur cet item 
    public static function get_bids_by_item_id($id) : array | null {

        $query = self::execute(
            "SELECT * 
            FROM bids 
            WHERE item = :id", 
            ["id" => $id]
        );

        $results = $query->fetchAll(PDO::FETCH_ASSOC);

        if ($query->rowCount() == 0) {
            return null;
        } else {
            return $results;
        }
    }
} 

?>