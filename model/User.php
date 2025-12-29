<?php

require_once "framework/Model.php";
require_once "framework/Controller.php";
require_once "framework/Configuration.php";

class User extends Model {
    public function __construct(
        private int $id, 
        private string $full_name, 
        private string $email, 
        private string $pseudo, 
        private string $hashed_password, 
        private string $role, 
        private ?string $picture_path = null, 
        private ?string $iban = null
    ) {}
    // -------- Getters --------
    public function getId(): int { return $this->id; }
    
    public function getFullName(): string { return $this->full_name; }
    
    public function getEmail(): string { return $this->email; }
    
    public function getPseudo(): string { return $this->pseudo; }
    
    public function getHashedPassword(): string { return $this->hashed_password; }
    
    public function getRole(): string { return $this->role; }
    
    public function getPicturePath(): ?string { return $this->picture_path; }
    
    public function getIban(): ?string { return $this->iban; }

    // -------- Setters --------

    public function setFullName(string $full_name): void { $this->full_name = $full_name; }
    
    public function setEmail(string $email): void { $this->email = $email; }
    
    public function setPseudo(string $pseudo): void { $this->pseudo = $pseudo; }
    
    public function setHashedPassword(string $hashed_password): void { $this->hashed_password = $hashed_password; }
    
    public function setRole(string $role): void{ $this->role = $role; }
    
    public function setPicturePath(?string $picture_path): void { $this->picture_path = $picture_path; }
    
    public function setIban(?string $iban): void { $this->iban = $iban; }

    public function persist() : User {
        if(self::get_users_by_id($this->id))
            self::execute("UPDATE users SET full_name=:full_name, email=:email, pseudo=:pseudo, password=:password, role=:role, picture_path=:picture_path, iban=:iban WHERE id=:id ", 
                          ["id"=>$this->id, "full_name"=>$this->full_name, "email"=>$this->email, "pseudo"=>$this->pseudo, "picture_path"=>$this->picture_path, "password"=>$this->hashed_password, "role"=>$this->role, "iban"=>$this->iban]);
        else
            self::execute("INSERT INTO users(full_name, email, pseudo, password, role, picture_path, iban) VALUES(:pseudo,:password,:profile,:picture_path)", 
                          ["full_name"=>$this->full_name, "email"=>$this->email, "pseudo"=>$this->pseudo, "picture_path"=>$this->picture_path, "password"=>$this->hashed_password, "role"=>$this->role, "iban"=>$this->iban]);
        return $this;
    }

