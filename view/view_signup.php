<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>index</title>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/styles.css" rel="stylesheet" type="text/css"/>
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
                    <tr><td><input id="email" name="email" type="text" placeholder="mail" value="<?= $email ?>"></td></tr>
                    <tr><td><input id="full_name" name="full_name" type="text" placeholder="Full Name" value="<?= $full_name ?>"></td></tr>
                    <tr><td><input id="pseudo" name="pseudo" type="text" placeholder="Pseudo" value="<?= $pseudo ?>"></td></tr>
                    <tr><td><input id="password" name="password" type="password" placeholder="Password" value="<?= $password ?>"></td></tr>
                    <tr><td><input id="confirm_password" name="confirm_password" type="password" placeholder="Confirm your password"></td></tr>
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