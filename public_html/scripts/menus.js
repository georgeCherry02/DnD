function toggle_navbar() {
    var dropdown = document.getElementById('navbar_dropdown');
    if (dropdown.style.maxHeight === "0px") {
        dropdown.style.maxHeight = "30px";
    } else {
        dropdown.style.maxHeight = "0px";
    }
}