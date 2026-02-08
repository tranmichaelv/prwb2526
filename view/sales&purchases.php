<?php $stats = $total_and_average[0]; ?>

<div class="title-purchases-or-sales">
    <?php if($page == "purchases"): ?>
        <div class="title-text">
            <h1>My purchases</h1>
            <p>Track the gear you've successfully secured.</p>
        </div>

        <div class="purchases-badge">
            <?= $stats["number_of_purchases"] ?> purchases
        </div>
    <?php else: ?>
        <div class="title-text">
            <h1>Completed Sales</h1>
            <p>A snapshot of the deals you've wrapped up.</p>
        </div>

        <div class="sales-badge">
            <?= $stats["number_of_purchases"] ?> sales
        </div>
    <?php endif; ?>
</div>

<div class="info-container">
    <div class="info-box">
        <?php if($page == "purchases"): ?>
            <p class="info-title">TOTAL SPENT</p>
        <?php else: ?>
            <p class="info-title">TOTAL REVENUE</p>
        <?php endif; ?>
        <p class="info-value"><?= $stats["total"] ?></p>
        <p class="info-desc">Accros <?= $stats["number_of_purchases"] ?> purchases</p>
    </div>

    <div class="info-box">
        <p class="info-title">AVERAGE TICKET</p>
        <p class="info-value"><?= $stats["average"] ?></p>
        <p class="info-desc">Helps you plan future bids</p>
    </div>

    <div class="info-box">
        <?php if($page == "purchases"): ?>
            <p class="info-title">TOP SELLER</p>
            <p class="top-seller"><?= $top_person ?></p>
            <p class="info-desc">Who you've bought from the most</p>
        <?php else: ?>
            <p class="info-title">TOP BIDDER</p>
            <p class="top-seller"><?= $top_person ?></p>
            <p class="info-desc">Most recurring winning bidder</p>
        <?php endif; ?>
    </div>
</div>