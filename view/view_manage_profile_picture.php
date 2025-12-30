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
        $title = "Manage profile picture";
        $pageBefore = "user/profile";
        include("header.php");
    ?>
    
    <div class="section">
        <div class="section-titre">
            <h2>Current Profile Picture</h2>  
        </div>

        <div class="picture-change">
            <img src="<?= $user->getPicturePath() ?>" alt="Avatar" class="avatar" />
            <h1><?= $user->getFullName()?></h1>
            <p class="username">@<?= $user->getPseudo()?></p>
        </div>
    </div>



    <div class="section">
        <div class="section-titre">
            <h2>Upload New Picture</h2>
        </div>

        <div class="section-menu-input">
            <p class = "section-menu-title">Select Image <sup>*</sup></p>
            <input id="file" name="file" type="file"  value="">
            <p class = "subtitle">Supported formats: JPG, PNG, GIF, WebP. This will replace your current profile picture.</p>
            <input class="button-upload" id="upload" name="upload" type="button"  value="Upload Picture">
        </div>
    </div>

    <div class="section">
        <div class="section-titre">
            <h2>Remove Current Picture</h2>
        </div>

        <div class="section-menu-input">
            <div class="remove-picture">
                <p>This will permanently delete your current profile picture. You can upload a new one anytime</p>
                <input id="delete" name="delete" type="button"  value="Delete Picture">
            </div>
        </div>
    </div>


    <footer> 
        <?php include('footer.php'); ?>
    </footer>
</body>
</html>