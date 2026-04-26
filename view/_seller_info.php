<?php 

$src = empty($seller->getPicturePath()) ? "" : $seller->getPicturePath();
?>

<section class="card bg-dark text-white">

    <div class="card-body">
        <small class="text-secondary fs-6 fw-bold">Seller information </small>

        <div class="d-flex align-items-center mt-2">
            <div class="rounded-circle bg-secondary me-3" style="width: 48px; height: 48px;">
                <?php if (!empty($src)): ?>
                    <img src="<?= $src ?>" class="seller-avatar rounded-circle" width="48px">
                <?php endif;?>    
            </div>

            <div>
                <div class="fw-bold"><?= $seller->getPseudo() ?></div>
                <div class="text-secondary fw-bold"> <?= $seller->getRole() ?></div>
            </div>
        </div>
    </div>
</section>