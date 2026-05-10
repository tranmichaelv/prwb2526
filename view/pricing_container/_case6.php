
  <?php 
  $lastBider = null;
  if (count($bids_history) != 0) {
      $lastBider = $bids_history['0']['pseudo'];
  }

  
 $max_bid = $item->hasBids() ? $item->getMaxBid() : $item->getStartingBid();
  ?>
  
  <div class="panel"> 
      <div class="title fs-6 fw-bold text-secondary text-start">Pricing : 6</div>
      <form method="post" action="item/place"<?=($encoded ? "/" . $encoded : "")?>>
          <input type="hidden" name="item" value="<?= $item->getId() ?>">
          <?php if ($item->isAuction() === false):?>
              <div class="d-flex justify-content-between align-items-center">
                <div class="fw-semibold ">Buy Now</div>
                <div class="fw-semibold"><?= "€ ".$item->getBuyNowPrice()?></div>
              </div>
          <?php else :?>
          <div class="d-flex justify-content-between align-items-center">
            <div class="fw-semibold ">Current Bid </div>
            <div class="fw-semibold" style="color: #22c55e;"><?= "€ ".$max_bid?></div>
          </div>
          
          <div class="d-flex justify-content-between align-items-center">
            <div class="fw-semibold ">Buy Now</div>
            <div class="fw-semibold"><?= "€ ".$item->getBuyNowPrice()?></div>
          </div>
          <?php endif;?>

          <div class="divider"></div>
          <?php if ($lastBider != null) :?>
              <div class="text-secondary fw-semibold"><?= $lastBider." "?>won this item for<?= " € ".$max_bid?></div>
          <?php else :?>
              <div class="text-secondary fw-semibold"><?= $lastBider." "?>This listing ended without a buyer</div>
           <?php endif;?> 
      </form>
  </div>