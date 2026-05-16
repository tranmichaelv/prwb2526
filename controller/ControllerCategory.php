<?php
require_once "framework/Controller.php";
require_once "model/Category.php";
require_once "model/Item.php";
require_once "utils/ImageManager.php";
require_once 'model/User.php';
require_once "utils/Utils.php";


class ControllerCategory extends Controller {


    public function index() : void {
        $this->manage();
    }

    public function manage() : void {
        $this->check_admin();

        $categories = Category::get_all_categories();

        (new View("manage_category"))->show([
            "categories" => $categories,
            "user" => $this->get_user_or_false()
        ]);
    }

    
    public function up() : void {
        $this->check_admin();

        $id = $_POST["id"] ?? null;

        $category = Category::get_by_id($id);

        if($category !== null && $category->get_priority() > 1) {
            Category::swap_priority($category->get_priority(), $category->get_priority() - 1 );
        }
        
        $this->redirect("category", "manage");
    }

    public function down(): void {
        $this->check_admin();

        $id = $_POST["id"] ?? null;

        $category = Category::get_by_id($id);
        $maxPriority = Category::get_max_priority();

        if ($category !== null && $category->get_priority() < $maxPriority) {
            Category::swap_priority(
                $category->get_priority(),
                $category->get_priority() + 1
            );
        }
    
        $this->redirect("category", "manage");
    }

    public function save() : void {
        $this->check_admin();

        $id = $_POST["id"] ?? null;
        $name = trim($_POST["name"] ?? "");

        $category = Category::get_by_id($id);

        $category->set_name($name);

        $errors = $category->persist();

        if(!empty($errors)) {
            $categories = Category::get_all_categories();

            (new View("manage_category"))->show([
                "categories" => $categories,
                "errors" => $errors,
                "error_id" => $id,
                "edited_name" => $name,
                "user" => $this->get_user_or_false()
            ]);

            return;
        }

         $this->redirect("category", "manage");
    }

    public function delete() : void {
        $this->check_admin();

        $id = $_POST["id"] ?? null;

        $category = Category::get_by_id($id);

        $errors = $category->delete();

        if (!empty($errors)) {
            $categories = Category::get_all_categories();

            (new View("manage_category"))->show([
            "categories" => $categories,
            "errors" => $errors,
            "user" => $this->get_user_or_false()
            ]);

            return;
        }

        $this->redirect("category", "manage");

    }

    public function confirm_delete(): void {
        $this->check_admin();

        $id = $_POST["id"] ?? null;
        $category = Category::get_by_id((int)$id);

        (new View("delete_category"))->show([
            "category" => $category,
            "user" => $this->get_user_or_false()
        ]);
    }

    public function add() : void {

        $this->check_admin();

        if (empty($_POST)) {
            $this->redirect("category", "manage");
            return;
        }

        $name = trim($_POST["name"] ?? "");
        $priority = Category::get_max_priority() + 1;

        $category = new Category(
            id : null,
            name : $name,
            priority : $priority
        );

            $errors = $category->persist(); 

        if (!empty($errors)) {
            $categories = Category::get_all_categories();

            (new View("manage_category"))->show([
                "categories" => $categories,
                "add_errors" => $errors,
                "user" => $this->get_user_or_false()
            ]);

            return;
        }

        $this->redirect("category", "manage");
    }

    private function check_admin() : void {
        $user = $this->get_user_or_false();

        if(!$user || $user->getRole() !== "admin") {
            $this->redirect("item", "browse");
        }
    }

}