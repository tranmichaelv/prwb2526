
<?php $max_bid = $item->hasBids() ? $item->getMaxBid() : $item->getStartingBid()?>
<section>

    
    <div class="panel">
        <form method="post" action="item/place/<?= $item->getId()?>">
            <input type="hidden" name="item" value="<?= $item->getId() ?>">
        <div class="d-flex justify-content-between align-items-center">
            <div class="fw-semibold ">Current Bid </div>
            <div class="fw-semibold" style="color: #22c55e;"><?= "€ ".$max_bid?></div>
        </div>
          
        <div class="buy-label-price">
            <div class="buy-now-label fw-semibold">Buy Now</div>
            <div class="buy-now-price fw-semibold"><?= " € ".$item->getBuyNowPrice() ?> </div>
        </div>

        <div class="divider"></div>

        <div class="input-group mb-3">
            <span class="input-group-text bg-dark text-white  border-secondary">€</span>
            <input class="form-control bg-dark text-white border-secondary fw-bold" type="" name="amount"  value="<?= (float) $max_bid ?>" aria-label="Username" aria-describedby="basic-addon1">
        </div>
          <button type="submit" class="btn btn-success w-100 fw-semibold mb-2"> Place Bid</button>
        </form> 

        <form method="post" action="item/place/<?= $item->getId() ?>">
            <button type="submit" class="btn btn-outline-primary fw-semibold w-100">Buy Now at <?=  $item->getBuyNowPrice() ?></button>
        </form>
    </div>    

    
    
</section>