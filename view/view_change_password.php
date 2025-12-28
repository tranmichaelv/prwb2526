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
        $title = "Change password";
        $pageBefore = "user/profile";
        include("header.php");
    ?>
    
    <div class="section">
        <div class="section-titre">
            <h2>Personal Information</h2>  
        </div>

        <div class="section-menu-input">
            <p class = "section-menu-title">Current Password <sup>*</sup></p>
            <input id="current_password" name="current_password" type="password" placeholder="Enter your current password" value="">
            <p class = "subtitle">Enter your current password to verify your identity</p>

        </div>
    </div>

    <div class="section">
        <div class="section-titre">
            <h2>New Password</h2>
        </div>

        <div class="section-menu-input">
            <p class = "section-menu-title">New Password <sup>*</sup></p>
            <input id="new_password" name="new_password" type="password"  placeholder="Enter your new password" value="">
            <p class = "subtitle">Password must be 8-16 characters with uppercase, number, and punctuation</p>


            <p class = "section-menu-title">Confirm New Password</p>
            <input id="confirm_new_password" name="confirm_new_password" type="password" placeholder="Confirm your new password" value="">
            <p class = "subtitle">Re-enter your new password to confirm it matches</p>
        </div>
    </div>


    <footer> 
        <?php include('footer.php'); ?>
    </footer>
</body>
</html>