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
    <div class="title"><h2>EPayFC</h2></div>
    <div class="login">
        <form action="login/login" method="post">
            <table>
                <thead>
                    <tr><th><h2>Sign in</h2></th></tr>
                </thead>

                <tbody>
                    <tr><td>Pseudo:</td><td><input id="pseudo" name="pseudo" type="text" value="<?= $pseudo ?>"></td></tr>
                    <tr><td>Password:</td><td><input id="password" name="password" type="password" value="<?= $password ?>"></td></tr>
                    <tr><td><input type="submit" name="login" value="Log In"></td></tr>
                    <tr><td><input type="submit" name="guest_login" value="Continue as Guest"></td></tr>
                </tbody>

                <tfoot>
                    <tr><th>For Debug Purpose</th></tr>
                    <tr><td><a href="index.php?controller=login&action=loginAsBoris">Login as boverhaegen@epfc.eu</a></td></tr>
                    <tr><td><a href="index.php?controller=login&action=loginAsMarc">Login as mamichel@epfc.eu</a></td></tr>
                    <tr><td><a href="index.php?controller=login&action=loginAsQuentin">Login as quhouben@epfc.eu</a></td></tr>
                    <tr><td><a href="index.php?controller=login&action=loginAsXavier">Login as xapigeolet@epfc.eu</a></td></tr>
                </tfoot>
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
    <footer> 
        <?php include('time.php'); ?>
    </footer>
</body>

</html>