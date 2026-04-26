<?php
require_once "framework/Controller.php";
require_once "model/Item.php";
require_once "model/ItemPicture.php";
require_once "utils/ImageManager.php";
require_once 'model/User.php';
require_once "utils/Utils.php";
require_once "model/ItemStatus.php";
require_once "utils/Search_criteria.php";

class ControllerItem extends Controller {

    public function index() : void {
        $this->browse();
    }

    // public function index() : void {
    //     session_destroy();
    //     session_start();
    //     echo("ok");
    // }

    public function get_item_pictures_service() : void {
        $user = $this->get_user_or_false();
        if (!$user) {
            http_response_code(401);
            echo json_encode(["error" => "Unauthorized"]);
            return;

        }

        $id = $_GET["param1"] ?? null;
        if($id === null || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(["error" => "Bad request"]);
            return;
        }

        $item = Item::get_by_id($id);
        if($item === null) {
            http_response_code(404);
            echo json_encode(["error" => "Item not found"]);
            return;
        }

        $pictures = ItemPicture::get_by_item($id);

        $result = [];

        foreach($pictures as $index => $picture) {
            $result[] = [
                "id" => $picture->get_item(),
                "src" => $picture->get_picture_path(),
                "thumbnail_src" => preg_replace('/\.jpg$/', '_thumbnail.jpg', $picture->get_picture_path()),
                "position" => $index
            ];
        }

        echo json_encode([
            "pictures" => $result
        ]);

    }

     // Page item/browse
    public function browse() : void {
        $user = $this->get_user_or_false();
        $page = "browse";
        $encoded = isset($_GET['param1'] ) ? $_GET['param1'] : null;
        $query = $encoded ? Search_criteria::url_safe_decode($encoded) : "";

        // Si user n'est pas vide, connecte au browser de l'user 
        if($user){
                    $items_other = $user->get_my_other_items();    
                    $items_user = $user->get_my_bids_items();

                    (new View("browse"))->show(["items" => $items_other, 
                                                "items_user" => $items_user, 
                                                "user" => $user, 
                                                "page" => $page,
                                                "query" => $query
                                            ]);
        } 
        // sinon se connecte en tant que guest 
        else{
                $all_items = ItemStatus::get_all_items();

                (new View("browse"))->show(["all_items" => $all_items, 
                                            "user" => $user, 
                                            "page" => $page,
                                            "encoded" => $query
                                        ]);
        }
    }

    // public function get_filtered_items() {

    //     $query = trim($_POST["query"] ?? "");
    //     $user = $this->get_user_or_false();
    //     $from = 'from-browse';
    //     $page = "browse";
    
    //     if ($query === "") {
    
    //         $this->browse();
    
    //     } else {
    
    //         if ($user) {

    //             $items_bids = $user->get_my_bids_items_filtered($query);
    //             $items_other = $user->get_my_other_items_filtered($query);
            
    //             if (count($items_bids) > 0) {
    //                 echo "<div class='category-items'>";
    //                 echo "<h1>Items I'm Participating in</h1>";
    //                 echo "<div class='items-box'>";
            
    //                 foreach ($items_bids as $item) {
    //                     include "view/browse.php";
    //                 }
            
    //                 echo "</div></div>";
    //             }
            
    //             if (count($items_other) > 0) {
    //                 echo "<div class='category-items'>";
    //                 echo "<h1>Other Available Items</h1>";
    //                 echo "<div class='items-box'>";
            
    //                 foreach ($items_other as $item) {
    //                     include "view/browse.php";
    //                 }
            
    //                 echo "</div></div>";
    //             }
            
    //         } else {
            
    //             $items = ItemStatus::get_all_items_filtered($query);
            
    //             if (count($items) > 0) {
    //                 echo "<div class='category-items'>";
    //                 echo "<h1>Available Items</h1>";
    //                 echo "<div class='items-box'>";
            
    //                 foreach ($items as $item) {
    //                     include "view/browse.php";
    //                 }
            
    //                 echo "</div></div>";
    //             }
    //         }
            
    //     }
    // }

    // public function get_filtered_items() : void {

    //     $query = trim($_POST["query"] ?? "");
    //     $user = $this->get_user_or_false();
    //     $from = 'from-browse';
    //     $page = "browse";
    
