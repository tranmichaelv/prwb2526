<div class="item-card">
    <?php $pictures = Item_pictures::get_all_pictures_by_item($item->getId()); ?>
    <?php $user = User::get_users_by_id($item->getOwner()); ?>
    <?php $time = Utils::get_time_left(Item::get_end_period($item->getCreatedAt(), $item->getDurationDays())); ?>
    <?php $highest_bid = Bids::get_highest_bids_from_item($item->getId()); ?>

    <a href="test">
    <div class="image-container">
        <img src="<?= $item->getPictures()[0]['path'] ?>" alt="<?= $item->getTitle() ?>" width="300">

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

        <?php if (isset($_SESSION['user']) && $highest_bid !== null && $highest_bid->getOwner() == $_SESSION['user']->id): ?>
            <span class="highest_bidder"><i class="bi bi-trophy"></i> Highest Bidder</span>
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
            <?php if ($buyNow > 0 && $startingBid > 0): ?>
                <p class="price-buy-now">€ <?= $item->getBuyNowPrice() ?></p>
                <?php if ($highest_bid): ?>
                    <div class="label-current-bid">
                        <p>Current bid</p>
                        <p class="current-bid">€ <?= Utils::convert_format_number_for_bids($highest_bid->getAmount()) ?></p>
                    </div>
                <?php endif; ?>
            <?php elseif ($buyNow > 0): ?>
                <p class="price-buy-now">€ <?= $item->getBuyNowPrice() ?></p>
            <?php else: ?>
                <p class="price-auction">€ <?= $item->getStartingBid() ?></p>
                <?php if ($highest_bid): ?>
                    <div class="label-current-bid">
                        <p>Current bid</p>
                        <p class="current-bid">€ <?= Utils::convert_format_number_for_bids($highest_bid['amount']) ?></p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="time-left">
            <?php if ($time[0] < 1 && $time[1] < 1): ?>
                <p><?= $time[2] ?>m <?= $time[3] ?>s left</p>
            <?php elseif ($time[0] < 1): ?>
                <p><?= $time[1] ?>h <?= $time[2] ?>m left</p>
            <?php else: ?>
                <p><?= $time[0] ?>d <?= $time[1] ?>h left</p>
            <?php endif; ?>
        </div>
    </div>
    </a>
</div>