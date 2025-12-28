<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>index</title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>
<div class="signup-page">
    <div class="login">
        <form action="user/signup" method="post">
            <table>
                <thead>
                    <tr><th><h2>Sign up</h2></th></tr>
                </thead>

                <tbody>
                    <tr><td><i class="bi bi-envelope"></i> <input id="email" name="email" type="email" placeholder="Email" value="<?= $email ?>"></td></tr>
                    <tr><td><i class="bi bi-person"></i> <input id="full_name" name="full_name" type="text" placeholder="Full Name" value="<?= $full_name ?>"></td></tr>
                    <tr><td><i class="bi bi-file-person"></i> <input id="pseudo" name="pseudo" type="text" placeholder="Pseudo" value="<?= $pseudo ?>"></td></tr>
                    <tr><td><i class="bi bi-key"></i> <input id="password" name="password" type="password" placeholder="Password" value="<?= $password ?>"></td></tr>
                    <tr><td><i class="bi bi-key"></i> <input id="confirm_password" name="confirm_password" type="password" placeholder="Confirm your password"></td></tr>
                    <tr><td><input type="submit" name="signup" value="Sign Up"></td></tr>
                    <tr><td><input type="submit" name="cancel" value="Cancel"></td></tr>
                </tbody>

            </table>
        </form>
        <?php if (count($errors) != 0): ?>
            <div class='errors'>
                <p>Please correct the following error(s) :</p>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>

</html>