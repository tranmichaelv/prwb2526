<?php 


?>

<section>
    <div>Pricing </div>
    <form method="post" action="item/place/<?= $item->getId() ?>">
      <input type="hidden" name="item" value="<?= $item->getId() ?>">
      <div>
        <div>Current Bid</div>
        <div><?= $item->getMaxBid();?> </div>
      </div>
      <div>
        <div>Buy Now </div>
        <div><?= $item->getBuyNowPrice() ?> </div>
      </div>

      <div> Divider </div>

      <input type="number" name="amount" placeholder="mettre une enchère " >
      <button type="submit" > Place Bid</button>
      <button type="submit">Buy Now at <?= $item->getBuyNowPrice(); ?></button>
</form>


</section>