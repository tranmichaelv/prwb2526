<?php if($user):?>
    <div class="menu">
        <a href="item/browse"><i class="bi bi-search"></i> Browse</a>
        <a href="item/list"><i class="bi bi-house-door"></i> My Items</a>
        <a href="item/add"><i class="bi bi-plus"></i> Add Offer</a>

        <?php if($user->getRole() === "admin"): ?>
            <a href="category/manage"><i class="bi bi-folder fill"></i> Categories</a>
        <?php endif; ?>    
        <a href="user/profile"><i class="bi bi-gear"></i> Profile</a>
    </div>
<?php else: ?>
    <div class="menu">
        <a href="item/browse"><i class="bi bi-search"></i> Browse</a>
        <a href="user/signup"><i class="bi bi-house-door"></i> Join Us</a>
    </div>
<?php endif; ?>