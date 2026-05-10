
<section>
  <div class="panel"> 
      <div class="title fs-6 fw-bold text-secondary text-start">Pricing : 8</div>
      <form method="post" action="item/place<?=($encoded ? "/" . $encoded : "")?>">
          <input type="hidden" name="item" value="<?= $item->getId() ?>">
          <div class="d-flex justify-content-between align-items-center">
            <div class="fw-semibold ">Current Bid </div>
            <div class="fw-semibold" style="color: #22c55e;"><?= "€ ".$item->getMaxBid()?></div>
          </div>
          

          <div class="divider"></div>
          <?php if ($user_is_guest === false):?>
          <div class="input-group mb-3">
            <span class="input-group-text bg-dark text-white  border-secondary">€</span>
            <input class="form-control bg-dark text-white border-secondary fw-bold" type="" name="amount" placeholder="mettre une enchère" value="<?= $item->getMaxBid() + 1 ?>" aria-label="Username" aria-describedby="basic-addon1">
          </div>
    

          <button type="submit" class="btn btn-success w-100 fw-bold"> Place Bid</button>
          <?php else :?>
              <div class="input-group mb-3">
            <span class="input-group-text bg-dark text-white  border-secondary">€</span>
            <input class="form-control bg-dark text-white border-secondary fw-bold" type="" name="amount" placeholder="mettre une enchère" value="<?= $item->getMaxBid() + 1 ?>" aria-label="Username" aria-describedby="basic-addon1" disabled>
          </div>
    

          <button type="submit" class="btn btn-success w-100 fw-bold" disabled> Place Bid</button>
            <div class="text-secondary fw-semibold ">Sign in with a buyer account to participate</div>
          <?php endif;?>  
      </form>
  </div>
  


</section>