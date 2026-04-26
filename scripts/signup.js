let email, errEmail, fullName, errFullName, pseudo, errPseudo, password, errPassword, passwordConfirm, errPasswordConfirm

let config;

async function loadConfig() {
    config = await fetch("user/get_validation_config_service")
        .then(r => r.json());
}

loadConfig();

function checkEmail() {
    let ok = true;
    errEmail.html("");
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.val())) {
        errEmail.append("<p> Must be a valid email address</p>"); 
        ok = false;
    }
    return ok;            
}

function checkFullName() {
    let ok = true;
    errFullName.html("");
    if(fullName.val().length < config.username_min_length) {
        errFullName.append("<p> Full Name must be at least " + config.username_min_length + " characters </p>");
        ok = false;
    }
    return ok;
}

function checkPseudo() {
    let ok = true;
    errPseudo.html("");
    if( pseudo.val().length < config.username_min_length || pseudo.val().length > config.username_max_length) {
        errPseudo.append("<p> Pseudo length must be between " + config.username_min_length + " and " + config.username_max_length + " characters</p>");
        ok = false;
    }
    return ok;
}

function checkPassword() {
    errPassword.html("");
    const hasUpperCase = /[A-Z]/.test(password.val());
    const hasLowerCase = /[a-z]/.test(password.val());
    const hasNumbers = /\d/.test(password.val());
    const hastPunct = /['";:,.\/?\\-]/.test(password.val());

    if(password.val().length < config.password_min_length) {
        errPassword.append("<p> Password length must be greater than " + config.password_min_length + " characters </p>");
        return false;
    }
    if(!(hasUpperCase && hasLowerCase && hasNumbers && hastPunct)) {
        errPassword.append("<p>Password must contain one uppercase, one lowercase, one number and one punctuation mark </p>");
        return false;
    } 

    return true;
}

function checkPasswords() {
    let ok = true;
    errPasswordConfirm.html("");
    if(password.val() !== passwordConfirm.val()) {
        errPasswordConfirm.append("<p>You have to enter twice the same password.</p>");
        ok = false;
    }
    return ok;
}



async function checkPseudoExists() {
    const data = await $.post("user/check_pseudo_signup_service/", { param1:pseudo.val() }, null , "json");
    if(!data.ok) {
        errPseudo.html("<p>Pseudo already exists.</p>");
    }
    
}

async function  checkEmailExists() {
    
    const data = await $.post("user/check_email_signup_service/",  {param1: email.val()}, null , "json"  );
    if(!data.ok) {
        errEmail.html("<p>Email already exists</p>");
        return false;
    }

    errEmail.html("");
    return true;
}

async function checkFullNameExists() {
    
    const data = await $.post("user/check_full_name_signup_service/",  {param1 :fullName.val() }, null , "json" );
    if(!data.ok) {
        errFullName.html("<p> Full Name already exists</p>");
        return false;
    }
    errFullName.html("");
    return true;
}

function checkAll() {
    let ok = checkEmail();
    ok = checkFullName() && ok;
    ok = checkPseudo() && ok;
    ok = checkPassword() && ok;
    ok = checkPasswords() && ok;
    return ok;

}

$(function(){
                email = $("#email");
                errEmail = $("#err_email");
                fullName = $("#full_name");
                errFullName = $("#err_full_name");
                pseudo = $("#pseudo");
                errPseudo = $("#err_pseudo");
                password = $("#password");
                errPassword = $("#err_password");
                passwordConfirm = $("#confirm_password");
                errPasswordConfirm = $("#err_confirm_password");
                    
                email.bind("input", checkEmail);
                email.bind("blur",checkEmailExists);
                fullName.bind("input",checkFullName);
                fullName.bind("blur",checkFullNameExists);
                pseudo.bind("input", checkPseudo);
                pseudo.bind("blur", checkPseudoExists);
                password.bind("input", checkPassword);
                passwordConfirm.bind("input", checkPasswords);
                    
                $("input:text:first").focus();
                }
            );