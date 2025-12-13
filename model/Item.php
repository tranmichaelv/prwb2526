<?php

require_once "framework/Model.php";
require_once "framework/Configuration.php";
require_once "utils/AppTime.php";


class Item extends Model {

    public function __construct(
        private ?int $id,
        private string $title,
        private string $description,
        private int $owner,
        private int $duration_days,
        private ?float $starting_bid = null,
        private ?float $buy_now_price = null,
        private ?string $created_at = null
    ) {
        $this->created_at = $this->created_at ?? AppTime::get_current_datetime();
      }

    public function get_id(): ?int {
         return $this->id; 
    }

    public function get_title(): string {
         return $this->title; 
    }

    public function set_title(string $title): void {
         $this->title = $title; 
     }

    public function get_description(): string {
         return $this->description; 
    }
    public function set_description(string $description): void { 
        $this->description = $description; 
    }

    public function get_owner_id(): int {
         return $this->owner; 
    }

    public function get_created_at(): string {
         return $this->created_at; 
    }

    public function get_duration_days(): int {
         return $this->duration_days; 
    }
    public function set_duration_days(int $d): void {
         $this->duration_days = $d; 
    }

    public function get_starting_bid(): ?float {
         return $this->starting_bid; 
    }
    public function set_starting_bid(?float $bid): void {
         $this->starting_bid = $bid; 
    }

    public function get_buy_now_price(): ?float {
         return $this->buy_now_price; 
    }
    public function set_buy_now_price(?float $price): void {
         $this->buy_now_price = $price; 
    }



    public function validate() : array {

        $errors = [];
         
        $title = trim($this->title);
        $desc = trim($this->description);

        $title_min    = (int) Configuration::get("title_min_length");
        $title_max    = (int) Configuration::get("title_max_length");
        $desc_min     = (int) Configuration::get("description_min_length");
        $dur_days_min = (int) Configuration::get("duration_days_min");
        $dur_days_max = (int) Configuration::get("duration_days_max");

        

          // title entre 3 et 255 carac, unique par owner 
        if(mb_strlen($title) < $title_min ||mb_strlen($title) > $title_max ){
               $errors['title'][] = "Title lenght must be between 3 and 255 characters";
        } elseif (self::title_exists_for_owner($title, $this->owner, $this->id)) {
               $errors['title'][] = "You already have an ad with this title.";
        }

        // si desc est remplie -> min 3 carac
        if( $desc !== "" && mb_strlen($desc) < $desc_min ){
               $errors['description'][] = "Description lenght must be at least 3 characters.";
        } 

        // duration days entre 1 et 365 jours
        if($this->duration_days < 1 || $this->duration_days > 365) {
               $errors ['duration_days'][] = "Duration days must be between 1 and 365";
        }

        // valeur ne peut pas etre nétagif
        if ($this->starting_bid !== null && $this->starting_bid < 0) {
               $errors['starting_bid'][] = "Starting bid cannot be negatif.";
        }


        if ($this->buy_now_price !== null && $this->buy_now_price < 0) {
               $errors['buy_now_price'][] = "Buy now price cannot be negatif.";
        }

        // la valeur doit avoir max 10 chiffres (dont 2 apres la virgule)
        if ( $this->starting_bid !== null && !self::fits_decimal_10_2($this->starting_bid)) {
               $errors["starting_bid"][] = "Starting bid is too high.";
        }


        if ( $this->buy_now_price !== null && !self::fits_decimal_10_2($this->buy_now_price)) {
               $errors['buy_now_price'][] = "Buy now price is too high.";
        }

        $has_bid = $this->starting_bid !== null && $this->starting_bid > 0;
        $has_buy_now = $this->buy_now_price !== null && $this->buy_now_price > 0;

        // si buy_now est rempli, il doit être > 0 et doit être > starting_bid (si pas null)
        if($has_buy_now && $has_bid && $this->buy_now_price <= $this->starting_bid) {
               $errors['buy_now_price'][] = "Buy now price must be strictly higher than the starting bid";
        }

        // Une annonce sans enchère doit avoir un buy_now_price
        if(!$has_bid && !$has_buy_now) {
               $errors['buy_now_price'][] = "An ad without a bid must have a buy now price";
        }

        return $errors;
    }

    public function persist(): array {
     $errors =$this->validate();
     if (!empty($errors)){
          return $errors;
     }
          // on convertit les null en 0 pour la DB
     $starting_db = $this->starting_bid ?? 0.0;
     $buy_now_db  = $this->buy_now_price ?? 0.0;

     if($this->id == null) {
          $sql = "INSERT INTO items
                        (title, description, owner, created_at,
                         buy_now_price, duration_days, starting_bid)
                    VALUES
                        (:title, :description, :owner, :created_at,
                         :buy_now_price, :duration_days, :starting_bid)";
          self::execute($sql, [
                ":title"         => $this->title,
                ":description"   => $this->description,
                ":owner"         => $this->owner,
                ":created_at"    => $this->created_at,
                ":buy_now_price" => $buy_now_db,
                ":duration_days" => $this->duration_days,
                ":starting_bid"  => $starting_db,
            ]);

            $this->id = self::lastInsertId();               

     
     }  else {
            $sql = "UPDATE items SET
                        title = :title,
                        description = :description,
                        buy_now_price = :buy_now_price,
                        duration_days = :duration_days,
                        starting_bid = :starting_bid
                    WHERE id = :id";
            self::execute($sql, [
                ":title"         => $this->title,
                ":description"   => $this->description,
                ":buy_now_price" => $buy_now_db,
                ":duration_days" => $this->duration_days,
                ":starting_bid"  => $starting_db,
                ":id"            => $this->id,
            ]);
          }

        return [];
    }

    public function delete(): void {
        if ($this->id === null) {
            return;
        }
        self::execute("DELETE FROM items WHERE id = :id", [":id" => $this->id]);
        $this->id = null;
    }

    //  Vérifie si un titre existe déjà pour un owner
    private static function title_exists_for_owner(string $title, int $owner, ?int $exclude_id = null) : bool {
          $sql = "SELECT COUNT(*) AS cnt FROM items
               WHERE owner = :owner AND title = :title";
          
          $params = [
               ":owner" => $owner,
               ":title" => $title,
          ];
          if($exclude_id !== null){
               $sql .= " AND id <> :id";
               $params[":id"] = $exclude_id;
          }  

          $stmt = self::execute($sql,$params);
          $row = $stmt->fetch();
          return $row && (int)$row["cnt"] > 0;
    }

    // respecter les 10 chiffres max (dont 2 après la virgule)
    private static function fits_decimal_10_2(float $value) : bool {
          $abs = abs($value);
          return $abs < 100000000 && round($abs,2) == $abs;
    }

}