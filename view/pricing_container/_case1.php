<?php 


?>

<section>
  <div class="panel"> 
      <div class="title fs-6 fw-bold text-secondary">Pricing : CASE 1</div>
      <form method="post" action="item/place">
          <input type="hidden" name="item" value="<?= $item->getId() ?>">
          <div class="d-flex justify-content-between align-items-center">
            <div class="fw-semibold ">Current Bid </div>
            <div class="fw-semibold" style="color: #22c55e;"><?= "€ ".$item->getStartingBid()?></div>
          </div>
          <div class="starting-at ">Starting at <?= $item->getStartingBid() ?></div>

          <div class="divider"></div>

          <div class="input-group mb-3">
            <span class="input-group-text bg-dark text-white  border-secondary">€</span>
            <input class="form-control bg-dark text-white border-secondary fw-bold" type="" name="amount"  value="<?= $item->getStartingBid() + 1 ?>" aria-label="Username" aria-describedby="basic-addon1">
          </div>
          

          <button type="submit" class="btn btn-success w-100 fw-bold"> Place Bid</button>
      </form>
  </div>
  


</section>