    //     if ($query === "") {
    
    //         $this->browse();
    
    //     } else {
    
    //         if ($user) {

    //             $items_bids = $user->get_my_bids_items_filtered_as_json($query);
    //             $items_other = $user->get_my_other_items_filtered_as_json($query);
            
            
    //         } else {
    //             $items = ItemStatus::get_all_items_filtered_as_json($query);
            
    //         }
    //     }
    // }

    public function get_filtered_items_browse() : void {
        $query = trim($_POST["query"] ?? "");
        $user = $this->get_user_or_false();
        $items_json = $user->get_browse_items_filtered_as_json($query);
        echo $items_json;
    }

    public function get_filtered_items_list() : void {
        $query = trim($_POST["query"] ?? "");
        $user = $this->get_user_or_false();
        $items_json = $user->get_my_items_filtered_as_json($query);
        echo $items_json;
    }

    public function render_items() {

        $items = $_POST['items'] ?? [];
        $encoded = $_POST['encoded'] ?? null;
        $page = $_POST['page'] ?? null;
        $user = $this->get_user_or_false();
    
        if($page === "browse"){
            $page = "browse";
            $from = "from-browse";
            $title = "Browser";
        } else {
            $page = "list";
            $from = "from-items";
            $title = "My items";
        }
    
    
        foreach ($items as $data) {
            $item = ItemStatus::get_item_by_id($data['id']);
            include "view/browse.php";
        }
    }

    // Page item/myIems
    public function list() : void {
        // doit être connecté pour voir la page sinon redirect
        $user = $this->get_user_or_redirect();
        $page = "list";
        $encoded = isset($_GET['param1'] ) ? $_GET['param1'] : null;
        $query = $encoded ? Search_criteria::url_safe_decode($encoded) : "";

        $active_items = $user->get_list();  
        // print_r($active_items);
        // die();
        $closed_unsold_items = $user->get_my_closed_unsold_items();
        $sold_items = $user->get_my_sold_items();

        (new View("list"))->show(["active_items" => $active_items, 
                                  "closed_unsold_items" => $closed_unsold_items, 
                                  "sold_items" => $sold_items, 
                                  "user" => $user, 
                                  "page" => $page,
                                  "query" => $query
                                ]);
        
    }

    // Page item/sales
    public function sales() : void {
        // doit être connecté pour voir la page sinon redirect
        $user = $this->get_user_or_redirect();
        $page = "sales";
        
        $sales = $user->get_my_sales(); 
        $top_person = $user->get_my_purchases_top_bidder();  
        $total_and_average = $user->get_my_sales_total_and_average(); 

        (new View("sales"))->show(["sales" => $sales, 
                                "top_person" => $top_person, 
                                "total_and_average"=> $total_and_average, 
                                "user" => $user, 
                                "page" => $page
                                ]);
        
    }

    // Page item/purchases
    public function purchases() : void {
        // doit être connecté pour voir la page sinon redirect
        $user = $this->get_user_or_redirect();
        $page = "purchases";

        $purchases = $user->get_my_purchases();   
        $top_person = $user->get_my_purchases_top_seller();
        $total_and_average = $user->get_my_purchases_total_and_average(); 

        (new View("purchases"))->show(["purchases" => $purchases, 
                                        "top_person" => $top_person, 
                                        "total_and_average"=> $total_and_average, 
                                        "user" => $user, 
                                        "page" => $page
                                    ]);

    }

    public function add() : void {
        $user = $this->get_user_or_redirect();

        $errors = [];
        $owner = $user->getId();

        $title = $_POST['title'] ?? "";
        $description = $_POST['description'] ?? "";
        $duration = isset($_POST['duration']) ? (int)$_POST['duration'] : 7;

        $bid_raw = $_POST['bid'] ?? null;
        $starting_bid = ($bid_raw === '') ? null : (float)$bid_raw;

        $buy_raw = $_POST['buy_now'] ?? '';
        $instant = ($buy_raw === '') ? null : (float)$buy_raw;

        $sale_raw = $_POST['sale_price'] ?? '';
        $sale = ($sale_raw === '') ? null : (float)$sale_raw;          

        $item = new Item(
            id: null,
            title: $title,
            description: $description,
            owner: $owner,
            duration_days: $duration,
            starting_bid: $starting_bid,
            buy_now_price: null // calculer dans validate()
        );

            $item->set_instant_purchase($instant);
            $item->set_sale_price($sale);

        if (!empty($_POST)) {
            $errors = $item->persist(); // validation + insert tentative

            if (empty($errors)) {
                // redirection vers Open Item
                // plus tard
                $this->redirect("item","open", $item->get_id(), "from-items");
            }
        }

        (new View("add_edit_item"))->show([
            "mode" => "add",         
            "errors" => $errors,
            "item" => $item,
            "user" => $user
        ]);

    }

