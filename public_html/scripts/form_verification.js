function validate_signup() {
    var username =  document.getElementById('signup_username').value;
    var email = document.getElementById('signup_email').value;
    // Verify Username
    if (username.length < 3) {
        alert("Username is too short!");
        return false;
    } else if (username.length > 15) {
        alert("Username is too long!");
        return false;
    }
    var usernameFormat = /^[a-zA-Z0-9]{3,15}$/;
    if (!username.match(usernameFormat)) {
        alert("You haven't entered a valid username!");
        return false;
    }
    // Verify email
    var emailFormat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    if (!email.match(emailFormat)) {
        alert("You haven't entered a valid email address!");
        return false;
    }
    return true;
}

function validate_password_signup() {
    var pass = document.getElementById('password_choice_field').value;
    var c_pass = document.getElementById('confirm_password_choice_field').value;
    if (pass !== c_pass) {
        alert("Password Mismatch!");
        e.preventDefault();
        return false;
    }
    return true;
}