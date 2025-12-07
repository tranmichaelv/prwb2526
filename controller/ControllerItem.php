<?php
require_once "framework/Controller.php";
require_once "utils/Apptime.php";

class ControllerItem extends Controller {

    public function index(): void
    {
        echo "<p><a href='item/add'>Ajouter une annonce</a></p>";
    }

    public function add() : void {
        (new View("add_edit_item"))->show([
            "mode" => "add",         // mode édit plus tard
            "errors" => []
        ]);
    }
}