    public function edit() : void {
        $user = $this->get_user_or_redirect();

        $id = $_GET['param1'] ?? null;
        $encoded = $_GET['param2'] ?? null;

        if ($id === null || !is_numeric($id)) {
            $this->redirect("item");
        }

        $item = Item::get_by_id((int)$id);
        if ($item === null) {
            $this->redirect("item");
        }

        $errors = [];

        if(!empty($_POST)){
            $item->set_title($_POST['title'] ?? "");
             $item->set_description($_POST['description'] ?? "");
            $item->set_duration_days(isset($_POST['duration']) ? (int)$_POST['duration'] : 7);

            $bid_raw = $_POST['bid'] ?? '';
            $starting_bid = ($bid_raw === '') ? null : (float)$bid_raw;
            $item->set_starting_bid($starting_bid);

            $buy_raw = $_POST['buy_now'] ?? '';
            $instant = ($buy_raw === '') ? null : (float)$buy_raw;
            $item->set_instant_purchase($instant);

            $sale_raw = $_POST['sale_price'] ?? '';
            $sale = ($sale_raw === '') ? null : (float)$sale_raw;
            $item->set_sale_price($sale);

            $errors = $item->persist();

        if (empty($errors)) {
            $this->redirect("item","open",$item->get_id(), "from-items");
        }
    }

        (new View("add_edit_item"))->show([
            "mode" => "edit",
            "errors" => $errors,
            "item" => $item,
            "id" => $id,
            "user" => $user,
            "encoded" => $encoded
        ]);

    }

    public function delete(): void {
        $user = $this->get_user_or_redirect();

        $id = $_GET["param1"] ?? null;
        if($id === null || !is_numeric($id)){
            $this->redirect("item","list");
        }

        $item = Item::get_by_id($id);
        if($item === null){
            $this->redirect("item","list");
        }

        $owner = User::get_users_by_id($item->get_owner_id());

        if(isset($_POST['delete'])){
            $item->delete();
            $this->redirect("item","list");
        }
        (new View("delete_item"))->show([
            "item" => $item,
            "owner" => $owner
        ]);
    }

    public function manage_images() : void {
        $user = $this->get_user_or_false();
        if (!$user) {
            $this->redirect("user", "login");
            return;
        }
        $itemId = (int)($_GET["param1"] ?? 0);
        $encoded = $_GET['param2'] ?? null;
        $item = Item::get_by_id($itemId);
        if($item === null) $this->redirect("item");


        $pictures = ItemPicture::get_by_item($itemId);

        (new View("manage_images"))->show([
            "item" => $item,
            "pictures" => $pictures,
            "errors" => [],
            "user" => $user,
            "encoded" => $encoded
        ]);
    }

