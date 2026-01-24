<?php 


?>

<section>
  <div>Pricing </div>
  <form method="post" action="item/place">
    <input type="hidden" name="item" value="<?= $item->getId() ?>">
    <div>
      <div>Current Bid</div>
      <div><?= $item->getStartingBid() ?> </div>
    </div>
    <div>Starting at <?= $item->getStartingBid() ?></div>

    <div> Divider </div>

    <input type="number" name="amount" placeholder="mettre une enchère ">
    <button type="submit"> Place Bid</button>
</form>


</section>