    public static function get_users_by_pseudo(string $pseudo) : User|false {
        $query = self::execute("SELECT * FROM users where pseudo = :pseudo", ["pseudo"=>$pseudo]);
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["id"], $data["full_name"], $data["email"], $data["pseudo"], $data["password"], $data["role"], $data["picture_path"], $data["iban"]);
        }
    }

    public static function get_users_by_email(string $email) : User|false {
        $query = self::execute("SELECT * FROM users where email = :email", ["email"=>$email]);
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["id"], $data["full_name"], $data["email"], $data["pseudo"], $data["password"], $data["role"], $data["picture_path"], $data["iban"]);
        }
    }

    public static function get_users() : array {
        $query = self::execute("SELECT * FROM users", []);
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $results[] = new User($row["id"], $row["full_name"], $row["email"], $row["pseudo"], $row["password"], $row["role"], $row["picture_path"], $row["iban"]);
        }
        return $results;
    }

    public static function get_users_by_id(int $id) : User|false {
        $query = self::execute("SELECT * FROM users where id = :id", ["id"=>$id]);
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["id"], $data["full_name"], $data["email"], $data["pseudo"], $data["password"], $data["role"], $data["picture_path"], $data["iban"]);
        }
    }

    public function get_my_other_items() : array{
        return Item::get_other_items_from_user($this->getId());
    }

    public function get_my_bids_items() : array{
        return Item::get_bids_items_from_user($this->getId());
    }

    public function get_my_items() : array{
        return Item::get_user_items($this->getId());
    }

    public function get_my_closed_unsold_items() : array{
        return Item::get_user_closed_unsold_items($this->getId());
    }

    public function get_my_sold_items() : array{
        return Item::get_user_sold_items($this->getId());
    }

    public function get_my_sales() : array{
        return Item::get_user_completed_sales($this->getId());
    }

    public function get_my_purchases_top_bidder() : string{
        return Item::get_top_bidder($this->getId());
    }

    public function get_my_sales_total_and_average() : array{
        return Item::get_total_and_average_of_sales($this->getId());
    }

    public function get_my_purchases() : array{
        return Item::get_user_purchases($this->getId());
    }

    public function get_my_purchases_top_seller() : string{
        return Item::get_top_seller($this->getId());
    }

    public function get_my_purchases_total_and_average() : array{
        return Item::get_total_and_average_of_purchases($this->getId());
    }
    
    // private static function validate_password(string $password) : array {
    //     $errors = [];
    //     if (strlen($password) < 8 || strlen($password) > 16) {
    //         $errors[] = "Password length must be between 8 and 16.";
    //     } if (!((preg_match("/[A-Z]/", $password)) && preg_match("/\d/", $password) && preg_match("/['\";:,.\/?!\\-]/", $password))) {
    //         $errors[] = "Password must contain one uppercase letter, one number and one punctuation mark.";
    //     }
    //     return $errors;
    // }
    
    // public static function validate_passwords(string $password, string $password_confirm) : array {
    //     $errors = User::validate_password($password);
    //     if ($password != $password_confirm) {
    //         $errors[] = "You have to enter twice the same password.";
    //     }
    //     return $errors;
    // }
    
    public static function validate_unicity_pseudo(string $pseudo) : array {
        $errors = [];
        $member = self::get_users_by_pseudo($pseudo);
        if ($member) {
            $errors[] = "This user already exists.";
        } 
        return $errors;
    }

    public static function validate_unicity_email(string $email) : array {
        $errors = [];
        $member = self::get_users_by_email($email);
        if ($member) {
            $errors[] = "This email already exists.";
        } 
        return $errors;
    }
    

    private static function check_password(string $clear_password, string $hash) : bool {
        return password_verify($clear_password, $hash);
    }

    // public function validate() : array {
    //     $errors = [];
    //     if (!strlen($this->pseudo) > 0) {
    //         $errors[] = "Pseudo is required.";
    //     } if (!(strlen($this->pseudo) >= 3 && strlen($this->pseudo) <= 16)) {
    //         $errors[] = "Pseudo length must be between 3 and 16.";
    //     } if (!(preg_match("/^[a-zA-Z][a-zA-Z0-9]*$/", $this->pseudo))) {
    //         $errors[] = "Pseudo must start by a letter and must contain only letters and numbers.";
    //     }
    //     return $errors;
    // }
    
    public static function validate_login(string $email, string $password) : array {
        $errors = [];
        $member = User::get_user_by_email($email);
        if ($member) {
            if (!self::check_password($password, $member->hashed_password)) {
                $errors[] = "Wrong password. Please try again.";
            }
        } else {
            $errors[] = "Can't find a member with the e-mail '$email'. Please sign up.";
        }
        return $errors;
    }

    public static function get_user_by_email(string $email) : User|false {
        $query = self::execute("SELECT * FROM users where email = :email", ["email"=>$email]);
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["id"], $data["full_name"], $data["email"], $data["pseudo"], $data["password"], $data["role"], $data["picture_path"], $data["iban"]);
        }
    }

    public function is_bidder(Item $item) : bool{
        $query = self::execute("SELECT b.owner 
                                FROM bids b
                                    join items i on b.item = i.id
                                where i.id = :itemId
                                and  b.owner = :owner;", ["itemId"=>$item->getId(), "owner"=>$this->getId()]);
        $result = $query->fetchAll();
        return !empty($result);
    }

    public static function validate_username(string $username): array {
        $errors = [];

        if ($username === '') {
            $errors[] = "Username is required.";
            return $errors;
        }

        // Longueur
        if (strlen($username) < 3 || strlen($username) > 30) {
            $errors[] = "Username must be between 3 and 30 characters.";
        }

        // Unicité
        $errors = array_merge(
            $errors,
            self::validate_unicity_pseudo($username)
        );
        return $errors;
    }

    public static function validate_full_name(string $fullName): array {
        $errors = [];

        if ($fullName === '') {
            $errors[] = "Full name is required.";
            return $errors;
        }

        // Longueur
        if (strlen($fullName) < 3 ) {
            $errors[] = "Full name must have more than 3 characters.";
        }

        return $errors;
    }

    public static function validate_email(string $email): array {
        $errors = [];

        if ($email === '') {
            $errors[] = "Email is required.";
            return $errors;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Must be a valid email address.";
        }
    

        return $errors;
    }

    public static function validate_iban(string $iban): array {
        $errors = [];

        if (!empty($iban)) {
            $iban = str_replace(" ", "",$iban);

            $len = strlen($iban);
            // if ($len < 14 || $len > 34) {
            //     $errors[] = "IBAN must be between 14 and 34 characters.";
            // }

            if (!preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]+$/', $iban) || ($len < 14 || $len > 34)) {
                $errors[] = "IBAN format is invalid.";
            }
        }
    

        return $errors;
    }

    // public static function validate_photo(array $file) : array {
    //     $errors = [];
    //     if (isset($file['name']) && $file['name'] != '') {
    //         if ($file['error'] == 0) {
    //             $valid_types = ["image/gif", "image/jpeg", "image/png"];
    //             if (!in_array($_FILES['image']['type'], $valid_types)) {
    //                 $errors[] = "Unsupported image format : gif, jpg/jpeg or png.";
    //             }
    //         } else {
    //             $errors[] = "Error while uploading file.";
    //         }
    //     }
    //     return $errors;
    // }

    // //pre : validate_photo($file) returns true
    // public function generate_photo_name() : string {
    //     //note : time() est utilisé pour que la nouvelle image n'aie pas
    //     //       le meme nom afin d'éviter que le navigateur affiche
    //     //       une ancienne image présente dans le cache
    //     if ($_FILES['image']['type'] == "image/gif") {
    //         $saveTo = $this->pseudo . time() . ".gif";
    //     } else if ($_FILES['image']['type'] == "image/jpeg") {
    //         $saveTo = $this->pseudo . time() . ".jpg";
    //     } else if ($_FILES['image']['type'] == "image/png") {
    //         $saveTo = $this->pseudo . time() . ".png";
    //     }
    //     return $saveTo;
    // }

}
