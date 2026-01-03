<!DOCTYPE html>
<html lang ="en">

<head>

    <meta charset="UTF-8">
    <title>Delete "<?= $item->get_title() ?>"</title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/delete_item.css">
</head>

<body>
   <div class="overlay"> 
    <div class="modal">

        <h2>Are you sure ?</h2>

        <p>
            Do you really want to delete item "<?= $item->get_title() ?>" by ownerName and all of its dependencies ?
            <br>
            This process cannot be undone.
        </p>

        <div class="buttons">
            <a class="btn cancel" href="item/open/<?= $item->get_id() ?>">Cancel</a>

            <form method="post" action="item/delete/<?= $item->get_id() ?>">
                <button type="submit" class="btn danger">Delete</button>
            </form>
        </div>
    </div>
   </div> 

</body>
</html>