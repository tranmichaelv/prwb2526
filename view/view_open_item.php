<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" 
          integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="/prwb_2526_c05/css/OpenItem.css" >
    <title>Item images</title>
</head>
<body>


    <!-- Un container de toute la page open item -->
<div class="page-container d-flex flex-row align-items-start mb-2">
        
<!-- left-container -->
    <div class="left-container d-flex flex-column gap-4 flex-grow-1">

        <section class="item-content">
            <!-- main-picture-container -->
            <div class="main-picture">
                <?php 
                    $baseUrl = '/prwb_2526_c05/';
                    $src = $baseUrl . $main_picture;

                    if ($src == null) {
                        $src = "";
                    }
                ?>

                <img src="<?= $src ?>" alt="Image principale de Item">
            </div>

            <div class="item-infos">
                <p>
                    <h2> <?= $item->getTitle(); ?> </h2>
                </p>
                <p> <?= $item->getDescription(); ?></p>

                <div class="startEndBid">
                    <p> start : <?= $item->getCreatedAt(); ?></p>
                    <p> End : <?= $item->getEndAt(); ?></p>
                </div>
            </div>
        </section>
        

        <section>
            <!-- additional-images-container -->
            <h6>Additional Images</h6>
            <div class="additional-images">
                <?php 
                    $baseUrl = '/prwb_2526_c05/';
                ?>
                <?php if (!empty($pictures)) : ?>
                    <?php foreach($pictures as $pic): ?>
                        <img src="<?=$baseUrl . $pic['picture_path'] ?>" alt="item image" class="thumbnail">
                    <?php endforeach; ?>   
                <?php else :?>
                    <p> Aucune image supplémentaire </p>
                <?php endif;?>        
            </div>


        </section>



        <section>
            <!-- bid-history-container -->
           <div class="bids-history">
                <div><p>Bid History</p></div>
                <div class="bids">
                     <?php if (!empty($bids_history)) :?>
                        <?php foreach($bids_history as $bid) :?>
                            <div class="bid">
                                <p class="owner"><?= htmlspecialchars($bid['pseudo']) ?></p>
                                <p class="amount"><?= "€" . number_format($bid['amount'], 2, ',', ' ') ?></p>
                                <p class="date"><?= htmlspecialchars($bid['created_at']) ?></p>
                            </div>
                        <?php endforeach ?>
                    <?php else : ?>
                        <p>No bids yet.</p>
                    <?php endif ?> 
                </div>    
            </div>

        </section>

    </div>



<!-- right container -->
    <div class="right-container d-flex flex-column gap-4 "> 

        <div class="pricing-container">
            <?php if (!$bid_is_closed && !$user_is_owner && !$is_direct_sale && !$has_bids) : ?>
            <!-- Cas 1 : Annonce ouverte, utilisateur non créateur, pas d'enchère, pas de Buy Now -->
                <div class="pricing-card open no-bid no-buynow">
                    <h6 class="section-title">Pricing</h6>
                    <div class="pricing-info">
                    <p class="label">Current Bid</p>
                    <p class="price highlight">€149,00</p>
                    <p class="starting-at">Starting at €149,00</p>
                    </div>
                    <form class="bid-form">
                    <div class="input-group">
                        <span class="currency">€</span>
                        <input type="number" name="new-bid" min="0" step="1" value="149" />
                    </div>
                    <button type="submit" class="btn btn-primary">Place Bid</button>
                    </form>

                    <div class="seller-info">
                    <h6 class="section-title">Seller Information</h6>
                    <div class="seller-details">
                        <div class="seller-avatar"></div>
                        <div>
                        <p class="seller-name">Quentin</p>
                        <p class="seller-role">Member</p>
                        </div>
                    </div>
                    </div>
                </div>
            <?php elseif (!$bid_is_closed && !$user_is_owner && $has_bids && !$buy_now_reached) :?>    
                <!-- Cas 2 : Annonce ouverte, utilisateur non créateur, enchères existantes, Buy Now non atteint -->
                <div class="pricing-card open bid-exists buynow-available">
                    <h6 class="section-title">Pricing</h6>
                    <div class="pricing-info">
                    <p class="label">Current Bid</p>
                    <p class="price highlight">€162,00</p>
                    <p class="label">Buy Now</p>
                    <p class="price">€275,00</p>
                    </div>
                    <form class="bid-form">
                    <div class="input-group">
                        <span class="currency">€</span>
                        <input type="number" name="new-bid" min="163" step="1" value="163" />
                    </div>
                    <button type="submit" class="btn btn-primary">Place Bid</button>
                    <button type="button" class="btn btn-outline-primary buy-now-btn">Buy Now at €275,00</button>
                    </form>

                    <div class="seller-info">
                    <h6 class="section-title">Seller Information</h6>
                    <div class="seller-details">
                        <div class="seller-avatar"></div>
                        <div>
                        <p class="seller-name">Xavier</p>
                        <p class="seller-role">Member</p>
                        </div>
                    </div>
                    </div>
                </div>
            <?php elseif (!$bid_is_closed && !$user_is_owner && $is_direct_sale ) :?>    
                <!-- Cas 3 : Annonce ouverte, utilisateur non créateur, vente directe uniquement -->
                <div class="pricing-card open direct-sale-only">
                    <h6 class="section-title">Pricing</h6>
                    <div class="pricing-info">
                    <p class="label">Price</p>
                    <p class="price highlight">€451,00</p>
                    </div>
                    <button type="button" class="btn btn-primary buy-now-btn">
                    <span class="icon-cart">🛒</span> Buy now
                    </button>

                    <div class="seller-info">
                    <h6 class="section-title">Seller Information</h6>
                    <div class="seller-details">
                        <div class="seller-avatar"></div>
                        <div>
                        <p class="seller-name">Quentin</p>
                        <p class="seller-role">Member</p>
                        </div>
                    </div>
                    </div>
                </div>
            <?php elseif (!$bid_is_closed && $user_is_owner) :?>
                <!-- Cas 4 : Annonce ouverte, utilisateur créateur -->
                <div class="pricing-card open owner-view">
                    <h6 class="section-title">Pricing</h6>
                    <div class="pricing-info">
                    <p class="label">Price</p>
                    <p class="price highlight">€418,00</p>
                    </div>
                    <button type="button" class="btn btn-primary buy-now-btn" disabled>
                    <span class="icon-cart">🛒</span> Buy now
                    </button>
                    <p class="warning-message">You cannot bid on your own listing.</p>

                    <div class="seller-info">
                    <h6 class="section-title">Seller Information</h6>
                    <div class="seller-details">
                        <img src="avatar-boris.png" alt="Boris avatar" class="seller-avatar" />
                        <div>
                        <p class="seller-name">Boris</p>
                        <p class="seller-role">Member</p>
                        </div>
                    </div>
                    </div>
                </div>
            <?php endif;?>    
        </div>
    </div>
                    
<!-- right container : 
 - Current Bid
 - Starting at
 - Text field to place bid  
 - Place Bid button 
 - seller information
  -->



</div>    
</body>
</html>