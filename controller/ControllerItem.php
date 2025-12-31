<?php
require_once "framework/Controller.php";
require_once "model/Item.php";
require_once "model/ItemPicture.php";
require_once "utils/Apptime.php";
require_once "utils/ImageManager.php";

class ControllerItem extends Controller {

    public function index(): void
    {
        echo "<p><a href='item/add'>Ajouter une annonce</a></p>";
    }

    public function add() : void {
        $errors = [];

        // en attendant la classe User
        $owner = 1;

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
                $this->redirect("item");
            }
        }

        (new View("add_edit_item"))->show([
            "mode" => "add",         
            "errors" => $errors,
            "item" => $item
        ]);

    }

    public function edit() : void {
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
            $this->redirect("item");
        }
    }

        (new View("add_edit_item"))->show([
            "mode" => "edit",
            "errors" => $errors,
            "item" => $item,
            "id" => $id
        ]);

    }

    public function manage_images() : void {
        $itemId = (int)($_GET["param1"] ?? 0);
        $item = Item::get_by_id($itemId);
        if($item === null) $this->redirect("item");


        $pictures = ItemPicture::get_by_item($itemId);

        (new View("manage_images"))->show([
            "item" => $item,
            "pictures" => $pictures,
            "errors" => []
        ]);
    }

    public function upload_images(): void {
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
            "errors" => $errors
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

}