    public function upload_images(): void {
        $user = $this->get_user_or_redirect();
        
        $itemId = (int)($_GET["param1"] ?? 0);
        $item = Item::get_by_id($itemId);
        if ($item === null) $this->redirect("item");

        $errors = [];

        if(!isset($_FILES["images"])){
            $errors[] = "No files receveid.";
        } else {

            // prochaine priorité = max_priority + 1
            $priority = ItemPicture::get_max_priority($itemId) + 1;

            $names = $_FILES["images"]["name"];
            $tmp   = $_FILES["images"]["tmp_name"];
            $size  = $_FILES["images"]["size"];
            $err   = $_FILES["images"]["error"];

            for ($i = 0; $i < count($names); $i++) {

             // ignorer cases vides   
             if ($names[$i] === "") continue;
             
             $file = [
                "name" => $names[$i],
                "tmp_name" => $tmp[$i],
                "size" => $size[$i],
                "error" => $err[$i]
             ];

             // validation fichier
             $fileErrores = ImageManager::validate($file);
             if (!empty($fileErrors)) {
                foreach ($fileErrors as $e) {
                    $errors[] = $names[$i] . " : " . $e;
                }
                continue;
            }

            // création fichier main + thumbnail
            $baseName = pathinfo($names[$i], PATHINFO_FILENAME);

            try {
                $mainPath = ImageManager::save_item_image($file, $itemId, $baseName, $priority);

                //  db
                $pic = new ItemPicture(
                    item: $itemId,
                    priority: $priority,
                    picture_path: $mainPath
                );

                $persistErrors = $pic->persist();
                if (!empty($persistErrors)) {
                    foreach ($persistErrors as $e) {
                        $errors[] = $names[$i] . " : " . $e;
                    }
                    // si persist rate, on supprime les fichiers créés 
                    $thumb =preg_replace('/\.jpg$/', '_thumbnail.jpg', $mainPath);
                    if (file_exists($mainPath)) unlink($mainPath);
                    if (file_exists($thumb)) unlink($thumb);

                    continue;
                }

                $priority++;

            } catch (Exception $ex) {
                $errors[] = $names[$i] . " : " . $ex->getMessage();
              }
        }
    }
        // recharge la page avec les images + erreurs
        $pictures = ItemPicture::get_by_item($itemId);
        (new View("manage_images"))->show([
            "item" => $item,
            "pictures" => $pictures,
            "errors" => $errors,
            "user" => $user
        ]);

}

            
    public function move_picture(): void {
        $itemId = (int)($_GET["param1"] ?? 0);
        $priority = (int)($_GET["param2"] ??0);
        $dir = $_GET["param3"] ?? "";

        if($dir === "left" && $priority > 1){
            ItemPicture::swap_priority($itemId, $priority, $priority - 1);
        } else if ($dir === "right"){
            $max = ItemPicture::get_max_priority($itemId);
            if($priority < $max) ItemPicture::swap_priority($itemId, $priority, $priority + 1);
        }
        $this->redirect("item","manage_images",$itemId);
    }
    
    public function delete_picture(): void {
        $itemId = (int)($_GET["param1"] ?? 0);
        $priority = (int)($_GET["param2"] ?? 0);

        $pics = ItemPicture::get_by_item($itemId);
        $toDelete = null;
        foreach ($pics as $p) if ($p->get_priority() === $priority) $toDelete = $p;

           if ($toDelete !== null) {
                // supprime fichiers (main et thumb)
                $main = $toDelete->get_picture_path();
                $thumb = preg_replace('/\.jpg$/', '_thumbnail.jpg', $main);

                if (file_exists($main)) unlink($main);
                if (file_exists($thumb)) unlink($thumb);

                $toDelete->delete();

                // réindexer les priorités 
                $pics = ItemPicture::get_by_item($itemId);
                $p = 1;
                foreach ($pics as $img) {
                    if ($img->get_priority() !== $p) {
                        ItemPicture::swap_priority($itemId, $img->get_priority(), $p);
                    }
                    $p++;
                }
            }   

        $this->redirect("item", "manage_images", $itemId);
    }

    public function move_picture_service(): void {
    $itemId = (int)($_POST["param1"] ?? 0);
    $priority = (int)($_POST["param2"] ?? 0);
    $dir = $_POST["param3"] ?? "";

    if ($itemId <= 0 || $priority <= 0 || !in_array($dir, ["left", "right"])) {
        http_response_code(400);
        echo json_encode(["error" => "Bad request"]);
        return;
    }

    if ($dir === "left" && $priority > 1) {
        ItemPicture::swap_priority($itemId, $priority, $priority - 1);
    } else if ($dir === "right") {
        $max = ItemPicture::get_max_priority($itemId);

        if ($priority < $max) {
            ItemPicture::swap_priority($itemId, $priority, $priority + 1);
        }
    }

    echo json_encode(["success" => true]);
}
    public function remove_picture_service() : void {
        $item_id = (int)($_POST["param1"] ?? 0);
        $priority = (int)($_POST["param2"] ?? 0);

        if ($item_id <= 0 || $priority <= 0) {
            http_response_code(400);
            echo json_encode(["error" => "Bad request"]);
            return;
        }

        $picture = ItemPicture::get_by_item_and_priority($item_id, $priority);

        if ($picture === null) {
            http_response_code(404);
            echo json_encode(["error" => "Picture not found"]);
            return;
        } else {
            
            $main = $picture->get_picture_path();
            $thumb = preg_replace('/\.jpg$/', '_thumbnail.jpg', $main);
            
            if (file_exists($main)) unlink($main);
            if (file_exists($thumb)) unlink($thumb);


            $picture->delete();
            ItemPicture::reindex_after_delete($item_id,$priority);
        }

        echo json_encode(["success" => true]);
    }
    /*------------ Ici le contenur de open Item ----------------*/

