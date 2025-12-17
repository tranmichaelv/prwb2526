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
}