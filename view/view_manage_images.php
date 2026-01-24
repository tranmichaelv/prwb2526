<!DOCTYPE html>
<html lang ="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Images</title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/manage_images.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <?php 
            $page = "manage_images";
            $title = "Manage Images";
            $pageBefore = "item/open/" . $item->get_id();
            include("header.php");
    ?>
 <div class="container">
    <p class="subtitle">Manage Images for "<?= $item->get_title()?>"</p>

    <div class="card">
        <h2>Add New Images</h2>
        <form method="post"
              action="item/upload_images/<?= $item->get_id()?>"
             enctype="multipart/form-data">
            
            <label class="small">Select Images</label>
            <input type ="file" name="images[]" multiple
                accept=".jpg,.jpeg,.png,.gif,.webp">

            <p class="hint">
                You can select multiple images (JPG, PNG, GIF, WebP). Images will be added to the end of your current list.
            </p>
            
            <button type="submit" class ="btn primary">Upload Images</button>
        </form>  
    </div>

    <div class="card">

    <h2>Current Images</h2>

             <?php $maxPriority = count($pictures); ?>

             <div class="grid">
                <?php foreach ($pictures as $p): ?>
                    <?php $priority = $p->get_priority(); ?>

                    <div class="thumb">
                        <img src="<?= $p->get_picture_path() ?>" alt="item picture">

                      <div class="actions">

                            <!-- gauche -->
                            <form method="post" action="item/move_picture/<?= $item->get_id() ?>/<?= $priority ?>/left">
                                <button class="btn icon" <?= $priority == 1 ? "disabled" : "" ?>>&#x25C0;</button>
                            </form>

                            <!-- droite -->
                            <form method="post" action="item/move_picture/<?= $item->get_id() ?>/<?= $priority ?>/right">
                                <button class="btn icon" <?= $priority == $maxPriority ? "disabled" : "" ?>>&#x25B6;</button>
                            </form>

                            <!-- delete -->
                            <form method="post" action="item/delete_picture/<?= $item->get_id() ?>/<?= $priority ?>">
                                <button class="btn danger" title="Delete">X</button>
                            </form>

                        </div>
                    </div>

                <?php endforeach; ?>
            </div>   

    </div>

 </div>

     <footer> 
        <?php include('footer.php'); ?>
    </footer>
</body>
</html>
