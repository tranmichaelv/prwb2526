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
    
    <div class="header">
        <p>Sales </p> <i class="bi bi-cart-fill"></i>
    </div>

    <?php include "view/sales&purchases.php"; ?>

    <div class="my-items">
        <?php foreach ($sales as $item): ?>
            <?php include "view/browse.php"; ?>
        <?php endforeach;?>
    </div>

    <footer> 
        <?php include('footer.php'); ?>
    </footer>
</body>
</html>