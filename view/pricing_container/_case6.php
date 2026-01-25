
  <div class="panel"> 
      <div class="title fs-6 fw-bold text-secondary">Pricing : CASE 6 </div>
      <form method="post" action="item/place">
          <input type="hidden" name="item" value="<?= $item->getId() ?>">
          
          <div class="d-flex justify-content-between align-items-center">
            <div class="fw-semibold ">Current Bid </div>
            <div class="fw-semibold" style="color: #22c55e;"><?= "€ ".$item->getMaxBid()?></div>
          </div>
          
          <div class="d-flex justify-content-between align-items-center">
            <div class="fw-semibold ">Buy Now</div>
            <div class="fw-semibold" style="color: #22c55e;"><?= "€ ".$item->getBuyNowPrice()?></div>
          </div>
          

          <div class="divider"></div>
          <div><?= $item->getOwner(). " "?>won this item for<?= " € ".$item->getMaxBid()?></div>
          
      </form>
  </div>