<?php

require_once 'model/User.php';
require_once "utils/AppTime.php";
require_once "framework/Controller.php";
require_once "framework/Configuration.php";
require_once "ControllerSetup.php";

class ControllerLogin extends Controller {
    
    //si l'utilisateur est connecté, redirige vers son profil.
    //sinon, produit la vue d'accueil.
    public function index() : void {
        if ($this->user_logged()) {
            $this->redirect("browseItems", "browse");
        } else {
            $this->redirect("login", "login");
        }
    }

    public function loginAsQuentin() : void{
        Configuration::is_dev();
        $user = User::get_users_by_pseudo("Quentin"); 
        $this->log_user($user);
    }

    public function loginAsBoris() : void{
        Configuration::is_dev();
        $user = User::get_users_by_pseudo("Boris"); 
        $this->log_user($user);
    }

    public function loginAsMarc() : void{
        Configuration::is_dev();
        $user = User::get_users_by_pseudo("Marc"); 
        $this->log_user($user);
    }

    public function loginAsXavier() : void{
        Configuration::is_dev();
        $user = User::get_users_by_pseudo("Xavier"); 
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
        $pseudo = '';
        $password = '';
        $errors = [];
        if (isset($_POST['pseudo']) && isset($_POST['password'])) { //note : pourraient contenir des chaînes vides
            $pseudo = $_POST['pseudo'];
            $password = $_POST['password'];

            $errors = User::validate_login($pseudo, $password);
            if (empty($errors)) {
                $this->log_user(User::get_users_by_pseudo($pseudo));
            }
        }
        (new View("login"))->show(["pseudo" => $pseudo, "password" => $password, "errors" => $errors]);
    }

}


