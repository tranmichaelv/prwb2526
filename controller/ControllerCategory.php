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

    public function update_categories_order_service(): void {
       $this->check_admin();
        $ids = $_POST["param1"] ?? [];


        if (!is_array($ids)) {
            http_response_code(400);
            echo json_encode(["error" => "Bad request"]);
            return;
        }

        Category::update_order($ids);

        echo json_encode(["success" => true]);
    }

    public function get_categories_service() : void {
        $this->check_admin();

        echo json_encode([
            "categories" => Category::get_all_categories_as_array()
        ]);
    }

    public function update_name_category_service() : void {
        $this->check_admin();

        $id = intval($_POST["param1"] ?? 0);
        $name = trim($_POST["param2"] ?? "");
        
        $category = Category::get_by_id($id);

        if ($category === null) {
            echo json_encode([
                "success" => false,
                "errors" => ["Category not found."]
            ]);
            return;
        }

        $category->set_name($name);
        $errors = $category->persist();

        echo json_encode([
            "success" => empty($errors),
            "errors" => $errors,
            "categories" => Category::get_all_categories_as_array()
        ]);
    }

    public function add_category_service() : void {
        $this->check_admin();

        $name = trim($_POST["param1"] ?? "");
        $priority = Category::get_max_priority() + 1;

        $category = new Category(
            id: null,
            name: $name,
            priority: $priority
        );

        $errors = $category->persist();

        echo json_encode([
            "success" => empty($errors),
            "errors" => $errors,
            "categories" => Category::get_all_categories_as_array()
        ]);

    }

    public function delete_category_service(): void {
        $this->check_admin();

        $id = intval($_POST["param1"] ?? 0);
        $category = Category::get_by_id($id);

        if ($category === null) {
            echo json_encode([
                "success" => false,
                "errors" => ["Category not found."]
            ]);
            return;
        }

        $errors = $category->delete();

        echo json_encode([
            "success" => empty($errors),
            "errors" => $errors,
            "categories" => Category::get_all_categories_as_array()
        ]);
    }

    public function get_validation_config_service() : void {
        echo json_encode([
            "category_min_length" => Configuration::get("category_min_length"),
            "category_max_length" => Configuration::get("category_max_length")     
        ]);
    }

}