    public function open() : void {
        $id = isset($_GET['param1']) ? (int) $_GET['param1'] : null;
        $img_priority = isset($_GET['param4']) ? (int) $_GET['param'] : 1;
        $from = isset($_GET['param2'] ) ? $_GET['param2'] : null;
        $errors = [];
        $main_picture_path = null;
        $user_can_edit = false;
        $user_is_guest = false;

        $query = trim($_POST["query"] ?? "");
        $encoded = isset($_GET['param3'] ) ? $_GET['param3'] : null;


        if ($id == null) {
            $errors[] = "Le controller a besoin d'un id pour charger la vue open item";
        }
        if ($from == null) {
            $errors[] = "L'information from est manquante";
        }

        $item = ItemStatus::get_item_by_id($id);
        $seller = User::get_users_by_id($item->getOwner());
        // $bid = $this->get_bid_status($id);
        $pictures = $item->get_all_pictures_by_item_id();
        $bids_history = $item->get_bids_history();
        $current_user = $this->get_user_or_false();

        if ($img_priority === 1) { 
            $main_picture_path = $item->get_main_picture_by_item_id();
        } else {
            $main_picture_path = $item->get_picture_by_priority($img_priority,$item->getId());
        }
        //  $item->getId();
        // --------------- gestion des différents cas ---------------- //
        if ($current_user instanceof User) {
            $pricing_case = $this->get_case($current_user->getId(), $item);
            $user_can_edit = $this->user_can_edit($from, $item, $current_user->getId());
        } else {
            $pricing_case = $this->get_case(null, $item);
            $user_is_guest = true;
        }
        // $pricing_case = $this->get_case($item->getId(), $current_user->getId());

        (new View("open_item"))->show([
            "item" => $item, 
            "main_picture_path" => $main_picture_path,
            "pictures" => $pictures,
            /*"bid" => $bid*/ 
            "seller" => $seller, 
            "pricing_case" => $pricing_case, 
            "can_edit" => $user_can_edit,
            "from" => $from,
            "user" => $current_user,
            "user_is_guest" => $user_is_guest,
            "bids_history" => $bids_history,
            "encoded" => $encoded
        ]);
    } 

    // public function itemDetails(): void {}

    private function get_case(?int $current_user_id, ItemStatus $item) : string {
        // $bid = $this->get_bid_status($item_id);

        $isClosed = $item->bid_is_closed();
        
       // $isAuction = $bid->is_auction();
        //$isAuction = $item->isAuction();
        
        // $hasBids = $bid->has_bids();
        // $hasBuyNow = $bid->has_buy_now();
        // $topBider = ItemStatus::get_top_bidder($current_user->getId());
        
        if(count($item->get_bids_history())!=0){

            $lastBiderId = $item->get_bids_history()['0']['id'];
        }

        // $buyNowReached = $bid->buy_now_reached();
        if ($current_user_id !== null ) {
            $userIsSeller = $item->user_is_owner($current_user_id);
            if (!$isClosed && !$userIsSeller && !$item->hasBuyNow() && !$item->has_bids()){
                return "case1";
            }
        
            if (!$isClosed && !$userIsSeller && $item->has_bids() && $item->hasBuyNow()) {
                return "case2";
            } 
            if (!$isClosed && !$userIsSeller && $item->isDirectSale()) {
                return "case3";
            } 
            if (!$isClosed && $userIsSeller) {
                return "case4";
            } 
            if (!$userIsSeller && ($isClosed && $current_user_id === $lastBiderId)) {
                return "case5";
            } 
            if ($isClosed && $userIsSeller) {
                return "case6";
            }
            if (!$isClosed && !$userIsSeller && !$item->has_bids() && $item->has_buy_now()) {
                return "case7";
            } 
            if (!$isClosed && $item->has_bids()) {
                return "case8";
            }
            return"";
        } else {

            // si j'arrive ici c'est qu'aucun user n'est connecté 
             if (!$isClosed && !$item->has_buy_now() && !$item->has_bids()){
                return "case1";
            }
            if (!$isClosed && $item->has_bids() && $item->has_buy_now()) {
                return "case2";
            } 
            if (!$isClosed && $item->isDirectSale()) {
                return "case3";
            } 
            if (!$isClosed && $item->has_bids()) {
                return "case8";
            }
            if (!$isClosed && !$item->has_buy_now() && $item->has_bids()) {
                return "case8";
            }
            if (!$isClosed && $item->has_buy_now() && !$item->has_bids()) {
                return "case2";
            }
            return "";
        }
        
    }

