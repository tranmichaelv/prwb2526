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

    // public function persist() : User {
    //     if(self::get_users_by_pseudo($this->pseudo))
    //         self::execute("UPDATE users SET full_name=:full_name, email=:email; pseudo=:pseudo, password=:password, role=:role, picture_path=:picture, iban=:iban WHERE pseudo=:pseudo ", 
    //                       ["full_name"=>$this->full_name, "email"=>$this->email, "pseudo"=>$this->pseudo, "picture"=>$this->picture_path, "password"=>$this->hashed_password, "iban"=>$this->iban]);
    //     else
    //         self::execute("INSERT INTO users(full_name, email, pseudo, password, role, picture_path, iban) VALUES(:pseudo,:password,:profile,:picture_path)", 
    //                       ["full_name"=>$this->full_name, "email"=>$this->email, "pseudo"=>$this->pseudo, "picture"=>$this->picture_path, "password"=>$this->hashed_password, "iban"=>$this->iban]);
    //     return $this;
    // }

    public static function get_user_by_pseudo(string $pseudo) : User|false {
        $query = self::execute("SELECT * FROM users where pseudo = :pseudo", ["pseudo"=>$pseudo]);
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
    
    // public static function validate_unicity(string $pseudo) : array {
    //     $errors = [];
    //     $member = self::get_users_by_pseudo($pseudo);
    //     if ($member) {
    //         $errors[] = "This user already exists.";
    //     } 
    //     return $errors;
    // }

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
