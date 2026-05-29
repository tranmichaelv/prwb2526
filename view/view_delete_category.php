<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Delete "<?= htmlspecialchars($category->get_name()) ?>"</title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/delete_item.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
          
</head>

<body>
<div class="overlay">
    <div class="modal">
        <div class="delete-icon">
            <i class="bi bi-trash-fill"></i>
        </div>

        <h2>Are you sure?</h2>

        <p>
            Do you really want to delete category
            "<?= $category->get_name() ?>"?
            <br>
            This process cannot be undone.
        </p>

        <div class="buttons">
            <a class="btn cancel" href="category/manage">Cancel</a>

            <form method="post" action="category/delete">
                <input type="hidden" name="id" value="<?= $category->get_id() ?>">
                <button type="submit" class="btn danger">Delete</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>