    private function user_can_edit($from, ItemStatus $item,int $current_user_id) : bool {

        // $bid = $this->get_bid_status($item->getId());
        $user_is_seller = $item->user_is_owner($current_user_id);

        if ($user_is_seller && $from !== 'from-browse' && !$item->hasBids()) {
            return true;
        }
        return false;
        
    }

    /*
    private function get_bid_status(int $id): ItemSaleStatus {
        
        // $bid = ItemSaleStatus::get_current_bid_by_item_id($id);
        return ItemSaleStatus::get_current_bid_by_item_id($id);
    }*/
    

    private function get_current_time(): ?string {
        return AppTime::get_current_datetime();
    }

    public function place() : void {
        // gérer le cas ou un guest essaye de participer !!! 
        $current_user = $this->get_user_or_redirect();
        
        if(count($_POST)){
        $item = $_POST['item'];
        $amount = $_POST['amount'];
        $from = $_POST['from'] ?? 'from-browse';
            //var_dump($item, " *** ", $amount);die ("arrivé sur place ");
        }else {
            $item=$_GET['param1'];
            $item_pop = ItemStatus::get_item_by_id($item);
            $amount=$item_pop->getBuyNowPrice();
            $from = $_POST['from'] ?? 'from-browse';
            
        }
        
        ItemStatus::bid($current_user,$item,$amount);
        
       $this->redirect("item","open",$item, $from);

    }
    public function item_validation_constants_service() : void {
        echo json_encode([
            "title_min_length" => Configuration::get("title_min_length"),
            "title_max_length" => Configuration::get("title_max_length"),
            "description_min_length" => Configuration::get("description_min_length"),
            "duration_days_min" => Configuration::get("duration_days_min"),
            "duration_days_max" => Configuration::get("duration_days_max") 
        ]); 

    }
    public function validate_title_service() : void {
        $user = $this->get_user_or_false();
        
        if (!$user) {
            echo json_encode(["exists_already" => false]);
            return;
        }
        $res = false; 
        $id = $_POST['id'] ?? null;
        $title = isset($_POST['title']) ? trim(mb_strtolower($_POST['title'])) : '';

        if ($id === "" || $id === "0") {
            $id = null;
        }
        

        // si c'est un edit
        if ($id !== null) {
            $item = Item::get_by_id($id); 
            
            if (!$item) {
                echo json_encode(["exists_already" => false]);
                return;
            }
            // var_dump($item); die();
            $res = Item::title_exists_for_owner(
                $title,
                $item->get_owner_id(),
                $item->get_id()
            );
        } 
        // si c'est un add 
        else {
            $res = Item::title_exists_for_owner(
                $title,
                $user->getId()
            );
        }
        echo json_encode([
            "exists_already" => $res
        ]);   
    }

    public function update_pictures_order_service(): void {
    $user = $this->get_user_or_false();

    if (!$user) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        return;
    }

    $itemId = (int)($_POST["item_id"] ?? 0);
    $priorities = $_POST["priorities"] ?? [];

    if ($itemId <= 0 || !is_array($priorities)) {
        http_response_code(400);
        echo json_encode(["error" => "Bad request"]);
        return;
    }

    $item = Item::get_by_id($itemId);

    if ($item === null) {
        http_response_code(404);
        echo json_encode(["error" => "Item not found"]);
        return;
    }

    ItemPicture::update_order($itemId, $priorities);

    echo json_encode(["success" => true]);
}

}


