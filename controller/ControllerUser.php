<?php

require_once 'model/User.php';
require_once "utils/AppTime.php";
require_once "framework/Controller.php";
require_once "framework/Configuration.php";
require_once "ControllerSetup.php";

class ControllerUser extends Controller {
    
    //si l'utilisateur est connecté, redirige vers son profil.
    //sinon, produit la vue d'accueil.
    public function index() : void {
        if ($this->user_logged()) {
            $this->redirect("item", "browse");
        } else {
            $this->redirect("user", "login");
        }
    }

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

    //gestion de la connexion d'un utilisateur
    public function login() : void {
         // Login en tant que guest
         if (isset($_POST['guest_login'])) {
            $guest_user = User::guest_user(""); 
            $this->log_user($guest_user);
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

    public function signup() : void{
        if (isset($_POST['cancel'])) {
           $this->login();
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
            $errors = User::validate_login($email, $password);
        }

        (new View("signup"))->show(["email" => $email, "full_name" => $full_name, "pseudo" => $pseudo, "password" => $password, "errors" => $errors]);
    }

}


