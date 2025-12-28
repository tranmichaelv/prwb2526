<?php

require_once 'model/User.php';
require_once "utils/AppTime.php";
require_once "framework/Controller.php";
require_once "framework/Configuration.php";
require_once "ControllerSetup.php";

class ControllerUser extends Controller {
    
    //si l'utilisateur est connecté, redirige vers son profil. Sinon, produit la vue d'accueil.
    public function index() : void {
        if ($this->user_logged()) {
            $this->redirect("item", "browse");
        } else {
            $this->redirect("user", "login");
        }
    }

    //gestion de la connexion d'un utilisateur
    public function login() : void {

         // Login en tant que guest renvoie l'utilisateur directement au controlleur item
         if (isset($_POST['guest_login'])) {
            $this->redirect("item");
            return;
        }

    
    // Login normal
        $email = '';
        $password = '';
        $errors = [];
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $errors = User::validate_login($email, $password);
            if (empty($errors)) {
                $this->log_user(User::get_user_by_email($email));
            }
        }
        (new View("login"))->show(["email" => $email, "password" => $password, "errors" => $errors]);
    }

    public function profile() : void{
        $user = $this->get_user_or_false();
        $page = "profile";

        (new View("profile"))->show(["user" => $user, "page" =>  $page]);
    }

    public function edit_profile() : void{
        $user = $this->get_user_or_false();
        $page = "edit_profile";

        (new View("edit_profile"))->show(["user" => $user, "page" =>  $page]);
    }

    public function change_password() : void{
        $user = $this->get_user_or_false();
        $page = "change_password";

        (new View("change_password"))->show(["user" => $user, "page" =>  $page]);
    }

    public function manage_profile_picture() : void{
        $user = $this->get_user_or_false();
        $page = "manage_profile_picture";

        (new View("manage_profile_picture"))->show(["user" => $user, "page" =>  $page]);
    }

    //########################### NOT FINISHED ###########################
    public function signup() : void{
        // Retourne vers la page d'acceuil
        if (isset($_POST['cancel'])) {
            $this->redirect("user", "login");
            return;
        }

        $email = '';
        $full_name = '';
        $pseudo = '';
        $password = '';
        $errors = [];

        if (isset($_POST['email']) && isset($_POST['full_name']) && isset($_POST['pseudo']) && isset($_POST['password'])) {
            $email = $_POST['email'];
            $full_name = $_POST['full_name'];
            $pseudo = $_POST['pseudo'];
            $password = $_POST['password'];
        }

        (new View("signup"))->show(["email" => $email, "full_name" => $full_name, "pseudo" => $pseudo, "password" => $password, "errors" => $errors]);
    }
    //########################### NOT FINISHED ###########################

    // -------- For Debug Purpose --------
    public function loginAsQuentin() : void{
        Configuration::is_dev();
        $user = User::get_user_by_pseudo("Quentin"); 
        $this->log_user($user);
    }

    public function loginAsBoris() : void{
        Configuration::is_dev();
        $user = User::get_user_by_pseudo("Boris"); 
        $this->log_user($user);
    }

    public function loginAsMarc() : void{
        Configuration::is_dev();
        $user = User::get_user_by_pseudo("Marc"); 
        $this->log_user($user);
    }

    public function loginAsXavier() : void{
        Configuration::is_dev();
        $user = User::get_user_by_pseudo("Xavier"); 
        $this->log_user($user);
    }
}


