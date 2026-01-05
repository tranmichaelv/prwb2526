<?php 

/*
require_once 'model/Item.php';
require_once "model/User.php";
require_once "model/Picture.php";
require_once "model/ItemSaleStatus.php";

require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'utils/AppTime.php';

class ControllerOpenItem extends Controller {

    private ?ItemSaleStatus $bid = null;

    public function index(): void {
        $this->itemDetails();
    }

    public function itemDetails(): void {
        $id = $_GET["param1"] ?? null;
        
        $errors = [];
        //print_r($id);
        //die();
        if ($id == null) {
            $errors[] = "Le controller a besoin d'un id pour charger la vue open item";
        }

        $current_user = $this->get_user_or_redirect();

        $itemData = $this->loadItemData($id, $current_user);
        $bidData = $this->loadBidData($id, $current_user);

        // Fusion des erreurs
        $errors = array_merge($errors, $itemData['errors'], $bidData['errors']);
        $itemData['errors'] = $errors;
        $bidData['errors'] = []; // éviter écrasement lors du merge

        
        (new View("open_item"))->show(array_merge($itemData, $bidData));
    }

    private function get_bid(int $id): ?ItemSaleStatus {
        if ($this->bid === null) {
            $this->bid = ItemSaleStatus::get_current_bid_by_item_id($id);
        }
        return $this->bid;
    }
    
    private function get_current_time(): ?string {
        return AppTime::get_current_datetime();
    }

    /*========= Chargements des données de l'Item ===========*/
    /*
    private function loadItemData(int $id, ?User $current_user): array {
        $errors = [];

        $item = Item::get_item_by_id($id);
        $main_picture = $item?->get_main_picture_by_item_id();
        $all_pictures = $item?->get_all_pictures_by_item_id();

        if (!$item) $errors[] = "Aucun item retrouvé";
        if (!$main_picture) $errors[] = "Aucune image principale";
        if (!$all_pictures) $errors[] = "Pas d'images retrouvées pour cet item";

        return [
            'item' => $item,
            'main_picture' => $main_picture,
            'pictures' => $all_pictures,
            'user' => $current_user,
            'errors' => $errors
        ];
    }

    /*======== Chargement des données de l'enchère =========*/
    /*
    private function loadBidData(int $id, ?User $current_user): array {
        $errors = [];
        $bid = $this->get_bid($id);

        if (!$bid) {
            $errors[] = "Aucune enchère pour le moment";
        }

        $seller = $bid?->get_seller_information();
        if (!$seller) $errors[] = "Aucune information sur le vendeur";

        $bids_history = $bid?->get_bids_history();
        if (!$bids_history) $errors[] = "Problème avec l'historique des enchères";

        return [
            'current_bid' => $bid?->getCurrentBid(),
            'starting_bid' => $bid?->getStartingBid(),
            'bid_is_closed' => $bid?->bid_is_closed() ?? true,
            'user_is_owner' => $current_user ? $bid?->user_is_owner($current_user) : false,
            'has_buy_now' => $bid?->buy_now_price_exist() ?? false,
            'has_bids' => $bid?->has_bids() ?? false,
            'buy_now_reached' => $bid?->buy_now_reached() ?? false,
            'is_direct_sale' => $bid?->is_direct_sale() ?? false,
            'seller' => $seller,
            'bids_history' => $bids_history,
            'errors' => $errors
        ];
    }

}*/
?>
