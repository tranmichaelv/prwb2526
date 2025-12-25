<?php 

require_once "framework/Model.php";

class Item extends Model {

    public function __construct(
        public int $id,
        public string $title, 
        public string $description,
        public int $owner, 
        public string $created_at,
        public ?string $buy_now_price,
        public int $duration_days,
        public float $starting_bid,
        public string $end_at,
        public int $bid_count,
        public ?int $max_bid,
        public bool $is_direct_sale,
        public bool $is_auction,
        public bool $has_buy_now,
        public bool $has_bids,
        public bool $has_now_reached,
        public bool $not_purchased_direct_sale,

        public ?string $main_picture = null,
        public array $pictures =  []
    ){}


    // static et dois retourner un objet de type item
    // utiliser la vue au lieu de la table pour récup le max d'infos sur un item

    public static function get_item_by_id($id) : ?Item {
        
        $error = [];
        $query = self::execute("SELECT * FROM v_items_status WHERE id = :id", 
        ['id' => $id]
        );
        $Item = $query->fetch(); 

        if ($query->rowCount() == 0){
            return null;
        } else {
            return new Item(
                $Item["id"], 
                $Item["title"], 
                $Item["description"],
                $Item["owner"], // je dois peut etre l'enlever car je gère ca dans User et je renvoi juste le owner comme un objets indépendant  
                $Item["created_at"],
                $Item["buy_now_price"],
                $Item["duration_days"], 
                $Item["starting_bid"],
                $Item["end_at"],
                $Item["bid_count"],
                $Item["max_bid"], 
                $Item["is_direct_sale"], 
                $Item["is_auction"],
                $Item["has_buy_now"],
                $Item["has_bids"], 
                $Item["buy_now_reached"],
                $Item["not_purchased_direct_sale"] 
            );
        }
    }


    // faire plusieurs model ou faire une seul model Item et définir toute les méthodes nécéssaires 

    // d'abord gérer les images, recup que l'image principal et plus le reste 

    public static function get_main_picture_by_item_id($id) : ?string {
        
        $query = self::execute(

            "SELECT ip.picture_path
            FROM item_pictures ip 
            WHERE ip.item = :id 
            ORDER BY ip.priority ASC", 

            ['id' => $id]
        );

        $results = $query->fetch();

        if ($query->rowCount() == 0) {
            $error [] = "Acune image pour cet Item !!"; // tmp pas sur d'avoir besoin de cette erreur
            return null;
        } else {
            return $results["picture_path"];
        }
    }


    // recup toutes les images 
    public static function get_all_pictures_by_item_id($id) : ?array {
        
        $query = self::execute(
            "SELECT ip.picture_path
            FROM item_pictures as ip
            WHERE ip.item = :id
            ORDER BY ip.priority ASC",
            ['id' => $id]
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