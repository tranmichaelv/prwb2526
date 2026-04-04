<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css"/>
</head>

<body>

<form id="edit-profile-form" action="user/edit_profile" method="post">
    <?php 
        $title = "Edit profile";
        $page_before = "user/profile";
        $href = "user/edit_profile";
        include("header.php");
    ?>

        <div class="section">
            <div class="section-titre">
                <h2>Personal Information</h2>  
            </div>

            <div class="section-menu-input">
                <p class = "section-menu-title">Full Name <sup>*</sup></p>
                <input id="full_name" name="full_name" type="text"  value="<?= $_POST['full_name'] ?? $user->getFullName() ?>">
                <div class="error-message" id="full_name_error"></div>
                <p class = "subtitle">Your complete name as it should appear on your profile</p>
                <?php if (!empty($errors['full_name'])): ?>
                    <ul class="error">
                        <?php foreach ($errors['full_name'] as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <p class = "section-menu-title">Username <sup>*</sup></p>
                <input id="pseudo" name="pseudo" type="text"  value="<?=$_POST['pseudo'] ?? $user->getPseudo() ?>">
                <div class="error-message" id="pseudo_error"></div>
                <p class = "subtitle">This will be your public display name on the platform</p>
               <?php if (!empty($errors['pseudo'])): ?>
                  <ul class="error">
                    <?php foreach ($errors['pseudo'] as $error): ?>
                     <li><?= $error ?></li>
                    <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
            </div>
        </div>

        <div class="section">
            <div class="section-titre">
                <h2>Contact & Payement</h2>
            </div>

            <div class="section-menu-input">
                <p class = "section-menu-title">Email Address <sup>*</sup></p>
                <input id="email" name="email" type="email"  value="<?= $_POST['email'] ?? $user->getEmail() ?>">
                <div class="error-message" id="email_error"></div>
                <p class = "subtitle">We'll use this email to send you notifications about your sales and purchases</p>
                <?php if (!empty($errors['email'])): ?>
                    <ul class="error">
                        <?php foreach ($errors['email'] as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>


                <p class = "section-menu-title">IBAN (Optional)</p>
                <input id="iban" name="iban" type="text"  value="<?= $_POST['iban'] ?? $user->getIban() ?? ''  ?>">
                <div class="error-message" id="iban_error"></div>
                <p class = "subtitle">For receiving payments from your sales. Leave empty if you don't want to receive payements directly</p>
                <?php if (!empty($errors['iban'])): ?>
                    <ul class="error">
                        <?php foreach ($errors['iban'] as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

            </div>
        </div>
    </form>


    <footer> 
        <?php include('footer.php'); ?>
    </footer>
    <script src="scripts/edit_profile.js"></script>
</body>
</html>