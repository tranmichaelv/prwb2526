<?php
require_once "framework/Controller.php";
require_once "model/Item.php";
require_once "model/ItemPicture.php";
// require_once "utils/Apptime.php";
require_once "utils/ImageManager.php";
require_once 'model/User.php';
require_once 'model/Item_pictures.php';
require_once "utils/Utils.php";
require_once "model/ItemStatus.php";
require_once "model/ItemSaleStatus.php";


require_once "model/Picture.php";
require_once "model/ItemSaleStatus.php";
require_once 'framework/View.php';
require_once 'utils/AppTime.php';
class ControllerItem extends Controller {

    public function index() : void {
        $this->browse();
    }

     // Page item/browse
    public function browse() : void {
        $user = $this->get_user_or_false();
        $page = "browse";
        // Si user n'est pas vide, connecte au browser de l'user sinon "guest login"
        if($user){
            $items_other = $user->get_my_other_items();    
            $items_user = $user->get_my_bids_items();

            (new View("browse"))->show(["items" => $items_other, "items_user" => $items_user, "user" => $user, "page" => $page]);
        } else{
            $all_items = ItemStatus::get_all_items();

            (new View("browse"))->show(["all_items" => $all_items, "user" => $user, "page" => $page]);
        }
    }

    // Page item/myIems
    public function myItems() : void {
        $user = $this->get_user_or_false();

        $page = "myItems";
        $active_items = $user->get_my_items();   
        $closed_unsold_items = $user->get_my_closed_unsold_items();
        $sold_items = $user->get_my_sold_items();

        (new View("list"))->show(["active_items" => $active_items, "closed_unsold_items" => $closed_unsold_items, "sold_items" => $sold_items, "user" => $user, "page" => $page]);
    }

    // Page item/sales
    public function sales() : void {
        $user = $this->get_user_or_false();

        if($user){
        $page = "sales";
        $sales = $user->get_my_sales(); 
        $topPerson = $user->get_my_purchases_top_bidder();  
       
        $totalAndAverage = $user->get_my_sales_total_and_average(); 
        (new View("sales"))->show(["sales" => $sales, "topPerson" => $topPerson, "totalAndAverage"=> $totalAndAverage, "user" => $user, "page" => $page]);
        } else {
            $this->redirect("user", "login");
        }
    }

    // Page item/purchases
    public function purchases() : void {
        $user = $this->get_user_or_false();

        if($user){
            $page = "purchases";
            $purchases = $user->get_my_purchases();   
            $topPerson = $user->get_my_purchases_top_seller();

            $totalAndAverage = $user->get_my_purchases_total_and_average(); 
            (new View("purchases"))->show(["purchases" => $purchases, "topPerson" => $topPerson, "totalAndAverage"=> $totalAndAverage, "user" => $user, "page" => $page]);
        } else {
            $this->redirect("user", "login");
        }
    }

    public function add() : void {
       $user = $this->get_user_or_false();
        if (!$user) {
            $this->redirect("user", "login");
            return;
        }

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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $item->persist(); // validation + insert tentative

            if (empty($errors)) {
                // redirection vers Open Item
                // plus tard
                $this->redirect("item","open", $item->get_id());
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

        $user = $this->get_user_or_false();
        if (!$user) {
            $this->redirect("user", "login");
            return;
        }

        $id = $_GET['param1'] ?? null;

        if ($id === null || !is_numeric($id)) {
            $this->redirect("item");
        }

        $item = Item::get_by_id((int)$id);
        if ($item === null) {
            $this->redirect("item");
        }

        $errors = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
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
            $this->redirect("item","open",$item->get_id());
        }
    }

