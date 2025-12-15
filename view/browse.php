<div class="item-card">
    <?php $pictures = Item_pictures::get_all_pictures_by_item($item->getId()); ?>
    <?php $user = $_SESSION['user']; ?>
    <?php $time = Utils::get_time_left(Item::get_end_period($item->getCreatedAt(), $item->getDurationDays())); ?>

    <a href="test">
    <div class="image-container">
        <img src="<?= $item->getPictures()[0]['path'] ?>" alt="<?= $item->getTitle() ?>" width="300">

        <?php
            $buyNow = floatval(str_replace(',', '.', $item->getBuyNowPrice()));
            $startingBid = floatval(str_replace(',', '.', $item->getStartingBid()));
        ?>

        <?php if ($item->is_auction() && $item->has_buy_now()): ?>
            <span class="labels label-buy-now-and-more"><i class="bi bi-bag"></i> Buy Now</span>
            <span class="labels label-auction-and-more"><i class="bi bi-piggy-bank"></i> Auction</span>
        <?php elseif ($item->is_direct_sale()): ?>
            <span class="labels label-buy-now"><i class="bi bi-bag"></i> Buy Now</span>
        <?php else: ?>
            <span class="labels label-auction"><i class="bi bi-piggy-bank"></i> Auction</span>
        <?php endif; ?>

        <?php if ($item->get_highest_bidder() === $user->id): ?>
            <span class="highest_bidder"><i class="bi bi-trophy"></i> Highest Bidder</span>
        
        <?php elseif($item->is_bidder()): ?>
            <span class="bidder">Bidder</span>
        <?php endif; ?>



        <?php if (sizeof($pictures) > 1): ?>
            <div class="labels-number-of-pictures">
                <i class="bi bi-images"></i> <?= sizeof($pictures) ?> images
            </div>
        <?php endif; ?>
    </div>

    <div class="item-info">
        <p class="item-title"><?= $item->getTitle() ?></p>
        <p class="by">by <?= $user->pseudo ?></p>

        <div class="price-labels">
            <?php if ($item->is_auction() && $item->has_buy_now()): ?>
                <p class="price-buy-now">€ <?= $item->getBuyNowPrice() ?></p>
                <?php if ($item->has_bids()): ?>
                    <div class="label-current-bid">
                        <p>Current bid</p>
                        <p class="current-bid">€ <?= $item->get_highest_bid() ?></p>
                    </div>
                <?php endif; ?>
            <?php elseif ($item->is_direct_sale()): ?>
                <p class="price-buy-now">€ <?= $item->getBuyNowPrice() ?></p>
            <?php else: ?>
                <p class="price-auction">€ <?= $item->getStartingBid() ?></p>
                <?php if ($item->has_bids()): ?>
                    <div class="label-current-bid">
                        <p>Current bid</p>
                        <p class="current-bid">€ <?= $item->get_highest_bid() ?></p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="time-left">
            <?php if ($time[0] <= 0 && $time[1] <= 0 && $time[2] <= 0 && $time[3] <= 0): ?>
                <p>closed</p>
            <?php elseif ($time[0] < 1 && $time[1] < 1): ?>
                <p><?= max(0, $time[2]) ?>m <?= max(0, $time[3]) ?>s left</p>
            <?php elseif ($time[0] < 1): ?>
                <p><?= max(0, $time[1]) ?>h <?= max(0, $time[2]) ?>m left</p>
            <?php else: ?>
                <p><?= max(0, $time[0]) ?>d <?= max(0, $time[1]) ?>h left</p>
        <?php endif; ?>
    </div>
    </div>
    </a>
</div>