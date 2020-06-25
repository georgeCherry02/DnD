<div id="create_form_dropdown_features" class="create_form_dropdown features white_background grey_border" onmouseover="expand_feature_dropdown();" onmouseout="this.style.maxHeight='calc(2em - 4px)';">
    <div class="label_container">
        <label class="grey_text">Features:</label>
        <div onclick="add_feature()" class="icon_container">
            <i class="fas fa-plus"></i>
        </div>
        <div onclick="remove_feature()" class="icon_container">
            <i class="fas fa-minus"></i>
        </div>
    </div>
    <div>
        <div class="col-4" id="feature_names">
            <input type="text" name="feature_1_name" id="feature_1_name" class="white_background grey_border grey_text"/>
        </div>
        <div class="col-8" id="feature_descriptions">
            <input type="text" name="feature_1_desc" id="feature_1_desc" class="white_background grey_border grey_text"/>
        </div>
        <input type="hidden" name="feature_amount" id="feature_amount_input" value="1"/>
    </div>
    <script>
        var number_of_features = 1;

        function Feature(name, description) {
            this.sanitiseValue = function(value) {
                const allowed_chars = /^[a-zA-Z0-9 '()!.,-]+$/
                var output = "";
                if (allowed_chars.test(value)) {
                    output = value;
                } else if (value.length > 0) {
                    alert("Unallowed characters in one of the submitted features, it was removed.");
                }
                
                return output;
            }

            this.name = this.sanitiseValue(name);
            this.description = this.sanitiseValue(description);

            this.updateDocument = function(index) {
                document.getElementById("feature_" + index + "_name").value = this.name;
                document.getElementById("feature_" + index + "_desc").value = this.description;
            }
        }

        function add_feature() {
            // Cap features at 10
            if (number_of_features == 10) {
                return false;
            }

            // Gather current features
            var current_features = [];
            for (var i = 1; i <= number_of_features; i++) {
                current_features.push(new Feature(document.getElementById("feature_"+i+"_name").value, document.getElementById("feature_"+i+"_desc").value));
            }

            // Add new inputs
            document.getElementById("feature_names").innerHTML = document.getElementById("feature_names").innerHTML + "<input type='text' name='feature_" + (number_of_features + 1) + "_name' id='feature_" + (number_of_features + 1) + "_name' class='white_background grey_border grey_text'/>";
            document.getElementById("feature_descriptions").innerHTML = document.getElementById("feature_descriptions").innerHTML + "<input type='text' name='feature_" + (number_of_features + 1) + "_desc' id='feature_" + (number_of_features + 1) + "_desc' class='white_background grey_border grey_text'/>";

            // Re-insert old values
            for (var i = 1; i <= current_features.length; i++) {
                current_features[i - 1].updateDocument(i);
            }

            // Increment number of features
            number_of_features++;
            document.getElementById("feature_amount_input").value = number_of_features;

            // Manage UI
            expand_feature_dropdown();
        }

        function expand_feature_dropdown() {
            var max_height = "calc(" + ((number_of_features + 1) * 2) + "em + " + ((number_of_features + 1) * 3) + "px)";
            document.getElementById("create_form_dropdown_features").style.maxHeight = max_height;
        }

        function remove_feature() {
            // Make sure last feature isn't removed

            if (number_of_features == 1) {
                return false;
            }

            // Manage UI
            number_of_features--;
            document.getElementById("feature_amount_input").value = number_of_features;
            expand_feature_dropdown();

            // Get input containers
            var name_inputs = document.getElementById("feature_names");
            var desc_inputs = document.getElementById("feature_descriptions");
            name_inputs.removeChild(name_inputs.childNodes[name_inputs.childNodes.length - 1]);
            desc_inputs.removeChild(desc_inputs.childNodes[desc_inputs.childNodes.length - 1]);
        }
    </script>
</div>
<div id="create_form_dropdown_features_placeholder" class="create_form_placeholder"></div>