        (new View("add_edit_item"))->show([
            "mode" => "edit",
            "errors" => $errors,
            "item" => $item,
            "id" => $id,
            "user" => $user
        ]);

    }

    public function delete(): void {
        $id = $_GET["param1"] ?? null;
        if($id === null || !is_numeric($id)){
            $this->redirect("item","myItems");
        }

        $item = Item::get_by_id($id);
        if($item === null){
            $this->redirect("item","myItems");
        }

        $owner = User::get_users_by_id($item->get_owner_id());

        if($_SERVER["REQUEST_METHOD"] === "POST"){
            $item->delete();
            $this->redirect("item","myItems");
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
        $item = Item::get_by_id($itemId);
        if($item === null) $this->redirect("item");


        $pictures = ItemPicture::get_by_item($itemId);

        (new View("manage_images"))->show([
            "item" => $item,
            "pictures" => $pictures,
            "errors" => [],
            "user" => $user
        ]);
    }

    public function upload_images(): void {
        $user = $this->get_user_or_false();
        if (!$user) {
            $this->redirect("user", "login");
            return;
        }
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


    /*------------ Ici le contenur de open Item ----------------*/

    public function open() : void {
        //$this->redirect("openitem");
        //$this->redirect("openitem","index",$_GET["param1"]);
        //$var = Item::test($_GET['param1']);
        //var_dump($var);
        //die ();
        // die("Arrivé sur open dans controller item ");
        $this->itemDetails();
    } 

    /*
    public function index(): void {
        $this->itemDetails();
    }
    */

    public function itemDetails(): void {
        
        
        $id = isset($_GET['param1']) ? (int) $_GET['param1'] : null;
        $img_priority = isset($_GET['param3']) ? (int) $_GET['param3'] : 1;
        $from = isset($_GET['param2'] ) ? $_GET['param2'] : null;
        $errors = [];
        
        if ($id == null) {
            $errors[] = "Le controller a besoin d'un id pour charger la vue open item";
        }
        if ($from == null) {
            $errors[] = "Le controller a besoin de savoir from-browse/items";
        }
        $current_user = $this->get_user_or_redirect();

        $item = ItemStatus::get_item_by_id($id);

        //$item->getMaxBid();

        $pictures = $item->get_all_pictures_by_item_id();
        $main_picture_path = null;
        if ($img_priority === 1) { // si je suis pas arrivé avec une priorité -> je viens pas de l'url de l'image addionnelle 
            $main_picture_path = $item->get_main_picture_by_item_id();
        } else {
            $main_picture_path = $item->get_picture_by_priority($img_priority,$item->getId());
    
        }
        // $main_picture_path = $item->get_main_picture_by_item_id();
       
       
        $bid = $this->get_bid_status($id);
    
        $seller = User::get_users_by_id($item->getOwner());
        
        // var_dump($seller->getPicturePath()); die();
        
        
       
        // var_dump();
        // die ();
        $item->getStartingBid();
        // $item->getStartingBid();
        // $lastBider = $bid->get_bids_history()['0']['pseudo'];
        // var_dump($lastBider); die ();
        
        // $status = $this->get_bid_status($id);
        $bids_history = $bid->get_bids_history();
        // faire la vérif si y a pas de bids 
        // var_dump($bids_history); 
        // var_dump();

        // var_dump($bids_history['0']['pseudo']);
        // die;

        // --------------- gestion des différents cas ---------------- // 
        $pricing_case = $this->get_case($item->getId(), $current_user);
    
        $user_can_edit = $this->user_can_edit($from, $item, $current_user);
        
        
        (new View("open_item"))->show([
            "item" => $item, 
            "main_picture_path" => $main_picture_path,
            "pictures" => $pictures,
            "bid" => $bid, 
            "seller" => $seller, 
            "pricing_case" => $pricing_case, 
            "can_edit" => $user_can_edit,
            "from" => $from,
            "user" => $current_user,
            "bids_history" => $bids_history
        ]);
    }

    private function get_case(int $item_id,User $current_user) : string {
        $bid = $this->get_bid_status($item_id);

        $isClosed = $bid->bid_is_closed();
        $userIsSeller = $bid->user_is_owner($current_user);
       // $isAuction = $bid->is_auction();
        //$isAuction = $item->isAuction();
        
        // $hasBids = $bid->has_bids();
        // $hasBuyNow = $bid->has_buy_now();
        // $topBider = ItemStatus::get_top_bidder($current_user->getId());
        
        if(count($bid->get_bids_history())!=0){

            $lastBiderId = $bid->get_bids_history()['0']['id'];
        }

        $buyNowReached = $bid->buy_now_reached();
        
        if (!$isClosed && !$userIsSeller && !$bid->getHasBuyNow() && !$bid->getHasBids()){
            return "case1";
        }
        
        if (!$isClosed && !$userIsSeller && $bid->getHasBids() && $bid->getHasBuyNow()) {
            return "case2";
        } 
        if (!$isClosed && !$userIsSeller && $bid->getIsDirectSale()) {
            return "case3";
        } 

        if (!$isClosed && $userIsSeller) {
            return "case4";
        } 
        if (!$userIsSeller && ($isClosed && $current_user->getId() === $lastBiderId)) {
            return "case5";
        } 
        if ($isClosed && $userIsSeller) {
            return "case6";
        }
        if (!$isClosed && !$userIsSeller && !$bid->gethasBids() && $bid->has_buy_now()) {
            return "case7";
        } 
        if (!$isClosed && $bid->has_bids()) {
            return "case8";
        }
        return"";
    }

    private function user_can_edit($from, $item,User $current_user) : bool {

        $bid = $this->get_bid_status($item->getId());
        $user_is_seller = $bid->user_is_owner($current_user);

        // if ($from === "from-browse" || ($from === 'from-items' && $item->hasBids())) {
        //     return false;
        //} 

        if ($user_is_seller && $from !== 'from-browse' && !$item->hasBids()) {
            return true;
        }
        return false;
        
    }

    private function get_bid_status(int $id): ItemSaleStatus {
        
        // $bid = ItemSaleStatus::get_current_bid_by_item_id($id);
        return ItemSaleStatus::get_current_bid_by_item_id($id);
    }
    

    private function get_current_time(): ?string {
        return AppTime::get_current_datetime();
    }

    public function place() : void {
        $current_user = $this->get_user_or_redirect();
        
        
        if(count($_POST)){
        $item = $_POST['item'];
        $amount = $_POST['amount'];
        // var_dump("id -> ",$item, "amount -> ", $amount); die("ne doit arrivé la ????");
        // var_dump($item, " *** ", $amount);die ("arrivé sur place ");
        }else {
            $item=$_GET['param1'];
            // var_dump($item);die ("je recois l'id en paramèttre donc je viens d'un achat");
            $item_pop = ItemStatus::get_item_by_id($item);
            $amount=$item_pop->getBuyNowPrice();

            // var_dump("id -> ",$item, "amount -> ", $amount); die("Bien arrivé par l'achat");
            // die ("Bien arivé sur place pour buy ");
        }
        // die("arrivé sur place dans controller item ");
        ItemStatus::bid($current_user,$item,$amount);
        
       $this->redirect("item","open",$item);
    }

    /* public function buy() : void {
        $current_user = $this->get_user_or_false();
        if (!$current_user) {
            die ("Not logged in");
        }
        $item = $_POST['item'];
        $amount = $_POST['amount'];
        $check = ItemStatus::buy($current_user,$item,$amount);
        
        if ($check == true) {
            header("Location: /prwb_2526_c05/item/open/$item");
        } else {
            die ("arreter le programme pour le moment");
        }
    } */


}


