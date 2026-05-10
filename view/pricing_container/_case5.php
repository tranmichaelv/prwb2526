<?php 
$isAuction = $item->isAuction();
?>

  <div class="panel"> 
      <div class="title fs-6 fw-bold text-secondary text-start">Pricing : 5</div>
      <form method="post" action="item/place"<?=($encoded ? "/" . $encoded : "")?>>
          <input type="hidden" name="item" value="<?= $item->getId() ?>">
          <?php if ($isAuction == true): ?>
          <div class="d-flex justify-content-between align-items-center">
            <div class="fw-semibold ">Current Bid </div>
            <div class="fw-semibold" style="color: #22c55e;"><?= "€ ".$item->getMaxBid() ?></div>
          </div>
          
          <div class="d-flex justify-content-between align-items-center">
            <div class="fw-semibold ">Buy Now </div>
            <div class="fw-semibold"><?= "€ ".$item->getBuyNowPrice()?></div>
          </div>
          <?php else :?>
          <div class="d-flex justify-content-between align-items-center">
            <div class="fw-semibold ">Price</div>
            <div class="fw-semibold" style="color: #22c55e;"><?= "€ ".$item->getBuyNowPrice() ?></div>
          </div>
          <?php endif;?>  
          <div class="divider"></div>
          <div class="text-secondary fw-semibold">Congratulations! You purchased this item for <?= "€ ".$item->getMaxBid()?></div>
          
      </form>
  </div>