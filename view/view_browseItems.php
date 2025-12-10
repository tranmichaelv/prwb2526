<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>browse</title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css"/>
</head>

<body>
    <?php  if(sizeof($items_user) != 0):?>
        <h1>Items I'm Participating in</h1>
    <?php endif; ?>
    <div class="my-items">
    <?php foreach ($items_user as $item_user): ?>
        <div class="item-card">
            <?php $pictures = Item_pictures::get_all_pictures_by_item($item_user->getid()) ?>
            <?php $user = User::get_users_by_id($item_user->getOwner()) ?>
            <?php $time = ControllerBrowseItems::get_time_left(Item::get_end_period($item_user->getCreatedAt(), $item_user->getDurationDays())) ?>
            <?php $highest_bid = ControllerBrowseItems::highest_bid($item_user->getid())?>
            
            <div class="image-container">
                <img src='<?= $item_user->getPictures()[0]['path'] ?>' alt='<?= $item_user->getTitle() ?>' width='300'>
               
                <?php
                    $buyNow = floatval(str_replace(',', '.', $item_user->getBuyNowPrice()));
                    $startingBid = floatval(str_replace(',', '.', $item_user->getStartingBid()));
                ?>

                <?php if ($buyNow > 0 && $startingBid > 0): ?>
                    <span class="labels label-buy-now-and-more"><i class="bi bi-bag"></i> Buy Now</span>
                    <span class="labels label-auction-and-more"><i class="bi bi-piggy-bank"></i> Auction</span>
                <?php elseif ($buyNow > 0): ?>
                    <span class="labels label-buy-now"><i class="bi bi-bag"></i> Buy Now</span>
                <?php elseif ($startingBid > 0): ?>
                    <span class="labels label-auction"><i class="bi bi-piggy-bank"></i> Auction</span>
                <?php endif; ?>
            

                <?php if($highest_bid['owner'] === $_SESSION['user']->id): ?>
                            <span class="highest_bidder"><i class="bi bi-trophy"></i> Highest Bidder</span>
                <?php else: ?>
                            <span class="bidder">Bidder</span>
                <?php endif;?>
                
                <?php if (sizeof($pictures) > 1): ?>
                    <div class="labels-number-of-pictures">
                    <i class="bi bi-images"></i> <?= sizeof($pictures) ?> images
                    </div>
                <?php endif; ?>
            </div>

            <div class="item-info">
                <p class="item-title"><?= $item_user->getTitle()  ?></p>
                <p class="by">by <?= $user->pseudo?></p>
                <div class="price-labels">
                <?php if ($item_user->getBuyNowPrice() > 0 && $item_user->getStartingBid() > 0): ?>
                    <p class="price-buy-now">&euro; <?= $item_user->getBuyNowPrice()  ?></p>
                    <?php if ($highest_bid): ?>
                        <div class="label-current-bid">
                            <p> Current bid </p>
                            <p class="current-bid"> &euro; <?= ControllerBrowseItems::convert_format_number_for_bids($highest_bid['amount']) ?> </p>
                        </div>
                    <?php endif; ?>
                <?php elseif ($item_user->getBuyNowPrice() > 0): ?>
                    <p class="price-buy-now">&euro; <?= $item_user->getBuyNowPrice()  ?></p>
                <?php else: ?>
                    <p class="price-auction">&euro; <?= $item_user->getStartingBid()  ?></p>
                    <?php if ($highest_bid): ?>
                    <div class="label-current-bid">
                        <p> Current bid </p>
                        <p class="current-bid"> &euro; <?= ControllerBrowseItems::convert_format_number_for_bids($highest_bid['amount']) ?> </p>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
                </div>

                <div class="time-left">
                <?php if($time[0] < 1 && $time[1] < 1):?>
                    <p class="time-more-than-1-day"><?= $time[2] ?>m  <?= $time[3] ?>s left</p>
                <?php elseif($time[0] < 1):?>
                    <p class="time-more-than-1-day"><?= $time[1] ?>h  <?= $time[2] ?>m left</p>
                <?php else: ?>
                    <p class="time-more-than-1-day"><?= $time[0] ?>d  <?= $time[1] ?>h left</p>
                <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <h1>Other Available Items</h1>
    <div class="other-items">

        <?php foreach ($items as $item): ?>
        <div class="item-card">
            <?php $pictures = Item_pictures::get_all_pictures_by_item($item->getid()) ?>
            <?php $user = User::get_users_by_id($item->getOwner()) ?>
            <?php $time = ControllerBrowseItems::get_time_left(Item::get_end_period($item->getCreatedAt(), $item->getDurationDays())) ?>
            <?php $highest_bid = ControllerBrowseItems::highest_bid($item->getid())?>

            <div class="image-container">
                <img src='<?= $item->getPictures()[0]['path'] ?>' alt='<?= $item->getTitle() ?>' width='300'>

                <?php
                $buyNow = floatval(str_replace(',', '.', $item->getBuyNowPrice()));
                $startingBid = floatval(str_replace(',', '.', $item->getStartingBid()));
                ?>

                <?php if ($buyNow > 0 && $startingBid > 0): ?>
                    <span class="labels label-buy-now-and-more"><i class="bi bi-bag"></i> Buy Now</span>
                    <span class="labels label-auction-and-more"><i class="bi bi-piggy-bank"></i> Auction</span>
                <?php elseif ($buyNow > 0): ?>
                    <span class="labels label-buy-now"><i class="bi bi-bag"></i> Buy Now</span>
                <?php elseif ($startingBid > 0): ?>
                    <span class="labels label-auction"><i class="bi bi-piggy-bank"></i> Auction</span>
                <?php endif; ?>
            

                <?php if (sizeof($pictures) > 1): ?>
                    <div class="labels-number-of-pictures">
                        <i class="bi bi-images"></i> <?= sizeof($pictures) ?> images
                    </div>
                <?php endif; ?>
            </div>

            <div class="item-info">
                <p class="item-title"><?= $item->getTitle()  ?></p>
                <p class="by">by <?= $user->pseudo?></p>
                <div class="price-labels">
                <?php if ($item->getBuyNowPrice() > 0 && $item->getStartingBid() > 0): ?>
                    <p class="price-buy-now">&euro; <?= $item->getBuyNowPrice()  ?></p>
                    <?php if ($highest_bid): ?>
                    <div class="label-current-bid">
                        <p> Current bid </p>
                        <p class="current-bid"> &euro; <?= ControllerBrowseItems::convert_format_number_for_bids($highest_bid['amount']) ?> </p>
                    </div>
                    <?php endif; ?>
                <?php elseif ($item->getBuyNowPrice() > 0): ?>
                    <p class="price-buy-now">&euro; <?= $item->getBuyNowPrice()  ?></p>
                <?php else: ?>
                    <p class="price-auction">&euro; <?= $item->getStartingBid()  ?></p>
                    <?php if ($highest_bid): ?>
                    <div class="label-current-bid">
                        <p> Current bid </p>
                        <p class="current-bid"> &euro; <?= ControllerBrowseItems::convert_format_number_for_bids($highest_bid['amount']) ?> </p>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
                </div>

                <div class="time-left">
                <?php if($time[0] < 1 && $time[1] < 1):?>
                    <p class="time-more-than-1-day"><?= $time[2] ?>m  <?= $time[3] ?>s left</p>
                <?php elseif($time[0] < 1):?>
                    <p class="time-more-than-1-day"><?= $time[1] ?>h  <?= $time[2] ?>m left</p>
                <?php else: ?>
                    <p class="time-more-than-1-day"><?= $time[0] ?>d  <?= $time[1] ?>h left</p>
                <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <a href="/prwb_2526_c05/login/logout">disconnect</a>

    
    <footer> 
        <?php include('time.php'); ?>
    </footer>
</body>
</html>