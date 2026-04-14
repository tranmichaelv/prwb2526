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
        <form action="user/signup" method="post" onsubmit="return checkAll();">
            <table>
                <thead>
                    <tr><th><h2>Sign up</h2></th></tr>
                </thead>

                <tbody>
                    <tr>
                        <td>
                            <i class="bi bi-envelope"></i> 
                            <input id="email" name="email" type="email" placeholder="Email" value="<?= $email ?>">
                            <div id="err_email" class="error"></div>
                            <?php if (!empty($errors['email'])): ?>
                            <ul class="error">
                                <?php foreach ($errors['email'] as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?> 
                            </ul>
                        <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <i class="bi bi-person"></i> 
                            <input id="full_name" name="full_name" type="text" placeholder="Full Name" value="<?= $full_name ?>">
                            <div id="err_full_name" class="error"></div>
                            <?php if (!empty($errors['full_name'])): ?>
                                <ul class="error">
                                    <?php foreach ($errors['full_name'] as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <i class="bi bi-file-person"></i> 
                            <input id="pseudo" name="pseudo" type="text" placeholder="Pseudo" value="<?= $pseudo ?>">
                            <div id="err_pseudo" class="error"></div>
                            <?php if (!empty($errors['pseudo'])): ?>
                                <ul class="error">
                                    <?php foreach ($errors['pseudo'] as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <i class="bi bi-key"></i> 
                            <input id="password" name="password" type="password" placeholder="Password" value="<?= $password ?>">
                            <div id="err_password" class="error"></div>
                            <?php if (!empty($errors['password'])): ?>
                                <ul class="error">
                                    <?php foreach ($errors['password'] as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <i class="bi bi-key"></i> 
                            <input id="confirm_password" name="confirm_password" type="password" placeholder="Confirm your password"  value="<?= $confirm ?>">
                            <div id="err_confirm_password" class="error"></div>
                        </td>
                    </tr>
                    <tr><td><input type="submit" name="signup" value="Sign Up"></td></tr>
                    <tr><td><input type="submit" name="cancel" value="Cancel"></td></tr>
                </tbody>

            </table>
        </form>
    </div>
</div>
<script src="lib\jquery-4.0.0.min.js"></script>
<script src="scripts\signup.js"></script>
</body>

</html>