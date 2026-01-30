<?php 

?>


<section>

    <form method="post" action="item/place/<?= $item->getId()?>">
    <div class="panel">
        <div class="title fs-6 fw-bold text-secondary">Pricing : CASE 3 </div>
        <div class="d-flex justify-content-between align-items-center">
            <div class="label-price">Price</div>
            <div class="buy-price fw-semibold"><?= "€ ".$item->getBuyNowPrice() ?></div>
        </div>

        <div class="divider"></div>
        <?php if ($bid->bid_is_closed()): ?>
        <div>Congratulations! You purchased this item for <?= $item->getBuyNowPrice() ?></div>
        <?php elseif (!$bid->bid_is_closed()) :?>
        <button type="submit" class="btn btn-success w-100 fw-semibold" ><i class="bi bi-cart-arrow-down"></i>Buy Now</button>
         <?php endif; ?>   
    </div>    
    </form>
    
</section>