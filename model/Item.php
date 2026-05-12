<?php

require_once "framework/Model.php";
require_once "framework/Configuration.php";
require_once "utils/AppTime.php";
require_once "model/Category.php";


class Item extends Model {

     private ?float $instant_purchase = null;
     private ?float $sale_price = null;  
     private array $categories = [];

    public function __construct(
        private ?int $id,
        private string $title,
        private string $description,
        private int $owner,
        private int $duration_days,
        private ?float $starting_bid = null,
        private ?float $buy_now_price = null, // valeur finale dans la BD
        private ?string $created_at = null,
            

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
    public function get_instant_purchase(): ?float {
          return $this->instant_purchase; 
    }
    public function set_instant_purchase(?float $v): void {
          $this->instant_purchase = $v; 
    }
    public function get_sale_price(): ?float {
          return $this->sale_price; 
    }
    public function set_sale_price(?float $v): void {
          $this->sale_price = $v; 
    }
    
    public function get_categories() : array {
        if ($this->id === null) {
            return [];
        }

        $sql = "SELECT c.* 
                FROM categories c 
                JOIN item_categories ic ON ic.category = c.id
                WHERE ic.item = :item
                ORDER BY c.name";

        $stmt = self::execute($sql, [
            ":item" => $this->id
        ]);

        $rows = $stmt->fetchAll();

        $categories = [];

        foreach($rows as $row) {
            $categories[] = new Category(
                id: $row["id"],
                name: $row["name"],
                priority: $row["priority"]
            );
        }

        return $categories;
    }

    public function set_categories(array $categories): void {
        $this->categories = $categories;
    }


   public function validate(): array {
    $errors = [];

    $title = trim($this->title);
    $desc = trim($this->description);

    $title_min    = (int) Configuration::get("title_min_length");
    $title_max    = (int) Configuration::get("title_max_length");
    $desc_min     = (int) Configuration::get("description_min_length");
    $dur_days_min = (int) Configuration::get("duration_days_min");
    $dur_days_max = (int) Configuration::get("duration_days_max");

    $this->validate_title($errors, $title, $title_min, $title_max);
    $this->validate_description($errors, $desc, $desc_min);
    $this->validate_duration_days($errors, $dur_days_min, $dur_days_max);
    $this->validate_prices($errors);
    $this->validate_categories($errors);

    $result = $this->validate_sale_mode($errors);
    if ($result !== null) {
        return $result;
    }

    return $errors;
}

private function validate_title(array &$errors, string $title, int $title_min, int $title_max): void {
    if (mb_strlen($title) < $title_min || mb_strlen($title) > $title_max) {
        $errors['title'][] = "Title length must be between $title_min and $title_max characters";
    } elseif (self::title_exists_for_owner($title, $this->owner, $this->id)) {
        $errors['title'][] = "You already have an ad with this title.";
    }
}

private function validate_description(array &$errors, string $desc, int $desc_min): void {
    if ($desc !== "" && mb_strlen($desc) < $desc_min) {
        $errors['description'][] = "Description length must be at least $desc_min characters.";
    }
}

private function validate_duration_days(array &$errors, int $dur_days_min, int $dur_days_max): void {
    if ($this->duration_days < $dur_days_min || $this->duration_days > $dur_days_max) {
        $errors['duration_days'][] = "Duration days must be between $dur_days_min and $dur_days_max";
    }
}

private function validate_prices(array &$errors): void {
    if ($this->starting_bid !== null && $this->starting_bid < 0) {
        $errors['starting_bid'][] = "Starting bid cannot be negatif.";
    }

    if ($this->buy_now_price !== null && $this->buy_now_price < 0) {
        $errors['buy_now_price'][] = "Buy now price cannot be negatif.";
    }

    if ($this->starting_bid !== null && !self::fits_decimal_10_2($this->starting_bid)) {
        $errors['starting_bid'][] = "Starting bid is too high.";
    }

    if ($this->buy_now_price !== null && !self::fits_decimal_10_2($this->buy_now_price)) {
        $errors['buy_now_price'][] = "Buy now price is too high.";
    }
}

private function validate_sale_mode(array &$errors): ?array {
    $has_bid = $this->starting_bid !== null && $this->starting_bid > 0;
    $has_instant = $this->instant_purchase !== null && $this->instant_purchase > 0;
    $has_sale = $this->sale_price !== null && $this->sale_price > 0;

    if ($has_bid && $has_sale) {
        $errors['starting_bid'][] = "Cannot create both auction and direct sale.";
        $errors['sale_price'][] = "Cannot create both auction and direct sale.";
    }

    if ($has_bid) {
        if ($has_instant && $has_bid && $this->instant_purchase <= $this->starting_bid) {
            $errors['buy_now_price'][] = "Buy now price must be strictly higher than the starting bid";
        }

        $this->buy_now_price = $has_instant ? $this->instant_purchase : null;
    } else {
        if (!$has_sale) {
            $errors['sale_price'][] = "An ad without a bid must have a buy now price.";
            return $errors;
        }

        $this->buy_now_price = $this->sale_price;
    }

    return null;
}

    public function persist(): array {
     $errors =$this->validate();
     if (!empty($errors)){
          return $errors;
     }
          // on convertit les null en 0 pour la DB
     $starting_db = $this->starting_bid ?? 0.0;
     $buy_now_db  = $this->buy_now_price ?? null;

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

          $this->save_categories();
        return [];
    }

    public function delete(): void {
        if ($this->id === null) return;

        // récuperer les images (pour supp les fichiers)
        $pics = ItemPicture::get_by_item($this->id);

        foreach ($pics as $p){
          $main = $p->get_picture_path();
          $thumb = preg_replace('/\.jpg$/', '_thumbnail.jpg', $main);

          if (file_exists($main)) unlink($main);
          if (file_exists($thumb)) unlink($thumb);
        }

        self::execute("DELETE FROM item_pictures WHERE item = :id",[":id" => $this->id]);
            
        self::execute("DELETE FROM bids WHERE item = :id", [":id" => $this->id]);

        self::execute("DELETE FROM item_categories WHERE item =:id", [":id" => $this->id]);

        self::execute("DELETE FROM items WHERE id = :id", [":id" => $this->id]);

        $this->id = null;
    }

    // récupère un item depuis la bd à partir de son id ou return null
    public static function get_by_id(int $id): ?Item {
          $sql = "SELECT * FROM items WHERE id = :id";
          $stmt = self::execute($sql, [":id" => $id]);
          $row = $stmt->fetch();
          if (!$row) return null;

         $item = new Item(
               id: (int)$row["id"],
               title: $row["title"],
               description: $row["description"] ?? "",
               owner: (int)$row["owner"],
               duration_days: (int)$row["duration_days"],
               starting_bid: $row["starting_bid"] !== null ? (float)$row["starting_bid"] : null,
               buy_now_price: $row["buy_now_price"] !== null ? (float)$row["buy_now_price"] : null,
               created_at: $row["created_at"]
          );

          $starting = (float)$row["starting_bid"];
          $buy = $row["buy_now_price"] !== null ? (float)$row["buy_now_price"] : null;

          if ($starting > 0) {
               // Auction : buy_now_price = instant purchase
               $item->set_instant_purchase($buy);
               $item->set_sale_price(null);
          } else {
               // Direct sale : buy_now_price = sale price
               $item->set_sale_price($buy);
               $item->set_instant_purchase(null);
            }

       return $item;
    }



    //  Vérifie si un titre existe déjà pour un owner
    public static function title_exists_for_owner(string $title, int $owner, ?int $exclude_id = null) : bool {
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

    private function save_categories() : void {
        self::execute("DELETE FROM item_categories WHERE item = :item",
                      [":item" => $this->id]
        );

        foreach($this->categories as $category_id) {
            self::execute("INSERT INTO item_categories(item, category)
                         VALUES (:item, :category)", 
                         [
                            ":item" => $this->id,
                            ":category" => $category_id
                         ]
            );
        }
    }

    private function validate_categories( array &$errors) : void {
      

        $categories = $this->categories;
        $categories_max_choice = Configuration::get("categories_max_choice");

        if(count($categories) > $categories_max_choice) {
            $errors['categories_choice'][] = "You can select at most $categories_max_choice categories"; 
        }
        
    }

    public function has_category(int $category_id) : bool {
        foreach($this->get_categories() as $category) {
            if($category->get_id() === $category_id) {
                return true;
            }
        }
        return false;
    }

}