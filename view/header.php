<div class="header">

    <?php 
        // Pages qui ont un bouton back
        $showBack = in_array($page, ["sales", "purchases", "edit_profile", "change_password", "manage_profile_picture"]);

        // Pages qui ont un bouton save
        $showSave = in_array($page, ["edit_profile", "change_password", "manage_profile_picture"]);
    ?>

    <?php if ($showBack): ?>
        <div class="header-left">
            <a href="<?= $pageBefore ?>">
                <i class="bi bi-arrow-left"></i>
            </a>
        </div>
    <?php endif; ?>

    <div class="header-center">
        <p><?= $title ?> <i class="bi bi-cart-fill"></i></p>
    </div>

    <?php if ($showSave): ?>
        <div class="header-right">
            <button type="submit">
                <i class="bi bi-floppy"></i>
            </button>
        </div>
    <?php elseif($showBack): ?>
        <div class="header-right"></div>
    <?php endif; ?>
</div>
