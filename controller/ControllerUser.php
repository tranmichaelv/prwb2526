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
                $this->log_user(User::get_users_by_email($email));
            }
        }
        (new View("login"))->show(["email" => $email, "password" => $password, "errors" => $errors]);
    }

    // -------- Change Password --------
    public function change_password(){
        $this->require_login();
        $user = $this->get_user_or_false();
        $page = "change_password";
        
        $currentPassword = '';
        $newPassword = '';
        $confirmNewPassword = '';
        $errors = [];

        if (isset($_POST['current_password']) && isset($_POST['new_password']) && isset($_POST['confirm_new_password'])) {
            $currentPassword = $_POST['current_password'];
            $newPassword = $_POST['new_password'];
            $confirmNewPassword = $_POST['confirm_new_password'];

            $errors = $user->validate_change_password($currentPassword, $newPassword , $confirmNewPassword);

            $hasErrors = false;
            foreach ($errors as $fieldErrors) {
                if (!empty($fieldErrors)) {
                    $hasErrors = true;
                }
            }
            
            if(!$hasErrors){
                $user->persist();
                $this->redirect("user", "profile");
            }
        }

        (new View("change_password"))->show(["user" => $user, "page" =>  $page, "errors" => $errors, "currentPassword" => $currentPassword, "newPassword" => $newPassword, "confirmNewPassword" => $confirmNewPassword]);
    }

    // -------- Edit Profile --------
    public function edit_profile() {
        $this->require_login();
        $user = $this->get_user_or_false();
        $page = "edit_profile";
        $errors = [];

        if (isset($_POST['full_name']) || isset($_POST['username']) || isset($_POST['email']) || isset($_POST['iban'])) {
            $fullName = $_POST['full_name'] ?? $user->getFullName();
            $username = $_POST['username'] ?? $user->getPseudo();
            $email    = $_POST['email'] ?? $user->getEmail();
            $iban     = $_POST['iban'] ?? $user->getIban();
            
            $errors = $user->validate_update($fullName, $username, $email, $iban);
            
            $hasErrors = false;
            foreach ($errors as $fieldErrors) {
                if (!empty($fieldErrors)) {
                    $hasErrors = true;
                }
            }

            if(!$hasErrors){
                $user->persist();
                $this->redirect("user", "profile");
            }
        }
        
        (new View("edit_profile"))->show(["user" => $user, "page" =>  $page, "errors" => $errors]);
    }

    // -------- Profile --------
    public function profile() : void{
        $this->require_login();
        $user = $this->get_user_or_false();
        $page = "profile";

        (new View("profile"))->show(["user" => $user, "page" =>  $page]);
    }

    // -------- Mangage Profile Picture --------
    public function manage_profile_picture() : void{
        $this->require_login();
        $user = $this->get_user_or_false();
        $page = "manage_profile_picture";
        $errors = [];

        if(isset($_POST['delete'])){
            $user->delete_picture_path();
        }

        if (isset($_POST['upload']) && !empty($_FILES['file']['tmp_name'])) {
            $file = $_FILES['file'];
            $errors = $user->upload_picture_path($file);
        }
        
        (new View("manage_profile_picture"))->show(["user" => $user, "page" =>  $page, "errors" => $errors]);
    }

    // -------- Signup --------
    public function signup() : void{
        // Retourne vers la page d'acceuil
        if (isset($_POST['cancel'])) {
            $this->redirect("user", "login");
            return;
        }

        $email = $_POST['email'] ?? '';
        $full_name = $_POST['full_name'] ?? '';
        $pseudo = $_POST['pseudo'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        $errors = [
            "email" => [],
            "full_name"  => [],
            "pseudo"  => [],
            "password"  => []
        ];

        if (isset($_POST['full_name']) && isset($_POST['pseudo']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['confirm_password'])) {
            $errors['email'] = array_merge(
                User::validate_email($_POST['email']),
                User::validate_unicity_email($_POST['email'])
            );

            $errors['full_name'] = array_merge(
                [],
                User::validate_full_name($_POST['full_name'])
            );

            $errors['pseudo'] = array_merge(
                User::validate_username($_POST['pseudo']),
                User::validate_unicity_pseudo($_POST['pseudo'])
            );

            $errors['password'] = array_merge(
                [],
                User::validate_password($_POST['password'], $_POST['confirm_password'])
            );

            $hasErrors = false;
            foreach ($errors as $fieldErrors) {
                if (!empty($fieldErrors)) {
                    $hasErrors = true;
                }
            }

            if(!$hasErrors) {
                $user = new User(99,$full_name, $email,$pseudo,password_hash($password, PASSWORD_BCRYPT),"user");
                $user->persist();

                $this->login();
            }
        }

        (new View("signup"))->show(["email" => $email, "full_name" => $full_name, "pseudo" => $pseudo, "password" => $password, "confirm" => $confirm, "errors" => $errors]);
    }


    // -------- Doit être connecté --------
    private function require_login(): void {
        if (!$this->user_logged()) {
            $this->redirect("user", "login");
            exit(); 
        }
    }


    // -------- For Debug Purpose --------
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
}


