<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>browse</title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    <script src="lib/jquery-4.0.0.min.js" type="text/javascript"></script>
    <script>
        const page = "<?= $page ?>";
        const user = "<?= $user ? $user->getPseudo() : '' ?>";
        const currentUserId = <?= $user ? $user->getId() : 'null' ?>;
        const from = "<?= $from ?>";
    </script>
    <script src="scripts/filter_items.js" type="text/javascript"></script>
</head>

<body>

    <?php 
        $from = 'from-browse';
        $title = "Browse";
        include("header.php");
        $encoded = $encoded ?? null; 
    ?>

    <div id="filters-box" style="display: none;">
        <?php include('search_box.php'); ?>
        <?php include('filter_category.php') ?>
    </div>
    
    <div id="items-container">
        <?php if($user):?>
            <?php  if(sizeof($items_user) != 0):?>
                <div class="category-items">
                    <h1>Items I'm Participating in</h1>
                    <div class="items-box">
                        <?php foreach ($items_user as $item): ?>
                            <?php include "view/browse.php"; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php  if(sizeof($items) != 0):?>
                <div class="category-items">
                    <h1>Other Available Items</h1>
                    <div class="items-box">
                        <?php foreach ($items as $item): ?>
                            <?php include "view/browse.php"; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($user == false): ?>
            <?php  if(sizeof($all_items) != 0):?>
                <div class="category-items">
                    <h1>Available Items</h1>
                    <div class="items-box">
                        <?php foreach ($all_items as $item): ?>
                            <?php include "view/browse.php"; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php include('no_result.php'); ?>

    <footer> 
        <?php include('footer.php'); ?>
    </footer>
</body>
</html>