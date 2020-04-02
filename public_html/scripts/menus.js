function toggle_navbar() {
    var dropdown = document.getElementById('navbar_dropdown');
    if (dropdown.style.maxHeight === "0px") {
        dropdown.style.maxHeight = "30px";
    } else {
        dropdown.style.maxHeight = "0px";
    }
}

function toggle_login_form() {
    var login_cont = document.getElementById('login_container');
    var signup_cont = document.getElementById('signup_container');
    if (login_cont.style.display == "none") {
        login_cont.style.display = "inline-block";
        signup_cont.style.display = "none";
    } else {
        signup_cont.style.display = "inline-block";
        login_cont.style.display = "none";
    }
}

function close_popup_message() {
    document.getElementById('popup_message').style.display = "none";
}