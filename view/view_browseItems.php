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
        <p> Browse</p>
    </div>

    <?php if($_SESSION["user"]->id !== 0):?>
        <?php  if(sizeof($items_user) != 0):?>
            <h1>Items I'm Participating in</h1>
            <div class="my-items">
                <?php foreach ($items_user as $item): ?>
                    <?php include "view/browse.php"; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php  if(sizeof($items) != 0):?>
            <h1>Other Available Items</h1>
            <div class="other-items">
                <?php foreach ($items as $item): ?>
                    <?php include "view/browse.php"; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($_SESSION["user"]->id == 0): ?>
        <?php  if(sizeof($all_items) != 0):?>
            <h1>Available Items</h1>
            <div class="other-items">
                <?php foreach ($all_items as $item): ?>
                    <?php include "view/browse.php"; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <a href="login/logout">disconnect</a>

    

    <footer> 
        <?php include('footer.php'); ?>
    </footer>
</body>
</html>