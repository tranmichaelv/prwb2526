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
            "mode" => "add",         // mode édit plus tard
            "errors" => $errors,
            "item" => $item
        ]);
    }
}