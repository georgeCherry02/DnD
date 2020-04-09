function validate_armour_creation() {
    // Validate name is of correct format
    var name = document.getElementById('armour_name').value;
    var nameFormat = /^[a-zA-Z0-9' ]{1,20}$/;
    if (!name.match(nameFormat)) {
        alert("Armour name invalid, look at guidance beside input!");
        return false;
    }
    return true;
}

function validate_duplicate_comparison() {
    // Validate an option is selected
    var yes_option = document.getElementById('compare_yes');
    var no_option = document.getElementById('compare_no');
    if (!yes_option.checked && !no_option.checked) {
        alert("Please select an option to continue!");
        return false;
    }
    return true;
}

function validate_duplicate_resolution() {
    // Validate an option is selected
    var keep_option = document.getElementById('duplicate_resolution_keep');
    var new_option = document.getElementById('duplicate_resolution_new');
    if (!keep_option.checked && !new_option.checked) {
        alert("Please select an option to continue!");
        return false;
    }
    return true;
}

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
        return false;
    }
    return true;
}

function toggle_checkbox(id) {
    var form_field = document.getElementById(id);
    var form_checkbox = document.getElementById(id + "_checkbox");
    form_field.value == "0" ? form_field.value = "1" : form_field.value = "0";
    form_checkbox.classList.toggle("active");
}
function toggle_radio(value, group_name, radio_button_elem) {
    var form_fields = document.getElementsByClassName(group_name + "_radio");
    for (var i = 0; i < form_fields.length; i++) {
        form_fields[i].classList.remove("active");
    }
    radio_button_elem.classList.add("active");

    document.getElementById(group_name + "_radio_input").value = value;

    // Manage aesthetics
    var display_type, location, placeholder = false;
    switch(group_name) {
        case "range_type":
            display_type = value === 1 ? "block" : "none";
            location = "range_distance";
            break;
        case "shape_type":
            display_type = value !== 4 ? "block" : "none";
            location = "shape_size";
            break;
        case "effect":
            display_type = value !== 3 ? "block" : "none";
            location = "create_form_dropdown_amount";
            placeholder = true;
            break;
        default:
            return;
    }

    document.getElementById(location).style.display = display_type;
    document.getElementById(location + "_label").style.display = display_type;
    if (placeholder) {
        document.getElementById(location + "_placeholder").style.display = display_type;
    }

    // Manage form height
    var form = document.getElementsByTagName("form")[0];
    // Get labels container
    var label_container = document.getElementsByClassName("labels_container")[0];
    var labels = label_container.childNodes;
    var input_count = 0;
    for (var i = 0; i < labels.length; i++) {
        if (labels[i].style !== undefined && labels[i].style.display !== "none") {
            input_count++;
        }
    }
    var height = (input_count * 2) + "em";
    form.style.height = height;
}