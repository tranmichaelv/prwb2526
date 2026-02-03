<?php 

?>


<section>
  <div class="panel"> 
      <div class="title fs-6 fw-bold text-secondary text-start">Pricing </div>
      <form method="post" action="item/place/<?= $item->getId() ?>">
          <input type="hidden" name="item" value="<?= $item->getId() ?>">
          
          <div class="d-flex justify-content-between align-items-center">
            <div class="fw-semibold ">Current Bid </div>
            <div class="fw-semibold" style="color: #22c55e;"><?= $item->getMaxBid()?></div>
          </div>
          
          <div class="buy-label-price">
              <div class="buy-now-label fw-semibold">Buy Now</div>
              <div class="buy-now-price fw-semibold"><?= " € ".$item->getBuyNowPrice() ?> </div>
          </div>
          

          <div class="divider"></div>

        <?php if ($user_is_guest === false): ?>
          <div class="input-group mb-3">
            <span class="input-group-text bg-dark text-white  border-secondary">€</span>
            <input class="form-control bg-dark text-white border-secondary fw-bold" type="" name="amount" placeholder="mettre une enchère" value="<?= $item->getMaxBid() + 1 ?>" aria-label="Username" aria-describedby="basic-addon1">
          </div>
          
            <button type="submit" class="btn btn-success w-100 fw-semibold mb-2"> Place Bid</button>
          <?php else:?>
            <div class="input-group mb-3">
              <span class="input-group-text bg-dark text-white  border-secondary">€</span>
              <input disabled class="form-control bg-dark text-white border-secondary fw-bold" type="" name="amount" placeholder="mettre une enchère" value="<?= $item->getMaxBid() + 1 ?>" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <button type="submit" class="btn btn-success w-100 fw-semibold mb-2" disabled> Place Bid</button>
          <?php endif;?>  
      </form>
      <form method="post" action="item/place/<?= $item->getId() ?>">
        <?php if ($user_is_guest === false): ?>
          <button type="submit" class="btn btn-outline-primary fw-semibold w-100">Buy Now at <?=  $item->getBuyNowPrice() ?></button>
        <?php else:?>
          <button type="submit" class="btn btn-outline-primary fw-semibold w-100" disabled>Buy Now at <?=  $item->getBuyNowPrice() ?></button>
            <div class="text-secondary fw-semibold ">Sign in with a buyer account to participate</div>
        <?php endif;?>  
      </form>
  </div>
  


</section>