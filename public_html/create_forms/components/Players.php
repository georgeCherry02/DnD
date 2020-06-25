<div id="create_form_dropdown_players" class="create_form_dropdown players white_background grey_border" onmouseover="expand_player_dropdown()" onmouseout="this.style.maxHeight='calc(2em - 4px)';">
    <div class="label_container">
        <label class="grey_text">Players (Except You):</label>
        <div onclick="add_player()" class="icon_container">
            <i class="fas fa-plus"></i>
        </div>
        <div onclick="remove_player()" class="icon_container">
            <i class="fas fa-minus"></i>
        </div>
    </div>
    <div>
        <div class="col-8" id="player_names">
            <input type="text" name="player_1_name" id="player_1_name" class="white_background grey_border grey_text"/>
        </div>
        <div class="col-4" id="player_colours">
            <input type="text" name="player_1_colour" id="player_1_colour" class="white_background grey_border grey_text"/>
        </div>
        <input type="hidden" name="player_amount" id="player_amount_input" value="1"/>
    </div>
    <script>
        var number_of_players = 1;
        function Player(name, colour) {
            this.sanitiseName = function(value) {
                const allowed_chars = /^[a-zA-Z0-9.]+$/
                var output = "";
                if (allowed_chars.test(value)) {
                    output = value;
                } else if (value.length > 0) {
                    alert("Unallowed characters in name");
                }
                return output;
            }
            this.sanitiseColour = function(value) {
                const hex_regex = /^#[a-f0-9]{6}$/;
                var output = "";
                if (hex_regex.test(value)) {
                    output = value;
                } else if (value.length > 0) {
                    alert("Invalid hex colour provided");
                }
                return output;
            }

            this.name = this.sanitiseName(name);
            this.colour = this.sanitiseColour(colour);

            this.updateDocument = function(index) {
                document.getElementById("player_"+index+"_name").value = this.name;
                document.getElementById("player_"+index+"_colour").value = this.colour;
            }
        }
        function add_player() {
            // Cap players at 6
            if (number_of_players == 6) {
                return false;
            }

            // Gather all current players
            var current_players = [];
            for (var i = 1; i <= number_of_players; i++) {
                current_players.push(new Player(document.getElementById("player_"+i+"_name").value, document.getElementById("player_"+i+"_colour").value));
            }

            // Add new inputs
            document.getElementById("player_names").innerHTML = document.getElementById("player_names").innerHTML + "<input type='text' name='player_" + (number_of_players + 1) + "_name' id='player_" + (number_of_players + 1) + "_name' class='white_background grey_border grey_text'/>";
            document.getElementById("player_colours").innerHTML = document.getElementById("player_colours").innerHTML + "<input type='text' name='player_" + (number_of_players + 1) + "_colour' id='player_" + (number_of_players + 1) + "_colour' class='white_backgorund grey_border grey_text'/>";

            // Re-insert old values
            for (var i = 1; i <= current_players.length; i++) {
                current_players[i - 1].updateDocument(i);
            }

            // Increment number of players
            number_of_players++;
            document.getElementById("player_amount_input").value = number_of_players;

            // Manage UI
            expand_player_dropdown();
        }

        function expand_player_dropdown() {
            var max_height = "calc(" + ((number_of_players + 1) * 2) + "em + " + ((number_of_players + 1) * 3) + "px)";
            document.getElementById("create_form_dropdown_players").style.maxHeight = max_height;
        }

        function remove_player() {
            // Make sure it's not last player
            if (number_of_players == 1) {
                return false;
            }

            // Manage UI
            number_of_players--;
            document.getElementById("player_amount_input").value = number_of_players;
            expand_player_dropdown();

            // Get input containers
            var name_inputs = document.getElementById("player_names");
            var colour_inputs = document.getElementById("player_colours");
            name_inputs.removeChild(name_inputs.childNodes[name_inputs.childNodes.length - 1]);
            colour_inputs.removeChild(colour_inputs.childNodes[colour_inputs.childNodes.length - 1]);
        }
    </script>
</div>
<div id="create_form_dropdown_features_placeholder" class="create_form_placeholder"></div>