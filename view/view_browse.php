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
    <div class="bids">
        <h1>Items I'm Participating in</h1>
    </div>

    <h1>Other Available Items</h1>
    <div class="other_items">

        <?php foreach ($items as $item): ?>
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
            </div>

                <?php if (sizeof($pictures) > 1): ?>
                    <div class="labels-number-of-pictures">
                        <?= sizeof($pictures) ?> images
                    </div>
                <?php endif; ?>

                <div class="item-info">
                    <p class="item-title"><?= $item->title  ?></p>
                    <p class="by">by <?= $user->pseudo?></p>
                    <?php if ($item->buy_now_price > 0 && $item->starting_bid > 0): ?>
                        <p>&euro; <?= $item->buy_now_price  ?></p>
                    <?php elseif ($item->buy_now_price > 0): ?>
                        <p>&euro; <?= $item->buy_now_price  ?></p>
                    <?php elseif ($item->starting_bid > 0): ?>
                        <p>&euro; <?= $item->buy_now_price  ?></p>
                    <?php endif; ?>
                    <p class="time"><?= $time[0] ?>d  <?= $time[1] ?>h left</p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <a href="/prw/user/logout">disconnect</a>

    
    <footer> 
        <?php include('time.php'); 
        // echo "Offset: " . AppTime::get_offset() . " seconds<br>";
        // echo "Current simulated date: " . AppTime::get_current_datetime() . "<br>";?>
    </footer>
</body>
</html>