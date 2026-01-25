

<section>

    <form method="post" action="item/place/<?= $item->getId()?>">
    <div class="panel">
        <div class="title fs-6 fw-bold text-secondary">Pricing : CASE 7 </div>
        <div class="d-flex justify-content-between align-items-center">
            <div class="label-price">Price</div>
            <div class="buy-price fw-semibold"><?= "€ ".$item->getBuyNowPrice() ?></div>
        </div>

        <div class="divider"></div>

        <div>This listing ended without a buyer</div>
    </div>    
    </form>
    
</section>