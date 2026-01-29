<?php

require_once "framework/Model.php";
require_once "framework/Controller.php";
require_once "framework/Configuration.php";
require_once "model/ItemStatus.php";

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


    // -------- Persist --------
    public function persist() : User {
        if(self::get_users_by_id($this->id))
            self::execute("UPDATE users SET full_name=:full_name, email=:email, pseudo=:pseudo, password=:password, role=:role, picture_path=:picture_path, iban=:iban WHERE id=:id ", 
                          ["id"=>$this->id, "full_name"=>$this->full_name, "email"=>$this->email, "pseudo"=>$this->pseudo, "picture_path"=>$this->picture_path, "password"=>$this->hashed_password, "role"=>$this->role, "iban"=>$this->iban]);
        else
            self::execute("INSERT INTO users(full_name, email, pseudo, password, role) VALUES(:full_name, :email, :pseudo, :password, :role)", 
                          ["full_name"=>$this->full_name, "email"=>$this->email, "pseudo"=>$this->pseudo, "password"=>$this->hashed_password, "role"=>$this->role]);
        return $this;
    }


    // -------- All of the get_users --------
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


    // -------- Item's requests --------
    public function get_my_other_items() : array{
        return ItemStatus::get_other_items_from_user($this->getId());
    }

    public function get_my_bids_items() : array{
        return ItemStatus::get_bids_items_from_user($this->getId());
    }

    public function get_my_items() : array{
        return ItemStatus::get_user_items($this->getId());
    }

    public function get_my_closed_unsold_items() : array{
        return ItemStatus::get_user_closed_unsold_items($this->getId());
    }

    public function get_my_sold_items() : array{
        return ItemStatus::get_user_sold_items($this->getId());
    }

    public function get_my_sales() : array{
        return ItemStatus::get_user_completed_sales($this->getId());
    }

    public function get_my_purchases_top_bidder() : string{
        return ItemStatus::get_top_bidder($this->getId());
    }

    public function get_my_sales_total_and_average() : array{
        return ItemStatus::get_total_and_average_of_sales($this->getId());
    }

    public function get_my_purchases() : array{
        return ItemStatus::get_user_purchases($this->getId());
    }

    public function get_my_purchases_top_seller() : string{
        return ItemStatus::get_top_seller($this->getId());
    }

    public function get_my_purchases_total_and_average() : array{
        return ItemStatus::get_total_and_average_of_purchases($this->getId());
    }
    

    // -------- Validate user's informations --------
    public static function validate_password(string $password, string $password_confirm) : array {
        $errors = [];

        $minLength = Configuration::get("password_min_length");
        $maxLength =  Configuration::get("varchar_max_length");
        if (strlen($password) < $minLength) {
            $errors[] = "Password length must be at least " . $minLength . " characters.";
        } 

        if (strlen($password) > $maxLength) {
            $errors[] = "Password must have less than " . $maxLength . " characters.";
        } 

        if (!((preg_match("/[A-Z]/", $password)) && preg_match("/\d/", $password) && preg_match("/[^a-zA-Z0-9]/", $password))) {
            $errors[] = "Password must contain one uppercase letter, one number and one special character.";
        }
        if ($password != $password_confirm) {
            $errors[] = "You have to enter twice the same password.";
        }

        return $errors;

    }
    
    public function validate_passwords(string $newPassword, string $password_confirm) : array {
        $errors = [];

        $errors = User::validate_password($newPassword, $password_confirm);
        if (empty($errors)) {
            $this->setHashedPassword(password_hash($newPassword,PASSWORD_BCRYPT));
        }

        return $errors;
    }

    public static function validate_login(string $email, string $password) : array {
        $errors = [];
        $member = User::get_users_by_email($email);
        if ($member) {
            if (!self::check_password($password, $member->hashed_password)) {
                $errors[] = "Wrong password. Please try again.";
            }
        } else {
            $errors[] = "Can't find a member with the e-mail '$email'. Please sign up.";
        }
        return $errors;
    }

    public function validate_update(string $fullName, string $username , string $email, string $iban): array{
        $errors = [
            "full_name" => [],
            "username"  => [],
            "email"     => [],
            "iban"      => []
        ];

        if ($fullName !== $this->getFullName()) {
            $errors["full_name"] = $this->validate_full_name($fullName);

            if (empty($errors["full_name"])) {
                $this->setFullName($fullName);
            }
        }

        if ($username !== $this->getPseudo()) {
            $errors["username"] = $this->validate_username($username);

            if (empty($errors["username"])) {
                $this->setPseudo($username);
            }
        }
        
        if ($email !== $this->getEmail()) {
            $errors["email"] = $this->validate_email($email);

            if (empty($errors["email"])) {
                $this->setEmail($email);
            }
        }
        
        if ($iban !== $this->getIban()) {
            $errors["iban"] = $this->validate_iban($iban);

            if (empty($errors["iban"])) {
                $this->setIban($iban);
            }
        }

        return $errors;
    }

    public function validate_change_password(string $currentPassword, string $newPassword , string $confirmNewPassword): array{
        $errors = [
            "current_password" => [],
            "new_password"  => [],
        ];

        $errors['current_password'] = User::validate_login($this->getEmail(), $currentPassword);
        $errors['new_password'] = $this->validate_passwords($newPassword,$confirmNewPassword);

        return $errors;
    }

    public static function validate_username(string $username): array {
        $errors = [];

        if ($username === '') {
            $errors[] = "Username is required.";
            return $errors;
        }

        $minLength = Configuration::get("username_min_length");
        $maxLength =  Configuration::get("username_max_length");
        if (strlen($username) < $minLength || strlen($username) > $maxLength) {
            $errors[] = "Username must be between " . $minLength . " and " . $maxLength . " characters.";
        }

        // Unicité
        $errors = array_merge(
            $errors,
            self::validate_unicity_pseudo($username)
        );
        return $errors;
    }

    private static function validate_varchar_length(string $string, string $varchar): array{
        $errors = [];

        $minLength = Configuration::get("varchar_min_length");
        $maxLength =  Configuration::get("varchar_max_length");
        if (strlen($varchar) <  $minLength) {
            $errors[] = $string . " must have more than " . $minLength ." characters.";
        }

        if (strlen($varchar) > $maxLength ) {
            $errors[] = $string . " must have less than " . $maxLength . " characters.";
        }
        return $errors;
    }

    public static function validate_full_name(string $fullName): array {
        $errors = [];

        if ($fullName === '') {
            $errors[] = "Full name is required.";
            return $errors;
        }

        $errors = array_merge(
            $errors,
            self::validate_varchar_length("Full name", $fullName)
        );

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

        $errors = array_merge(
            $errors,
            User::validate_varchar_length("Email", $email)
        );
    

        return $errors;
    }

    public static function validate_iban(string $iban): array {
        $errors = [];

        if (!empty($iban)) {
            $iban = str_replace(" ", "",$iban);

            $len = strlen($iban);

            if (!preg_match('/^[A-Z]{2}[0-9]{14}/', $iban) || $len !== 16) {
                $errors[] = "IBAN doit être de forme BE99 9999 9999 9999.";
            }
        }
    

        return $errors;
    }

    public static function validate_photo(array $file) : array {
        $errors = []; 
        if (isset($file['name']) && $file['name'] != '') { 
            if ($file['error'] == 0) { 

                $info = getimagesize($file['tmp_name']);
                if ($info === false) {
                    $errors[] = "Le fichier n'est pas une image valide.";
                    return $errors;
                }

                $valid_types = ["image/gif", "image/jpeg", "image/png", "image/webp"]; 
                if (!in_array($info["mime"], $valid_types)) { 
                    $errors[] = "Format non autorisé (gif, jpg, png, webp uniquement)."; 
                } 

                $file_max_size = Configuration::get("file_max_size");
                if ($file['size'] > $file_max_size) {
                    $errors[] = "L'image ne doit pas dépasser ". $file_max_size/1024/1024 ." MB.";
                }
            } 
            else { 
                $errors[] = "Erreur lors du chargement de l'image."; 
            } 
        } 
        return $errors;
    }


    // -------- UNICITY --------
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

    public function is_bidder(ItemStatus $item) : bool{
        $query = self::execute("SELECT b.owner 
                                FROM bids b
                                    join items i on b.item = i.id
                                where i.id = :itemId
                                and  b.owner = :owner;", ["itemId"=>$item->getId(), "owner"=>$this->getId()]);
        $result = $query->fetchAll();
        return !empty($result);
    }

    
    private function create_image(array $file){
        $info = getimagesize($file['tmp_name']);
        if ($info["mime"] == "image/gif") {
            return imagecreatefromgif($file['tmp_name']);
        } else if ($info["mime"] == "image/jpeg") {
            return imagecreatefromjpeg($file['tmp_name']);
        } else if ($info["mime"] == "image/png") {
            return imagecreatefrompng($file['tmp_name']);
        } else if ($info["mime"] == "image/webp") {
            return imagecreatefromwebp($file['tmp_name']);
        }
    }

    public function upload_picture_path(array $file) : array {
        $errors = self::validate_photo($file);
        if (!empty($errors)) {
            return $errors;
        }

        $image = $this->create_image($file);
        if (!$image) {
            return ["Impossible de traiter l'image (GD)."];
        } else {
            $principal = $this->resize_image($image, Configuration::get("img_max_width"), Configuration::get("img_max_height"), 90);
            $thumbnail = $this->resize_image($image, Configuration::get("img_thumbnail_max_width"), Configuration::get("img_thumbnail_max_height"), 90);
        }

        $dir = "uploads/users/" . $this->getId();
        self::create_or_clean_directory($dir);


        $fileName = pathinfo($file['name'], PATHINFO_FILENAME);
        $principal_path = $dir . "/" .  $fileName . ".jpg";
        $thumbnail_path = $dir . "/" .  $fileName . "_thumbnail" . ".jpg";

        imagejpeg($principal, $principal_path);
        imagejpeg($thumbnail, $thumbnail_path);

        $this->setPicturePath($principal_path);
        $this->persist();

        return [];
    }

    private static function resize_image($image, int $maxWidth, int $maxHeight){
        $oldWidth = imagesx($image);
        $oldHeight = imagesy($image);

        $ratio = min($maxWidth / $oldWidth, $maxHeight / $oldHeight, 1);

        $newWidht = (int) ($oldWidth * $ratio);
        $newHeight = (int) ($oldHeight * $ratio);

        $temp = imagecreatetruecolor($newWidht, $newHeight);

        imagecopyresampled($temp, $image, 0, 0, 0, 0, $newWidht, $newHeight, $oldWidth, $oldHeight);
        
        return $temp;
    }

    public static function create_or_clean_directory($dir): void{
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        } else {
            // foreach (glob($dir . "/*.jpg") as $file) {
            foreach (glob($dir . "/*") as $files) {
                unlink($files);
            }
        }
    }

    public function delete_picture_path(){
        $dir = "uploads/users/" . $this->getId();
        
        $files = glob($dir . "/*"); 
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        rmdir($dir);

        $this->setPicturePath(null);
        $this->persist();
    }

    // A véririfer sans fonctionnement !
    public static function get_owner_infos_by_item_id(int $id) : array {

        $query=self::execute(
            "SELECT u.*
            FROM v_items_status v join users u on v.owner = u.id 
            WHERE v.id = :id", 

            ["id" => $id]
        );

        $ownerInfo  = $query->fetch();
        return $ownerInfo;
        /*
        return new User(
            $owner["id"],
            $owner["full_name"],
            $owner["email"],
            $owner["pseudo"],
            $owner["password"],
            $owner["role"]
        );*/
    }

}
