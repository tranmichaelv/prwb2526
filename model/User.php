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

    public function get_list() : array{
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

        $min_length = Configuration::get("password_min_length");
        $max_length =  Configuration::get("varchar_max_length");
        if (strlen($password) < $min_length) {
            $errors[] = "Password length must be at least " . $min_length . " characters.";
        } 

        if (strlen($password) > $max_length) {
            $errors[] = "Password must have less than " . $max_length . " characters.";
        } 

        if (!((preg_match("/[A-Z]/", $password)) && preg_match("/\d/", $password) && preg_match("/[^a-zA-Z0-9]/", $password))) {
            $errors[] = "Password must contain one uppercase letter, one number and one special character.";
        }
        if ($password != $password_confirm) {
            $errors[] = "You have to enter twice the same password.";
        }

        return $errors;

    }
    
    public function validate_passwords(string $new_password, string $password_confirm) : array {
        $errors = [];

        $errors = User::validate_password($new_password, $password_confirm);
        if (empty($errors)) {
            $this->setHashedPassword(password_hash($new_password,PASSWORD_BCRYPT));
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

    public function validate_update(string $full_name, string $username , string $email, string $iban): array{
        $errors = [
            "full_name" => [],
            "username"  => [],
            "email"     => [],
            "iban"      => []
        ];

        if ($full_name !== $this->getFullName()) {
            $errors["full_name"] = $this->validate_full_name($full_name);

            if (empty($errors["full_name"])) {
                $this->setFullName($full_name);
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

    public function validate_change_password(string $current_password, string $new_password , string $confirm_new_password): array{
        $errors = [
            "current_password" => [],
            "new_password"  => [],
        ];

        $errors['current_password'] = User::validate_login($this->getEmail(), $current_password);
        $errors['new_password'] = $this->validate_passwords($new_password,$confirm_new_password);

        return $errors;
    }

    public static function validate_username(string $username): array {
        $errors = [];

        if ($username === '') {
            $errors[] = "Username is required.";
            return $errors;
        }

        $min_length = Configuration::get("username_min_length");
        $max_length =  Configuration::get("username_max_length");
        if (strlen($username) < $min_length || strlen($username) > $max_length) {
            $errors[] = "Username must be between " . $min_length . " and " . $max_length . " characters.";
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

        $min_length = Configuration::get("varchar_min_length");
        $max_length =  Configuration::get("varchar_max_length");
        if (strlen($varchar) <  $min_length) {
            $errors[] = $string . " must have more than " . $min_length ." characters.";
        }

        if (strlen($varchar) > $max_length ) {
            $errors[] = $string . " must have less than " . $max_length . " characters.";
        }
        return $errors;
    }

    public static function validate_full_name(string $full_name): array {
        $errors = [];

        if ($full_name === '') {
            $errors[] = "Full name is required.";
            return $errors;
        }

        $errors = array_merge(
            $errors,
            self::validate_varchar_length("Full name", $full_name)
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


        $file_name = pathinfo($file['name'], PATHINFO_FILENAME);
        $principal_path = $dir . "/" .  $file_name . ".jpg";
        $thumbnail_path = $dir . "/" .  $file_name . "_thumbnail" . ".jpg";

        imagejpeg($principal, $principal_path);
        imagejpeg($thumbnail, $thumbnail_path);

        $this->setPicturePath($principal_path);
        $this->persist();

        return [];
    }

    private static function resize_image($image, int $max_width, int $max_height){
        $old_width = imagesx($image);
        $old_height = imagesy($image);

        $ratio = min($max_width / $old_width, $max_height / $old_height, 1);

        $new_widht = (int) ($old_width * $ratio);
        $new_height = (int) ($old_height * $ratio);

        $temp = imagecreatetruecolor($new_widht, $new_height);

        imagecopyresampled($temp, $image, 0, 0, 0, 0, $new_widht, $new_height, $old_width, $old_height);
        
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

    public function delete_picture_path() : void{
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


    

    /*======  Propriétaire de l'item  ======*/
    public static function get_owner_by_item_id($id) : User {

        $query = self::execute(
            "SELECT * 
            FROM v_items_status as vs JOIN users as u on u.id = vs.owner
            WHERE vs.id = :id", ['id' => $id]);

        $owner = $query->fetch(); 

        if ($query->rowCount() == 0){
            die ("User non trouvé !!!!!");
        } else {
            return new User(
                $owner["id"], 
                $owner["full_name"],
                $owner["email"],
                $owner["pseudo"],
                $owner["password"],
                $owner["role"],
                $owner["picture_path"],
                $owner["iban"],
            );
        }
    }

}
