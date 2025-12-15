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
                ?>

                <img src="<?= $src ?>" alt="Image principale de Item">
            </div>

            <div class="item-infos">
                <p>
                    <h2> <?php echo $item->title; ?></h2>
                </p>
                <p> <?php echo $item->description; ?></p>

                <div class="startEndBid">
                    <p> start : <?php echo $item->created_at ?></p>
                    <p> End : <?php echo $item->end_at ?></p>
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
                <div class="owner-bid">
                    <?php if (!empty($bids)) :?>
                        <?php foreach($bids as $b) :?>
                            <div class="bid">
                                <p class="owner"><?= htmlspecialchars($b['owner']) ?></p>
                                <p class="date"><?= htmlspecialchars($b['created_at']) ?></p>
                                <p class="amount"><?= "€" . number_format($b['amount'], 2, ',', ' ') ?></p>
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

        <section class="pricing-container">
            <!-- pricing-container -->
            <div class="pricing-content">
                <h6> Pricing </h6>
                <?php if (!empty($errors)): ?>
                    <?php foreach($errors as $err) : ?>
                        <p class="text-warning"><?php echo $err?></p>
                    <?php endforeach;?>  
                <?php endif?>

                <div>
                    <?php if($current_bid != false) : ?>
                    <p>Current Bid <?= "€".$current_bid->current_bid; ?></p>
                    <p>Buy Now  <?= "€".$current_bid->buy_now_price; ?></p>
                    <?php endif;?>    
                </div>   
                
                <div>
                    <input type="text" name="new-bid">
                    <button type="submit" name="place-bid"> Place Bid </button>
                    <button type="submit" name="Buy-now-at"> Buy Now at € <?php echo $current_bid->buy_now_price; ?></button>
                </div>
            </div>
            <br><br>
            <div>
                <h6>Seller information</h6>
                <div class="seller-info-container ">
                    <?php if ($seller) : ?>
                    <p> <?= $seller['pseudo'];?> </p>
                    <p><?= $seller['role'];?></p>
                    <?php endif ?>

                </div>
            </div>

        </section>
        

        <section>
            <!-- Seller-info-container -->
            <div>

            </div>

        </section>

    </div>
</div>    
</body>
</html>