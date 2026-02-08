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

        $is_dev = Configuration::is_dev();
        // $is_dev = false;
        (new View("login"))->show(["email" => $email, "password" => $password, "errors" => $errors, "is_dev" => $is_dev]);
    }

    // -------- Change Password --------
    public function change_password() : void{
        $this->require_login();
        $user = $this->get_user_or_false();
        $page = "change_password";
        
        $current_password = '';
        $new_password = '';
        $confirm_new_password = '';
        $errors = [];

        if (isset($_POST['current_password']) && isset($_POST['new_password']) && isset($_POST['confirm_new_password'])) {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_new_password = $_POST['confirm_new_password'];

            $errors = $user->validate_change_password($current_password, $new_password , $confirm_new_password);

            $has_errors = false;
            foreach ($errors as $field_errors) {
                if (!empty($field_errors)) {
                    $has_errors = true;
                }
            }
            
            if(!$has_errors){
                $user->persist();
                $this->redirect("user", "profile");
            }
        }

        (new View("change_password"))->show(["user" => $user, 
                                             "page" =>  $page, 
                                             "errors" => $errors, 
                                             "current_password" => $current_password, 
                                             "new_password" => $new_password, 
                                             "confirm_new_password" => $confirm_new_password
                                            ]);
    }

    // -------- Edit Profile --------
    public function edit_profile() : void{
        $this->require_login();
        $user = $this->get_user_or_false();
        $page = "edit_profile";
        $errors = [];

        if (isset($_POST['full_name']) || isset($_POST['username']) || isset($_POST['email']) || isset($_POST['iban'])) {
            $full_name = $_POST['full_name'] ?? $user->getFullName();
            $username = $_POST['username'] ?? $user->getPseudo();
            $email    = $_POST['email'] ?? $user->getEmail();
            $iban     = $_POST['iban'] ?? $user->getIban();
            
            $errors = $user->validate_update($full_name, $username, $email, $iban);
            
            $has_errors = false;
            foreach ($errors as $field_errors) {
                if (!empty($field_errors)) {
                    $has_errors = true;
                }
            }

            if(!$has_errors){
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

            $has_errors = false;
            foreach ($errors as $field_errors) {
                if (!empty($field_errors)) {
                    $has_errors = true;
                }
            }

            if(!$has_errors) {
                $user = new User(Configuration::get("temporary_user_id"),$full_name, $email,$pseudo,password_hash($password, PASSWORD_BCRYPT),"user");
                $user->persist();

                $this->login();
            }
        }

        (new View("signup"))->show(["email" => $email, 
                                    "full_name" => $full_name, 
                                    "pseudo" => $pseudo, 
                                    "password" => $password, 
                                    "confirm" => $confirm, 
                                    "errors" => $errors
                                ]);
    }


    // -------- Doit être connecté --------
    private function require_login(): void {
        if (!$this->user_logged()) {
            $this->redirect("user", "login");
        }
    }


    // -------- For Debug Purpose --------
    private function admin_login($nom) : void {
        if(Configuration::is_dev()){
            $user = User::get_users_by_pseudo($nom); 
            $this->log_user($user);
        } else {
            $this->redirect("user", "login");
        }
    }

    public function login_as_quentin() : void{
        $this->admin_login("Quentin");
    }

    public function login_as_boris() : void{
        $this->admin_login("Boris");
    }

    public function loginAsMarc() : void{
        $this->admin_login("Marc");
    }

    public function login_as_xavier() : void{
        $this->admin_login("Xavier");
    }
}


