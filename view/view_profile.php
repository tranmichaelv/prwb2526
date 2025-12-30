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
        $title = "Profile";
        include("header.php");
    ?>

    <div class="account-info">
        <img src="<?= $user->getPicturePath() ?>" alt="Avatar" class="avatar" />
        <h1><?= $user->getFullName()?></h1>
        <p class="username">@<?= $user->getPseudo()?></p>
        <p class="email"><?= $user->getEmail()?></p>
    </div>
    
    <div class="section">
        <div class="section-titre">
            <h2>My Activities</h2>  
        </div>

        <div class="section-menu">
            <a href="item/sales" class="item">
                <i class="bi bi-receipt"></i>
                <div class="display-colonne">
                    <strong>Sales</strong>
                    <p>Items you have sold</p>
                </div>
            </a>

            <a href="item/purchases" class="item">
                <i class="bi bi-cart3"></i>
                <div class="display-colonne">
                    <strong>Purchases</strong>
                    <p>Items you have purchased</p>
                </div>
            </a>
        </div>
    </div>

    <div class="section">
        <div class="section-titre">
            <h2>Account Settings</h2>
        </div>

        <div class="section-menu">

        <a href="user/edit_profile" class="item">
            <i class="bi bi-person-gear"></i>
            <div class="display-colonne">
                <strong>Edit Profile</strong>
                <p>Update your personnal informations</p>
            </div>
        </a>

        <a href="user/change_password" class="item">
            <i class="bi bi-three-dots"></i>
            <div class="display-colonne">
                <strong>Change Password</strong>
                <p>Update your account security</p>
            </div>
        </a>

        <a href="user/manage_profile_picture" class="item">
            <i class="bi bi-person-circle"></i>
            <div class="display-colonne">
                <strong>Profile Picture</strong>
                <p>Upload or change your profile picture</p>
            </div>
        </a>

        <a href="user/logout" class="item danger">
            <i class="bi bi-box-arrow-right"></i>
            <div class="display-colonne">
                <strong>Logout</strong>
                <p>Sign out of your account</p>
            </div>
        </a>

        </div>
    </div>



    <footer> 
        <?php include('footer.php'); ?>
    </footer>
</body>
</html>