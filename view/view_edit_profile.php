<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css"/>
</head>

<body>
    <?php 
        $title = "Edit profile";
        $pageBefore = "user/profile";
        include("header.php");
    ?>
    
    <div class="section">
        <div class="section-titre">
            <h2>Personal Information</h2>  
        </div>

        <div class="section-menu-input">
            <p class = "section-menu-title">Full Name <sup>*</sup></p>
            <input id="full_name" name="full_name" type="text"  value="<?= $user->getFullName() ?>">
            <p class = "subtitle">Your complete name as it should appear on your profile</p>

            <p class = "section-menu-title">Username <sup>*</sup></p>
            <input id="username" name="username" type="text"  value="<?= $user->getPseudo() ?>">
            <p class = "subtitle">This will be your public display name on the platform</p>
        </div>
    </div>

    <div class="section">
        <div class="section-titre">
            <h2>Contact & Payement</h2>
        </div>

        <div class="section-menu-input">
            <p class = "section-menu-title">Email Address <sup>*</sup></p>
            <input id="email" name="email" type="text"  value="<?= $user->getEmail() ?>">
            <p class = "subtitle">We'll use this email to send you notifications about your sales and purchases</p>


            <p class = "section-menu-title">IBAN (Optional)</p>
            <input id="iban" name="iban" type="text"  value="<?= $user->getIban() ?>">
            <p class = "subtitle">For receiving payments from your sales. Leave empty if you don't want to receive payements directly</p>

        </div>
    </div>


    <footer> 
        <?php include('footer.php'); ?>
    </footer>
</body>
</html>