function validate_armour_creation() {
    // Validate name is of correct format
    var name = document.getElementById('armour_name').value;
    if (!validate_create_name(name)) {
        alert("Armour name invalid, look at guidance beside input!");
        return false;
    }
    return true;
}
function validate_spell_creation() {
    // Validate name is of correct format
    var name = document.getElementById('spell_name').value;
    if (!validate_create_name(name)) {
        alert("Spell name invalid, look at guidance beside input!");
        return false;
    }
    return true;
}
function validate_stat_block_creation() {
    // Validate name is of correct format
    var name = document.getElementById("npc_name").value;
    if (!validate_create_name(name)) {
        alert("This NPC's name is invalid, look at guidance beside input!");
        return false;
    }
    return true;
}
function validate_weapon_creation() {
    // Validate name is correct format
    var name = documnet.getElementById('weapon_name').value;
    if (!validate_create_name(name)) {
        alert("Weapon name invalid, look at guidance beside input!");
        return false;
    }
    return true;
}
function validate_create_name(name) {
    const nameFormat = /^[a-zA-Z0-9' ]{1,25}$/;
    return name.match(nameFormat);
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
function toggle_damage_checkbox(damage_type, hide = false) {
    var damage_type_checkbox = document.getElementById(damage_type + "_check");
    var damage_type_label = document.getElementById(damage_type + "_label");
    var damage_type_input_container = document.getElementById("create_form_dropdown_" + damage_type + "_damage");
    var damage_type_input_placeholder = document.getElementById("create_form_dropdown_" + damage_type + "_damage_placeholder");
    if (!hide) {
        damage_type_checkbox.classList.toggle("active");
    } else {
        damage_type_checkbox.classList.remove("active");
    }
    var display_type = damage_type_label.style.display == "none" && !hide ? "block" : "none";
    damage_type_label.style.display = display_type;
    damage_type_input_container.style.display = display_type;
    damage_type_input_placeholder.style.display = display_type;
    if (display_type == "none") {
        var num_inputs = document.querySelectorAll("#create_form_dropdown_" + damage_type + "_damage input");
        for (var i = 0; i < num_inputs.length; i++) {
            num_inputs[i].value = 0;
        }
    }
}
function toggle_weapon_prop_checkbox(property) {
    // Check if it's initially a ranged weapon
    var ranged_check = document.getElementById("Ammunition_property").value + document.getElementById("Loading_property").value + document.getElementById("Thrown_property").value > 0;
    if (ranged_check && property == "Range") {
        return;
    }
    toggle_checkbox(property + "_property");
    if (property == "Versatile") {
    // If property is Versatile reveal versatile damage field
        var versatile_damage_label = document.getElementById("versatile_damage_label");
        var versatile_damage_dropdown = document.getElementById("create_form_dropdown_versatile_damage");
        var versatile_damage_placeholder = document.getElementById("create_form_dropdown_versatile_damage_placeholder");
        var display_type = versatile_damage_label.style.display == "none" ? "block" : "none";
        versatile_damage_label.style.display = display_type;
        versatile_damage_dropdown.style.display = display_type;
        versatile_damage_placeholder.style.display = display_type;
    } else if (property == "Ammunition" || property == "Loading" || property == "Thrown") {
    // If property is Ammunition, Loading or Thrown toggle the Ranged field appropriately
        if (document.getElementById(property + "_property").value == 1) {
            document.getElementById("Range_property").value = 1;
            if (!document.getElementById("Range_property_checkbox").classList.contains("active")) {
                document.getElementById("Range_property_checkbox").classList.add("active");
            }
        } else {
            var ranged_weapon = document.getElementById("Ammunition_property").value + document.getElementById("Loading_property").value + document.getElementById("Thrown_property").value > 0;
            if (!ranged_weapon) {
                document.getElementById("Range_property").value = 0;
                document.getElementById("Range_property_checkbox").classList.remove("active");
            }
        }
    }

    // Reveal or remove the range input fields according to the ranged field value
    var display_type = document.getElementById("Range_property").value == 1 ? "block" : "none";
    var fields = ["Effective_Range", "Maximum_Range"];
    for (var i = 0; i < fields.length; i++) {
        document.getElementById(fields[i] + "_label").style.display = display_type;
        document.getElementById(fields[i]).style.display = display_type;
    }
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
        case "shape":
            display_type = value !== 4 ? "block" : "none";
            location = "shape_size";
            break;
        case "effect":
            handle_spell_effect_change(value);
            return;
        default:
            return;
    }

    document.getElementById(location).style.display = display_type;
    document.getElementById(location + "_label").style.display = display_type;
    if (placeholder) {
        document.getElementById(location + "_placeholder").style.display = display_type;
    }
}

function handle_spell_effect_change(value) {
    document.getElementById("damage_types_label").style.display = "none";
    document.getElementById("create_form_dropdown_damage").style.display = "none";
    document.getElementById("create_form_dropdown_damage_types_placeholder").style.display = "none";
    document.getElementById("healing_amount_label").style.display = "none";
    document.getElementById("create_form_dropdown_healing").style.display = "none";
    document.getElementById("create_form_dropdown_healing_placeholder").style.display = "none";
    for (var i = 0; i < DAMAGE_TYPES.length; i++) {
        toggle_damage_checkbox(DAMAGE_TYPES[i], true);
    }
    var num_inputs = document.querySelectorAll("#create_form_dropdown_healing input");
    for (var i = 0; i < num_inputs.length; i++) {
        num_inputs[i].value = 0;
    }
    // A value of 3 represents an rp role yet there's no other field necessary in this case
    // Need to make the individual damage types disappear too
    // Finally need to get rid of all values that don't exist either
    switch(value) {
        case 1:
            // Damage value
            document.getElementById("damage_types_label").style.display = "block";
            document.getElementById("create_form_dropdown_damage").style.display = "block";
            document.getElementById("create_form_dropdown_damage_types_placeholder").style.display = "block";
            break;
        case 2:
            // Heal value
            document.getElementById("healing_amount_label").style.display = "block";
            document.getElementById("create_form_dropdown_healing").style.display = "block";
            document.getElementById("create_form_dropdown_healing_placeholder").style.display = "block";
            break;
    }
}