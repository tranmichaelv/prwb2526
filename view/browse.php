<div class="item-wrapper">
    <div class="item-card">
        <?php 
        $fromWhere = $from;
        if($title) ;?>
        <a href="item/open/<?= $item->getId()?>/<?= $fromWhere ?>">
            <div class="image-container">
                <img src="<?= $item->getPictures()[0]['path'] ?>" alt="<?= $item->getTitle() ?>" width="300">

                <?php if ($item->isAuction() && $item->hasBuyNow()): ?>
                    <span class="labels label-buy-now-and-more"><i class="bi bi-bag"></i> Buy Now</span>
                    <span class="labels label-auction-and-more"><i class="bi bi-piggy-bank"></i> Auction</span>
                <?php elseif ($item->isDirectSale()): ?>
                    <span class="labels label-buy-now"><i class="bi bi-bag"></i> Buy Now</span>
                <?php else: ?>
                    <span class="labels label-auction"><i class="bi bi-piggy-bank"></i> Auction</span>
                <?php endif; ?>

                <?php if($user): ?>
                    <?php if ($item->get_highest_bidder_id() === $user->getId()): ?>
                        <span class="highest_bidder"><i class="bi bi-trophy"></i> Highest Bidder</span>
                    
                    <?php elseif($user->is_bidder($item)): ?>
                        <span class="bidder">Bidder</span>
                    <?php endif; ?>
                <?php endif; ?>



                <?php if (sizeof($item->getPictures()) > 1): ?>
                    <div class="labels-number-of-pictures">
                        <i class="bi bi-images"></i> <?= sizeof($item->getPictures()) ?> images
                    </div>
                <?php endif; ?>
            </div>

            <div class="item-info">
                <p class="item-title"><?= $item->getTitle() ?></p>
                <p class="by">by <?= $item->getOwnerName() ?></p>

                <div class="price-labels">
                    <?php if ($item->isAuction() && $item->hasBuyNow()): ?>
                        <p class="price-buy-now"><?= $item->getBuyNowPrice() ?></p>
                        <?php if ($item->hasBids()): ?>
                            <div class="label-current-bid">
                                <p>Current bid</p>
                                <p class="current-bid"><?= $item->getMaxBid() ?></p>
                            </div>
                        <?php endif; ?>
                    <?php elseif ($item->isDirectSale()): ?>
                        <p class="price-buy-now"><?= $item->getBuyNowPrice() ?></p>
                    <?php else: ?>
                        <p class="price-auction"><?= $item->getStartingBid() ?></p>
                        <?php if ($item->hasBids()): ?>
                            <div class="label-current-bid">
                                <p>Current bid</p>
                                <p class="current-bid"><?= $item->getMaxBid() ?></p>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="time-left">
                    <?php $time = $item->getTimeLeft()?>
                    <?php if (($time[0] <= 0 && $time[1] <= 0 && $time[2] <= 0 && $time[3] <= 0) 
                    || ($item->isAuction() && $item->isBuyNowReached())
                    || ($item->isDirectSale() && !$item->isNotPurchasedDirectSale() ) ): ?>
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
    <div class="price-and-closeTime">
        <?php if ($page == "purchases" || $page == "sales"): ?>
            <p><i class="bi bi-tag"></i> Paid <?= $item->getMaxBid() ?></p>
            <p><i class="bi bi-clock"></i> Closed on <?= $item->getEndAt() ?></p>
        <?php endif; ?>
    </div>
</div>