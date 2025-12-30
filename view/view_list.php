<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>browse</title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css"/>
</head>

<body>

    <?php 
        $title = "My items";
        include("header.php");
    ?>
    
    <?php  if(sizeof($active_items) != 0):?>
        <h1>Active Items</h1>
        <div class="my-items">
            <?php foreach ($active_items as $item): ?>
                <?php include "view/browse.php"; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php  if(sizeof($closed_unsold_items) != 0):?>
        <h1>Closed Unsold Items</h1>
        <div class="other-items">
            <?php foreach ($closed_unsold_items as $item): ?>
                <?php include "view/browse.php"; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php  if(sizeof($sold_items) != 0):?>
        <h1>Sold Items</h1>
        <div class="other-items">
            <?php foreach ($sold_items as $item): ?>
                <?php include "view/browse.php"; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <a href="user/logout">disconnect</a>

    

    <footer> 
        <?php include('footer.php'); ?>
    </footer>
</body>
</html>