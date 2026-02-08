<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css"/>
</head>

<body>

<form action="user/change_password" method="post">
    <?php 
        $title = "Change password";
        $page_before = "user/profile";
        $href = "user/change_password";
        include("header.php");
    ?>
    
        <div class="section">
            <div class="section-titre">
                <h2>Personal Information</h2>  
            </div>

            <div class="section-menu-input">
                <p class = "section-menu-title">Current Password <sup>*</sup></p>
                <input id="current_password" name="current_password" type="password" placeholder="Enter your current password" value="<?= $currentPassword ?>">
                <p class = "subtitle">Enter your current password to verify your identity</p>
                <?php if (!empty($errors['current_password'])): ?>
                    <ul class="error">
                        <?php foreach ($errors['current_password'] as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

            </div>
        </div>

        <div class="section">
            <div class="section-titre">
                <h2>New Password</h2>
            </div>

            <div class="section-menu-input">
                <p class = "section-menu-title">New Password <sup>*</sup></p>
                <input id="new_password" name="new_password" type="password"  placeholder="Enter your new password" value="<?= $newPassword ?>">
                <p class = "subtitle">Password must be 8-16 characters with uppercase, number, and punctuation</p>
                <?php if (!empty($errors['new_password'])): ?>
                    <ul class="error">
                        <?php foreach ($errors['new_password'] as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>


                <p class = "section-menu-title">Confirm New Password</p>
                <input id="confirm_new_password" name="confirm_new_password" type="password" placeholder="Confirm your new password" value="<?= $confirmNewPassword ?>">
                <p class = "subtitle">Re-enter your new password to confirm it matches</p>
                <?php if (!empty($errors['confirm_new_password'])): ?>
                    <ul class="error">
                        <?php foreach ($errors['confirm_new_password'] as $error): ?>
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
</body>
</html>