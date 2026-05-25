<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage categories</title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet" type="text/css"/>
</head>

<body class="page-manage-category">

     <?php 
        $title = "Categories";
        $page = "categories";
        include("header.php");
    ?>

    <?php
        $errors = $errors ?? [];
        $error_id = $error_id ?? null;
        $edited_name = $edited_name ?? null;
        $add_errors = $add_errors ?? [];
    ?>

    <div class="container category-box" id="categories-nojs">
    <?php foreach($categories as $index => $category): ?>
        <form class="category-row" method="post">
            <input type="hidden" name="id" value="<?= $category->get_id() ?>">
            <?php $has_error = (int)$error_id === (int)$category->get_id()  && !empty($errors["category_name"]);
                  $value = $has_error ? $edited_name : $category->get_name();?>
                <div class="category-field <?= $has_error ? "has-error" : "" ?>"> 
                <input type="text" name="name" value="<?= htmlspecialchars($value) ?>">
                <?php if ($has_error): ?>
                    <?php foreach ($errors["category_name"] as $error): ?>
                    <div class="field-error">
                    <?= $error ?>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="submit" formaction="category/up" class="btn btn-secondary" <?= $index === 0 ?"disabled" : "" ?>> Up </button>
            <button type="submit" formaction="category/down" class="btn btn-secondary" <?= $index === count($categories) - 1 ?"disabled" : "" ?>> Down </button>
            <button type="submit" formaction="category/save" class="btn btn-save"> Save </button>
            <?php $is_used = $category->is_used(); ?>
            <button type="submit" formaction="category/confirm_delete" class="btn btn-delete" <?= $is_used ? "disabled" : "" ?>> Delete </button>
        </form>
    <?php endforeach; ?>    

    <hr>

    <form class="category-add-row" method="post" action="category/add">
       <div class="category-field <?= !empty($add_errors["category_name"]) ? "has-error" : "" ?>"> 
        <input type="text" name="name" placeholder="Category name" class="category-input">

        <?php if (!empty($add_errors["category_name"])): ?>
            <?php foreach ($add_errors["category_name"] as $error): ?>
                <div class="field-error">
                    <?= $error ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-add"> + </button> 
    </form>
    </div>

    <div id="categories-js" style="display: none;">
           <div class="manage-categories-box">
                <div id="categories-list"></div>
                    
                    <div id="add-category-row" style="display: none;">
                        <span class="drag-drop"></span>
                        <input type="text" id="new-category-name">
                        <button type="button" id="cancel-add-category" class="btn btn-secondary">Cancel</button>
                        <small id="add-category-error" class="field-errors-js"></small>
                    </div>

                    <button type="button" id="show-add-category" class="btn btn-primary">+</button>
           </div>     
    </div>

    <div class="modal fade" id="deleteCategoryModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content delete-modal">
                <div class="modal-header">
                    <h5 class="modal-title">Delete category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" id="deleteCategoryModalBody">
                    Are you sure you want to delete this category ?
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn danger" id="confirm-delete-category">Delete</button>
                </div>
            </div>
        </div>
    </div>

<?php include("footer.php"); ?>
<script src="lib/jquery-4.0.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.3/jquery-ui.min.js"></script><script src="lib/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="scripts/manage_categories.js"></script>

</body>
</html>