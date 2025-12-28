<div class="header">
    <?php if($page == "sales" || $page == "purchases" 
    || $page == "edit_profile" || $page == "change_password" 
    || $page == "manage_profile_picture"): ?>
        <div class="header-left">
            <?php if (!empty($_SESSION['previous_page'])): ?>
                <a href="<?= htmlspecialchars($_SESSION['previous_page']) ?>">
                    <i class="bi bi-arrow-left"></i>
                </a>
            <?php endif; ?>
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