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
    <div class="login-page">
        <h2 class="title"><i class="bi bi-cart4"></i> EPayFC</h2>
        <div class="login">
            <form action="user/login" method="post">
                <table>
                    <thead>
                        <tr><th><h2>Sign in</h2></th></tr>
                    </thead>

                    <tbody>
                        <tr><td><i class="bi bi-person"></i> <input id="email" name="email" type="email" placeholder="Email" value="<?= $email ?>"></td></tr>
                        <tr><td><i class="bi bi-key"></i> <input id="password" name="password" type="password" placeholder="••••••" value="<?= $password ?>"></td></tr>
                        <tr><td><input type="submit" name="login" value="Log In"></td></tr>
                        <tr><td><input type="submit" name="guest_login" value="Continue as Guest"></td></tr>
                        <tr><td><a href="user/signup">New here ? Click here to subsribe !</a></td></tr>
                    </tbody>

                    <tfoot>
                        <tr><th>For Debug Purpose</th></tr>
                        <tr><td><a href="user/loginAsBoris">Login as boverhaegen@epfc.eu</a></td></tr>
                        <tr><td><a href="user/loginAsMarc">Login as mamichel@epfc.eu</a></td></tr>
                        <tr><td><a href="user/loginAsQuentin">Login as quhouben@epfc.eu</a></td></tr>
                        <tr><td><a href="user/loginAsXavier">Login as xapigeolet@epfc.eu</a></td></tr>
                        <tr><td><a href="setup">Restore original data</a></td></tr>
                        <tr><td><a href="setup/export">Backup personal data</a></td></tr>
                        <tr><td><a href="setup/restore">Restore personal data</a></td></tr>
                    </tfoot>
                </table>
            </form>
        </div>
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

    <footer> 
        <?php include('time.php'); ?>
    </footer>
</body>

</html>