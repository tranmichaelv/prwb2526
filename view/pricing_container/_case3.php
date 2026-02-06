<?php 

?>


<section>

    <form method="post" action="item/place/<?= $item->getId()?>">
    <div class="panel">
        <div class="title fs-6 fw-bold text-secondary text-start">Pricing : 3</div>
        <div class="d-flex justify-content-between align-items-center">
            <div class="label-price">Price</div>
            <div class="buy-price fw-semibold"><?= "€ ".$item->getBuyNowPrice() ?></div>
        </div>

        <div class="divider"></div>

        <?php if ($item->bid_is_closed()): ?>
        <div>Congratulations! You purchased this item for <?= $item->getBuyNowPrice() ?></div>
        <?php elseif (!$item->bid_is_closed()) :?>
            <?php if ($user_is_guest === false): ?>
                <button type="submit" class="btn btn-success w-100 fw-semibold" ><i class="bi bi-cart-arrow-down"></i>Buy Now</button>
            <?php else :?>    
                <button type="submit" class="btn btn-success w-100 fw-semibold" disabled><i class="bi bi-cart-arrow-down"></i>Buy Now</button>
                <div class="text-secondary fw-semibold ">Sign in with a buyer account to participate</div>
            <?php endif;?>    
         <?php endif; ?>   
    </div>    
    </form>
    
</section>