<?php
require_once "framework/Controller.php";
require_once "model/Item.php";
require_once "utils/Apptime.php";

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

        if ($bid_raw === null || $bid_raw === '') {
            $starting_bid = null; // champ vide → placeholder
        } else {
            $starting_bid = (float)$bid_raw; // utilisateur a saisi quelque chose
        }

       $buy_raw = $_POST['buy_now'] ?? null;

        if ($buy_raw === null || $buy_raw === '') {
            $buy_now = null;
        } else {
            $buy_now = (float)$buy_raw;
        }            

        $item = new Item(
            id: null,
            title: $title,
            description: $description,
            owner: $owner,
            duration_days: $duration,
            starting_bid: $starting_bid,
            buy_now_price: $buy_now
        );
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $item->persist(); // validation + insert tentative

            if (empty($errors)) {
                // redirection vers la liste ou la page détails
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

            $bid_raw = $_POST['bid'] ?? null;
            $item->set_starting_bid(($bid_raw === null || $bid_raw === '') ? null : (float)$bid_raw);

            $buy_raw = $_POST['buy_now'] ?? null;
            $item->set_buy_now_price(($buy_raw === null || $buy_raw === '') ? null : (float)$buy_raw);

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
}