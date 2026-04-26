<!DOCTYPE html>
<html lang ="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Images</title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/manage_images.css">
    <link rel="stylesheet" href="css/styles.css">
    <script src="lib/jquery-4.0.0.min.js" type="text/javascript"></script>
    <script src="https://code.jquery.com/ui/1.14.2/jquery-ui.js"></script>
    <script>
        let itemId = <?= $item->get_id() ?>;

        $(function (){
            $("#grid").sortable({
                placeholder: 'ui-state-highlight',
                over: function(event, ui) {
                    var cl = ui.item.attr('class');
                    $('.ui-state-highlight').addClass(cl);
                },
                update: async function(event,ui){
                    var pos = [];
                    $(this).children().each(function(index) {
                        pos.push({
                            id: $(this).attr('data-id'), 
                            priority: index + 1
                        });
                    });
                    // console.log(pos);
                    // let ok = await $.post("item/move_picture_dd/" + itemId,{
                    //     pos: pos
                    // });
                    // console.log(ok);
                    await $.post("item/move_picture_dd/" + itemId,{
                        pos: pos
                    });
                    refreshButtons()
                }
            })
        })

        function refreshButtons() {
            const thumbs = $("#grid .thumb");

            thumbs.find(".btn-left, .btn-right").attr("disabled", false);

            thumbs.first().find(".btn-left").attr("disabled", true);
            thumbs.last().find(".btn-right").attr("disabled", true);
        }
    </script>
</head>

<body>
    <?php 
            $page = "manage_images";
            $title = "Manage Images";
            $page_before = "item/open/" . $item->get_id()."/from-items" . ($encoded ? "/" . $encoded : "");
            include("header.php");
    ?>
 <div class="container">
    <p class="subtitle">Manage Images for "<?= $item->get_title()?>"</p>

    <div class="card">
        <h2>Add New Images</h2>
        <form method="post"
              action="item/upload_images/<?= $item->get_id() . ($encoded ? "/" . $encoded : "")?>"
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

             <div class="grid" id="grid">
                <?php foreach ($pictures as $p): ?>
                    <?php $priority = $p->get_priority(); ?>

                    <!-- <div class="thumb" data-id="?= $p->get_picture_path() ?>"> -->
                    <div class="thumb" data-id="<?= $p->get_picture_path() ?>">
                        <img src="<?= $p->get_picture_path() ?>" alt="item picture">

                      <div class="actions">
                            <!-- gauche -->
                            <form method="post" action="item/move_picture/<?= $item->get_id() ?>/<?= $priority ?>/left">
                                <button class="btn icon btn-left" <?= $priority == 1 ? "disabled" : "" ?>>&#x25C0;</button>
                            </form>

                            <!-- droite -->
                            <form method="post" action="item/move_picture/<?= $item->get_id() ?>/<?= $priority ?>/right">
                                <button class="btn icon btn-right" <?= $priority == $maxPriority ? "disabled" : "" ?>>&#x25B6;</button>
                            </form>

                            <!-- delete -->
                            <form method="post" action="item/delete_picture/<?= $item->get_id() ?>/<?= $priority ?><?= $encoded ? "/" . $encoded : "" ?>">
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
