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
    <!-- <div class="title"><h2>browse</h2></div> -->
    <?php  if(sizeof($items_user) != 0):?>
        <h1>Items I'm Participating in</h1>
    <?php endif; ?>
    <div class="my-items">
    <?php foreach ($items_user as $item_user): ?>
        <div class="item-card">
            <?php $pictures = Item_pictures::get_all_pictures_by_item($item_user->id) ?>
            <?php $user = User::get_users_by_id($item_user->owner) ?>
            <?php $time = ControllerItem::get_time_left($item_user->end_date) ?>

            <div class="image-container">
                <img src='<?= $item_user->picture_path ?>' alt='<?= $item_user->title ?>' width='300'>

                <?php if ($item_user->buy_now_price > 0 || $item_user->starting_bid > 0): ?>
                    <div class="labels">
                        <?php if ($item_user->buy_now_price > 0 && $item_user->starting_bid > 0): ?>
                            <span class="label buy-now">Buy Now</span>
                            <span class="label auction">Auction</span>
                        <?php elseif ($item_user->buy_now_price > 0): ?>
                            <span class="label buy-now">Buy Now</span>
                        <?php elseif ($item_user->starting_bid > 0): ?>
                            <span class="label auction">Auction</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (sizeof($pictures) > 1): ?>
                    <div class="labels-number-of-pictures">
                    <i class="bi bi-images"></i> <?= sizeof($pictures) ?> images
                    </div>
                <?php endif; ?>
            </div>

            <div class="item-info">
                <p class="item-title"><?= $item_user->title  ?></p>
                <p class="by">by <?= $user->pseudo?></p>
                <?php if ($item_user->buy_now_price > 0 && $item_user->starting_bid > 0): ?>
                    <p>&euro; <?= $item_user->buy_now_price  ?></p>
                <?php elseif ($item_user->buy_now_price > 0): ?>
                    <p>&euro; <?= $item_user->buy_now_price  ?></p>
                <?php else: ?>
                    <p>&euro; <?= $item_user->starting_bid  ?></p>
                <?php endif; ?>

                <?php if($time[0] < 1 && $time[1] < 1):?>
                    <p class="time-more-than-1-day"><?= $time[2] ?>m  <?= $time[3] ?>s left</p>
                <?php elseif($time[0] < 1):?>
                    <p class="time-more-than-1-day"><?= $time[1] ?>h  <?= $time[2] ?>m left</p>
                <?php else: ?>
                    <p class="time-more-than-1-day"><?= $time[0] ?>d  <?= $time[1] ?>h left</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <h1>Other Available Items</h1>
    <div class="other-items">

        <?php foreach ($items as $item): ?>
        <div class="item-card">
            <?php $pictures = Item_pictures::get_all_pictures_by_item($item->id) ?>
            <?php $user = User::get_users_by_id($item->owner) ?>
            <?php $time = ControllerItem::get_time_left($item->end_date) ?>

            <div class="image-container">
                <img src='<?= $item->picture_path ?>' alt='<?= $item->title ?>' width='300'>

                <?php if ($item->buy_now_price > 0 || $item->starting_bid > 0): ?>
                    <div class="labels">
                        <?php if ($item->buy_now_price > 0 && $item->starting_bid > 0): ?>
                            <span class="label buy-now">Buy Now</span>
                            <span class="label auction">Auction</span>
                        <?php elseif ($item->buy_now_price > 0): ?>
                            <span class="label buy-now">Buy Now</span>
                        <?php elseif ($item->starting_bid > 0): ?>
                            <span class="label auction">Auction</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (sizeof($pictures) > 1): ?>
                    <div class="labels-number-of-pictures">
                    <i class="bi bi-images"></i> <?= sizeof($pictures) ?> images
                    </div>
                <?php endif; ?>
            </div>

            <div class="item-info">
                <p class="item-title"><?= $item->title  ?></p>
                <p class="by">by <?= $user->pseudo?></p>
                <?php if ($item->buy_now_price > 0 && $item->starting_bid > 0): ?>
                    <p>&euro; <?= $item->buy_now_price  ?></p>
                <?php elseif ($item->buy_now_price > 0): ?>
                    <p>&euro; <?= $item->buy_now_price  ?></p>
                <?php else: ?>
                    <p>&euro; <?= $item->starting_bid  ?></p>
                <?php endif; ?>

                <?php if($time[0] < 1 && $time[1] < 1):?>
                    <p class="time-more-than-1-day"><?= $time[2] ?>m  <?= $time[3] ?>s left</p>
                <?php elseif($time[0] < 1):?>
                    <p class="time-more-than-1-day"><?= $time[1] ?>h  <?= $time[2] ?>m left</p>
                <?php else: ?>
                    <p class="time-more-than-1-day"><?= $time[0] ?>d  <?= $time[1] ?>h left</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <a href="/prwb_2526_c05/user/logout">disconnect</a>

    
    <footer> 
        <?php include('time.php'); 
        // echo "Offset: " . AppTime::get_offset() . " seconds<br>";
        // echo "Current simulated date: " . AppTime::get_current_datetime() . "<br>";?>
    </footer>
</body>
</html>