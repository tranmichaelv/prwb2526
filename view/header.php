<div class="header">
    <?php if($page == "sales" || $page == "purchases" 
    || $page == "edit_profile" || $page == "change_password" 
    || $page == "manage_profile_picture"): ?>
        <div class="header-left">
            <a href="<?= $pageBefore ?>">
                <i class="bi bi-arrow-left"></i>
            </a>
        </div>
    <?php endif; ?>

    <div class="header-center">
        <p><?= $title ?> <i class="bi bi-cart-fill"></i></p>
    </div>

    <?php if($page == "edit_profile" || $page == "change_password"): ?>
        <div class="header-right">
            <!-- <a href="#" onclick="document.getElementById('editProfileForm').submit(); return false;">
                 -->
            <button type="submit">
                <i class="bi bi-floppy"></i>
            <!-- </a> -->
            </button>
        </div>
    <?php endif; ?>
</div>