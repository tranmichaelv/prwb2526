<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <base href="<?= $web_root ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" 
          integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/styles.css" >
    <link rel="stylesheet" href="css/OpenItem.css" >
    
    <title>Item images</title>
</head>
<body>

    <?php 
    $page = "open_item" ; 
    $title = "Item open";
    $pageBefore = in_array($from, ['from-browse', '']) ? "item/browse" : "item/myItems";
    include("header.php")?>

    <!-- Un container de toute la page open item -->
<div class="page-container d-flex flex-row align-items-start mb-2">
        
<!-- left-container -->
    <div class="left-container d-flex flex-column gap-4 flex-grow-1">

        <section class="item-content">
            <!-- main-picture-container -->
            <div class="main-picture">
                <?php 

                     if ($main_picture_path == null) {
                        $src = 'uploads/default/default_image.png';
                    } else {
                        $src = $main_picture_path;
                    }
                ?>

                <img src="<?= $src ;?>" alt="Image principale de Item">
            </div>

            <div class="item-infos">
                <p>
                    <h2> <?= $item->getTitle(); ?> </h2>
                </p>
                <p> <?= $item->getDescription(); ?></p>

                <div class="startEndBid">
                    <p> start : <?= $item->getCreatedAt(); ?></p>
                    <p> End : <?= $item->getEndAt(); ?></p>
                </div>
            </div>
        </section>
        

        <section>
            <!-- additional-images-container -->
            <h6 style="color : white">Additional Images</h6>
            <div class="additional-images">
                <?php if (!empty($pictures)) : ?>
                    <?php foreach($pictures as $pic):?>
                        <?php if ($from === 'from-browse' || $from === null) : ?>
                            <a href="item/open/<?= $item->getId() ?>/from-browse/<?= $pic["priority"] ?>"><img src="<?=$pic['picture_path'] ?>" alt="item image" class="thumbnail"></a>
                        <?php elseif ($from === 'from-items' || $from === null) :?>
                            <a href="item/open/<?= $item->getId() ?>/from-items/<?= $pic["priority"] ?>"><img src="<?=$pic['picture_path'] ?>" alt="item image" class="thumbnail"></a>
                        <?php endif;?>
                    <?php endforeach; ?>   
                <?php else :?>
                    <div></div>
                <?php endif;?>        
            </div>
        </section>

        <section>
            <!-- bid-history-container -->
           <div class="bids-history">
                
                <div class="bids">
                     <?php if (!empty($bids_history)) :?>
                        <div><p>Bid History</p></div>
                        <?php foreach($bids_history as $bid) :?>
                            <div class="bid">
                                <p class="owner"><?= $bid['pseudo']; ?></p>
                                <p class="amount"><?= "€ " . $bid['amount']; ?></p>
                                <p class="date"><?= $bid['created_at']; ?></p>
                            </div>
                        <?php endforeach ?>
                    <?php else : ?>
                        
                    <?php endif ?> 
                </div>    
            </div>
        </section>

    </div>



    <!-- right container -->
    <div class="right-container d-flex flex-column gap-4">
        <div class="pricing-container">

            <?php if ($pricing_case === 'case1'){
                 include "pricing_container/_case1.php";
             } ?>

            <?php if ($pricing_case === 'case2'){
                 include "pricing_container/_case2.php";
             } ?>

            <?php if ($pricing_case === 'case3') {
                include "pricing_container/_case3.php";
            }  ?>

            <?php if ($pricing_case === 'case4') {
                include "pricing_container/_case4.php";
            } ?>

            <?php if ($pricing_case === 'case5'){
                include "pricing_container/_case5.php"; 
            } ?>

            <?php if ($pricing_case === 'case6'){
                include "pricing_container/_case6.php";
             } ?>

            <?php if ($pricing_case === 'case7') {
                include "pricing_container/_case7.php"; 
            } ?>

            <?php if ($pricing_case === 'case8') {
                include "pricing_container/_case8.php";
            }

            ?>
        </div>

        <?php include "_seller_info.php"; ?>
        <?php if ($can_edit == true)  include ("owner_actions.php") ?>
    </div>

</div>
    <?php include("footer.php");?>
</body>
</html>
