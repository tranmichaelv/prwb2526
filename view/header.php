<div class="header">
    <?php if($page == "sales" || $page == "purchases" 
    || $page == "edit_profile" || $page == "change_password"): ?>
        <div class="header-left">
            <a href="<?= $pageBefore ?>"><i class="bi bi-arrow-left"></i></a>
        </div>
    <?php endif; ?>

    <div class="header-center">
        <p><?= $title ?> <i class="bi bi-cart-fill"></i></p>
    </div>

    <?php if($page == "edit_profile" || $page == "change_password"): ?>
        <div class="header-right">
            <a href=""><i class="bi bi-floppy"></i></a>
        </div>
    <?php endif; ?>
</div>