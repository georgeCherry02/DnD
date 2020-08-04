with (paper) {
    var game = {
        animate: true
    };
    // Define basic properties
    game.canvas = document.getElementById("game_canvas");
    game.canvas_size = new Size(1024, BACKGROUND_HEIGHT);
    game.puddles = [];
    game.markers = {};
    game.markers.list = [];
    game.markers.paths = [];
    game.radius_list = [];
    game.grid = {};
    game.grid.state = {};
    // Initialise mouse tools
    game.initialise = function() {
        setup(this.canvas);
        this.base_layer = new Layer();
        this.canvas_container = new Path.Rectangle(new Point(0, 0), game.canvas_size);
        this.marker_layer = new Layer();
        this.marker_layer.opacity = 0.7;
        this.radius_layer = new Layer();
        this.radius_layer.opacity = 0.5;
        this.fog_layer  = new Layer();
        this.fog_layer.opacity = 0.95;
        this.mouse_puddle_layer = new Layer();
        this.mouse_circling_layer = new Layer();
        this.mouse_circling_layer.opacity = 0.5;
        this.mouse_track_layer = new Layer();

        // Initialise custom mouse tracker
        this.mouse_circling_layer.activate()
        this.mouse_circling_marker = new Path.Circle({
            center: new Point(-15, -15),
            radius: 5
        });
        this.mouse_circling_marker.fillColor = PLAYER_COLOURS[PLAYER_ID];
        this.mouse_track_layer.activate();
        this.mouse_marker = new Path.Circle({
            center: new Point(-1, -1),
            radius: 1
        });
        this.mouse_marker.fillColor = PLAYER_COLOURS[PLAYER_ID];

        this.point_tool = new Tool();
        this.point_tool.onMouseDown = function(event) {
            game.sendPuddle(event.point);
            view.draw();
            event.stopPropagation();
        }
        this.point_tool.onMouseMove = function(event) {
            game.updateMouseAppearance(event.point);
            view.draw();
            event.stopPropagation();
        }
        this.fog_tool = new Tool();
        this.fog_tool.onMouseDown = function(event) {
            // verify point's within canvas
            if (game.canvas_container.contains(event.point)) {
                game.owner.reInitialiseFogPath(event.point);
            }
            event.stopPropagation();
        }
        this.fog_tool.onMouseDrag = function(event) {
            // verify point's within canvas
            if (game.canvas_container.contains(event.point)) {
                game.owner.addToFogPath(event.point);
            } else {
                game.owner.updateFog();
            }
            game.updateMouseAppearance(event.point);
            event.stopPropagation();
        }
        this.fog_tool.onMouseUp = function(event) {
            game.owner.updateFog();
            event.stopPropagation();
        }
        this.fog_tool.onMouseMove = function(event) {
            game.updateMouseAppearance(event.point);
            view.draw();
            event.stopPropagation();
        }
        this.marker_tool = new Tool();
        this.marker_tool.onMouseDown = function(event) {
            game.owner.checkMarker(event.point);
            view.draw();
            event.stopPropagation();
        }
        this.marker_tool.onMouseMove = function(event) {
            game.updateMouseAppearance(event.point);
            view.draw();
            event.stopPropagation();
        }
        this.radius_tool = new Tool();
        this.radius_tool.onMouseDown = function(event) {
            game.place_radius_marker(event.point);
            view.draw();
            event.stopPropagation();
        }
        this.radius_tool.onMouseMove = function(event) {
            game.updateMouseAppearance(event.point);
            view.draw();
            event.stopPropagation();
        }
        this.measure_tool = new Tool();
        this.measure_tool.onMouseDrag = function(event) {
            game.updateMouseAppearance(event.point);
            var measure_tape = new Path.Line(event.downPoint, event.point);
            measure_tape.strokeColor = "black";
            // Determine length
            var pixel_length = measure_tape.length;
            var square_length = pixel_length / game.grid.smaller_dimension;
            var feet_length = Math.floor(square_length * 5);
            console.log("Feet: "+feet_length);
            var angle = new Point(event.point.x - event.downPoint.x, event.point.y - event.downPoint.y).angle;
            console.log("Angle: "+angle);
            var text_y_position = event.downPoint.y - 8;
            if (angle < 0) {
                text_y_position += 23;
            }
            var measure_text = new PointText(new Point(event.downPoint.x - 5, text_y_position));
            measure_text.content = feet_length;

            measure_tape.removeOn({
                drag: true,
                down: true,
                up: true
            });
            measure_text.removeOn({
                drag: true,
                down: true,
                up: true
            });
        }
        this.measure_tool.onMouseMove = function(event) {
            game.updateMouseAppearance(event.point);
            view.draw();
            event.stopPropagation();
        }
        this.point_tool.activate();

        this.addConnection(PLAYER_ID);
        this.renderBackground();
        this.grid.generate();
    }

    game.use_point_tool = function() {
        this.point_tool.activate();
    }
    game.use_fog_tool = function() {
        this.fog_tool.activate();
    }
    game.use_marker_tool = function(type) {
        if (type == "npc") {
            this.markers.colour = document.getElementById("marker_colour").value;
        } else {
            this.markers.colour = PLAYER_COLOURS[type];
        }
        this.marker_tool.activate();
    }
    game.use_radius_tool = function() {
        this.radius_tool.activate();
    }
    game.use_measure_tool = function() {
        this.measure_tool.activate();
    }
    game.updateMouseAppearance = function(point_location) {
        this.mouse_track_layer.activate();
        this.mouse_circling_marker.position = point_location;
        this.mouse_marker.position = point_location;
    }
    game.addPuddle = function(point_location, id) {
        this.mouse_puddle_layer.activate();
        var puddle = new Path.Circle({
            center: point_location,
            radius: 5
        })
        puddle.strokeWidth = 4;
        puddle.strokeColor = PLAYER_COLOURS[id];
        puddle.opacity = 0.6;
        this.puddles.push(puddle);
    }
    game.handlePuddles = function() {
        for (var i = 0; i < this.puddles.length; i++) {
            this.puddles[i].scale(1.1);
            var init_opacity = this.puddles[i].opacity;
            this.puddles[i].opacity = init_opacity - 0.04;
            if (this.puddles[i].opacity < 0) {
                this.puddles[i].remove();
                this.puddles.splice(i, 1);
            }
        }
    }
    game.place_radius_marker = function(event_location) {
        this.radius_layer.activate();
        var grid_coords = this.grid.determineCoordinates(event_location);
        var radius_feet = $("#radius_tool_value").val();
        var radius_squares = radius_feet / 5;
        var radius_pixels = radius_squares * this.grid.smaller_dimension / 2;
        var colour = $("#radius_tool_colour").val();
        var path_location = new Point((grid_coords[0] + 1/2) * this.grid.cell_width, (grid_coords[1] + 1/2) * this.grid.cell_height);
        var path = new Path.Circle({
            center: path_location,
            radius: radius_pixels
        });
        path.fillColor = colour;
        this.radius_list.push(path);
    }
    game.clear_radius_markers = function() {
        for (var i = 0; i < this.radius_list.length; i++) {
            this.radius_list[i].remove();
        }
        this.radius_list = [];
    }   
    game.swivelChevron = function(elem) {
        var rotation = elem.style.transform == "rotate(180deg)" ? "rotate(0)" : "rotate(180deg)";
        elem.style.transform = rotation;
    }
    game.addConnection = function() {
        var process = "addConnection";
        var data = {
            "game_id":      GAME_ID,
            "player_id":    PLAYER_ID
        };
        $.ajax({
            type:   "POST",
            url:    "api.php",
            data:   {
                "ajax_token":   AJAX_TOKEN,
                "process":      process,
                "data":         JSON.stringify(data)
            },
            success: function(data) {
                var response = JSON.parse(data);
                if (response.status != "Success") {
                    window.location.href = "default.php";
                }
            },
            error: function() {
                console.log("Error!");
            }
        });
    }
    game.removeConnection = function() {
        var process = "removeConnection";
        var data = {
            "game_id":      GAME_ID,
            "player_id":    PLAYER_ID
        };
        $.ajax({
            type:   "POST",
            url:    "api.php",
            data:   {
                "ajax_token":   AJAX_TOKEN,
                "process":      process,
                "data":         JSON.stringify(data)
            },
            success: function(data) {
                var response = JSON.parse(data);
                if (response.status !== "Success") {
                    // Not sure if something needs to be done here...
                }
            },
            error: function() {
                console.log("Error!");
            }
        })
    }
    game.sendPuddle = function(location) {
        var process = "addPuddle";
        var data = {
            "game_id":      GAME_ID,
            "player_id":    PLAYER_ID,
            "point_x":      location.x,
            "point_y":      location.y
        };
        $.ajax({
            type:   "POST",
            url:    "api.php",
            data:   {
                "ajax_token":   AJAX_TOKEN,
                "process":      process,
                "data":         JSON.stringify(data)
            },
            success: function(data) {
                var response = JSON.parse(data);
                if (response.status != "Success") {
                    console.log("Error message: "+response.error_message);
                }
            },
            error: function() {
                console.log("Error!");
            }
        })
    }
    game.fetchPuddles = function() {
        var process = "fetchPuddles";
        var data = {
            "game_id":      GAME_ID,
            "player_id":    PLAYER_ID
        };
        $.ajax({
            type:   "POST",
            url:    "api.php",
            data:   {
                "ajax_token":   AJAX_TOKEN,
                "process":      process,
                "data":         JSON.stringify(data)
            },
            success: function(data) {
                var response = JSON.parse(data);
                if (response.status == "Success") {
                    var puddles = response.puddles;
                    for (var i = 0; i < puddles.length; i++) {
                        game.addPuddle(new Point(puddles[i].x, puddles[i].y), puddles[i].created_by);
                    }
                } else {
                    console.log("Error message: "+response.error_message);
                }
            },
            error: function() {
                console.log("Error!");
            }
        })
    }
    game.renderBackground = function() {
        this.base_layer.activate();
        this.background = new Raster({
            source: "./resources/rooms/background"+ROOM_ID+".jpg",
            position: view.center
        });
    }

    game.grid = {}
    game.grid.state = {};
    game.grid.cell_width = 1024 / BACKGROUND_GRID_WIDTH;
    game.grid.cell_height = BACKGROUND_HEIGHT / BACKGROUND_GRID_HEIGHT;
    game.grid.smaller_dimension = game.grid.cell_width < game.grid.cell_height ? game.grid.cell_width : game.grid.cell_height;
    game.grid.generate = function() {
        game.fog = {};
        for (var i = 0; i < BACKGROUND_GRID_WIDTH; i++) {
            this.state[i] = {};
            game.fog[i] = {};
            for (var j = 0; j < BACKGROUND_GRID_HEIGHT; j++) {
                game.fog_layer.activate();
                var fog = new Path.Rectangle((i * this.cell_width - 2), (j * this.cell_height) - 2, this.cell_width + 4, this.cell_height + 4);
                fog.fillColor = "black";
                this.state[i][j] = {"fog": true, "contains_marker": false}
                game.fog[i][j] = fog;
            }
        }
        view.draw();
        game.owner.updateGrid();
    }
    game.grid.render = function() {
        for (var i = 0; i < BACKGROUND_GRID_WIDTH; i++) {
            for (var j = 0; j < BACKGROUND_GRID_HEIGHT; j++) {
                game.fog[i][j].visible = this.state[i][j].fog;
            }
        }
    }
    game.grid.determineCoordinates = function(point_location) {
        var grid_x = Math.floor(point_location.x / this.cell_width);
        var grid_y = Math.floor(point_location.y / this.cell_height);
        return [grid_x, grid_y];
    }

    game.markers.size = game.smaller_dimension/2 - 5;
    game.markers.render = function() {
        game.marker_layer.activate();
        for (var i = 0; i < this.paths.length; i++) {
            this.paths[i].remove();
        }
        this.paths = [];
        for (var i = 0; i < this.list.length; i++) {
            var location = new Point(this.list[i].x, this.list[i].y);
            var grid_coords = game.grid.determineCoordinates(location);
            game.grid.state[grid_coords[0]][grid_coords[1]].contains_marker = true;
            var path = new Path.Circle({
                center: location,
                radius: this.size
            });
            path.fillColor = this.list[i].colour;
            this.paths.push(path);
        }
    }
    game.markers.determineCoordinates = function(point_location) {
        var grid_coords = game.grid.determineCoordinates(point_location);
        var x = grid_coords[0] * game.grid.cell_width;
        var y = grid_coords[1] * game.grid.cell_height;
        var marker_x = x + game.grid.cell_width/2 + 2;
        var marker_y = y + game.grid.cell_height/2 + 2;
        return [Math.floor(marker_x), Math.floor(marker_y)];
    }

    game.owner = {};
    game.owner.checkMarker = function(point_location) {
        if (this.verify()) {
            var grid_coords = game.grid.determineCoordinates(point_location);
            var x = grid_coords[0];
            var y = grid_coords[1];
            if (game.grid.state[x][y].contains_marker) {
                this.removeMarker(point_location);
            } else {
                this.addMarker(point_location);
            }
        }
    }
    game.owner.addMarker = function(point_location) {
        if (this.verify()) {
            var marker_coords = game.markers.determineCoordinates(point_location);
            var marker = {"x": marker_coords[0], "y": marker_coords[1], "colour": game.markers.colour};
            var process = "addMarker";
            var data = {
                "game_id":  GAME_ID,
                "marker":   marker
            };
            $.ajax({
                type:   "POST",
                url:    "api.php",
                data:   {
                    "ajax_token":   AJAX_TOKEN,
                    "process":      process,
                    "data":         JSON.stringify(data)
                },
                success: function(data) {
                    var response = JSON.parse(data);
                    if (response.status != "Success") {
                        console.log(response.error_message);
                    }
                },
                error: function() {
                    console.log("Error!");
                }
            });
        }
    }
    game.owner.removeMarker = function(point_location) {
        if (this.verify()) {
            var marker_coords = game.markers.determineCoordinates(point_location);
            var process = "removeMarker";
            var data = {
                "game_id": GAME_ID,
                "marker_x": marker_coords[0],
                "marker_y": marker_coords[1]
            };
            $.ajax({
                type:   "POST",
                url:    "api.php",
                data:   {
                    "ajax_token":   AJAX_TOKEN,
                    "process":      process,
                    "data":         JSON.stringify(data)
                },
                success: function(data) {
                    var response = JSON.parse(data);
                    if (response.status != "Success") {
                        console.log(response.error_message);
                    }
                },
                error: function() {
                    console.log("Error!");
                }
            });
            var grid_coords = game.grid.determineCoordinates(point_location);
            var x = grid_coords[0];
            var y = grid_coords[1];
            game.grid.state[x][y].contains_marker = false;
            this.updateGrid();
        }
    }
    game.owner.verify = function() {
        return PLAYER_ID == GAME_OWNER;
    }
    game.owner.reInitialiseFogPath = function(point_location) {
        // Verify location's within canvas
        var [x, y] = game.grid.determineCoordinates(point_location);
        this.fog_path = {"x": [x], "y": [y]};
        this.fog_mode_remove = game.grid.state[x][y].fog;
    }
    game.owner.addToFogPath = function(point_location) {
        var [x, y] = game.grid.determineCoordinates(point_location);
        // Determine if fog_path contains x
        for (var i = 0; i < this.fog_path.x.length; i++) {
            if (this.fog_path.x[i] == x && this.fog_path.y[i] == y) {
                return;
            }
        }
        this.fog_path.x.push(x);
        this.fog_path.y.push(y);
    }
    game.owner.updateFog = function() {
        var x_coord, y_coord;
        for (var i = 0; i < this.fog_path.x.length; i++) {
            x_coord = this.fog_path.x[i];
            y_coord = this.fog_path.y[i];
            game.grid.state[x_coord][y_coord].fog = !this.fog_mode_remove;
        }
        this.updateGrid();
        this.fog_path = {"x": [], "y": []};
    }
    game.owner.updateGrid = function() {
        if (this.verify()) {
            var process = "updateGrid";
            var data = {
                "game_id":      GAME_ID,
                "grid_state":   game.grid.state
            };
            $.ajax({
                type:   "POST",
                url:    "api.php",
                data:   {
                    "ajax_token":   AJAX_TOKEN,
                    "process":      process,
                    "data":         JSON.stringify(data)
                },
                success: function(data) {
                    var response = JSON.parse(data);
                    if (response.status != "Success") {
                        console.log(response.error_message);
                    }
                },
                error: function() {
                    console.log("Error!");
                }
            });
        }
    }
    game.owner.modifyHealth = function(add_health) {
        var health_amount = $("#health_tool_amount").val();
        var player_id = $("#health_tool_player").val();
        var process = "modifyHealth";
        var data = {
            "game_id":      GAME_ID,
            "health_amount":health_amount,
            "player_id":    player_id,
            "damaging":     !add_health
        }
        $.ajax({
            type:   "POST",
            url:    "api.php",
            data: {
                "ajax_token":   AJAX_TOKEN,
                "process":      process,
                "data":         JSON.stringify(data)
            },
            success: function(data) {
                var response = JSON.parse(data);
                if (response.status != "Success") {
                    console.log(response.error_message);
                }
            },
            error: function() {
                console.log("Error!");
            }
        });
    }
    game.owner.setTempHealth = function() {
        var health_amount = $("#health_tool_amount").val();
        var player_id = $("#health_tool_player").val();
        var process = "setTempHealth";
        var data = {
            "game_id":      GAME_ID,
            "health_amount":health_amount,
            "player_id":    player_id
        }
        $.ajax({
            type:   "POST",
            url:    "api.php",
            data:   {
                "ajax_token":   AJAX_TOKEN,
                "process":      process,
                "data":         JSON.stringify(data)
            },
            success: function(data) {
                var response = JSON.parse(data);
                if (response.status != "Success") {
                    console.log(response.error_message);
                }
            },
            error: function() {
                console.log("Error!");
            }
        });
    }
    game.owner.modifySpellSlots = function(add_slots) {
        var slot_level = $("#spell_slot_level").val();
        var player_id = $("#spell_slot_player").val();
        var process = "modifySpellSlots";
        var data = {
            "game_id":      GAME_ID,
            "slot_level":   slot_level,
            "player_id":    player_id,
            "addition":     add_slots
        };
        $.ajax({
            type:   "POST",
            url:    "api.php",
            data:   {
                "ajax_token":   AJAX_TOKEN,
                "process":      process,
                "data":         JSON.stringify(data)
            },
            success: function(data) {
                var response = JSON.parse(data);
                if (response.status != "Success") {
                    console.log(response.error_message);
                }
            },
            error: function() {
                console.log("Error!");
            }
        })
    }
    game.render = function() {
        this.grid.render();
        this.markers.render();
    }
    game.run = function() {
        if (game.background.width !== 1024) {
            game.background.width = 1024;
            game.background.height = BACKGROUND_HEIGHT;
        }
        game.fetchPuddles();
        game.fetchBoard();
        game.fetchPlayerInfo();
    }
    game.fetchBoard = function() {
        var process = "fetchBoard";
        var data = {
            "game_id":  GAME_ID
        };
        $.ajax({
            type:   "POST",
            url:    "api.php",
            data:   {
                "ajax_token":   AJAX_TOKEN,
                "process":      process,
                "data":         JSON.stringify(data)
            },
            success: function(data) {
                // Update the fog and markers state
                var response = JSON.parse(data);
                game.grid.state = response.board.grid;
                game.markers.list = response.board.markers;
                game.render();
            },
            error: function() {
                console.log("Error!");
            }
        });
    }
    game.fetchPlayerInfo = function() {
        var process = "fetchPlayerInfo";
        var data = {
            "game_id": GAME_ID
        };
        $.ajax({
            type:   "POST",
            url:    "api.php",
            data:   {
                "ajax_token":   AJAX_TOKEN,
                "process":      process,
                "data":         JSON.stringify(data)
            },
            success: function(data) {
                var response = JSON.parse(data);
                if (response.status == "Success") {
                    game.player_cards.updateHealthBars(response["health_summary"]);
                    game.player_cards.updateSpellSlots(response["spell_slot_summary"]);
                } else {
                    console.log(response.error_message);
                }
            },
            error: function() {
                console.log("Error!");
            }
        })
    }
    game.player_cards = {};
    game.player_cards.card_heights = {};
    game.player_cards.updateHealthBars = function(health_summary) {
        var player_health, health_bar, temp_health_bar, health_percentile, temp_health_percentile;
        for (var id in health_summary) {
            player_health = health_summary[id];
            health_bar = document.getElementById("hb_"+id);
            temp_health_bar = document.getElementById("thb_"+id);
            health_percentile = Math.floor(player_health["Current"] / player_health["Max"] * 100);
            temp_health_percentile = Math.floor(player_health["Temp"] / player_health["Max"] * 100);

            health_bar.style.width = "calc(" + health_percentile + "% - 2px)";
            if (health_percentile < 100) {
                health_bar.style.borderTopRightRadius = "0px";
                health_bar.style.borderBottomRightRadius = "0px";
            } else {
                health_bar.style.borderTopRightRadius = "5px";
                health_bar.style.borderBottomRightRadius = "5px";
            }
            temp_health_bar.style.width = "calc(" + temp_health_percentile + "% - 2px)";
            document.getElementById("current_health_text_"+id).innerHTML = player_health["Current"];
        }
    }
    game.player_cards.updateSpellSlots = function(spell_slot_summary) {
        this.emptySpellSlots();
        for (var id in spell_slot_summary) {
            spell_slots = spell_slot_summary[id];
            for (var i = 1; i <= 9; i++) {
                var level = "Level_"+i;
                for (var j = 0; j < spell_slots[level]; j++) {
                    var slot = $("#spell_slot_"+id+"_"+i+"_"+j+" i");
                    slot.removeClass("far");
                    slot.addClass("fas");
                }
            }
        }
    }
    game.player_cards.emptySpellSlots = function() {
        $(".spell_slot i").removeClass("fas");
        $(".spell_slot i").removeClass("far");
        $(".spell_slot i").addClass("far");
    } 
    game.player_cards.reveal = function(player_id) {
        var id = "class_and_level_"+player_id;
        var target_height = "2rem";
        this.toggleElement(id, target_height);
        var id = "player_details_"+player_id;
        var target_height = (2 + 6 + 2 + this.card_heights[player_id].weapons * 3 + this.card_heights[player_id].slots * 1.5 + this.card_heights[player_id].list * 1.5)+"rem";
        this.toggleElement(id, target_height);
    }
    game.player_cards.toggleAbilities = function(player_id) {
        var id = "ability_description_"+player_id;
        var target_height = "6rem";
        this.toggleElement(id, target_height);
    }
    game.player_cards.toggleKeyStats = function(player_id) {
        var id = "key_stats_"+player_id;
        var target_height = "2rem";
        this.toggleElement(id, target_height);
    }
    game.player_cards.toggleWeapons = function(player_id) {
        var id = "weapons_"+player_id;
        var target_height = (this.card_heights[player_id].weapons * 3) + "rem";
        this.toggleElement(id, target_height);
    }
    game.player_cards.toggleWeaponDetails = function(player_id, weapon_id) {
        var id = "weapon_details_"+player_id+"_"+weapon_id;
        var target_height = "3rem";
        this.toggleElement(id, target_height);
    }
    game.player_cards.toggleSpells = function(player_id) {
        var id = "spells_"+player_id;
        var target_height = (3 + this.card_heights[player_id].slots * 1.5 + this.card_heights[player_id].list * 1.5) + "rem";
        this.toggleElement(id, target_height);
    }
    game.player_cards.toggleSpellSlots = function(player_id) {
        var id = "spell_slots_"+player_id;
        var target_height = (this.card_heights[player_id].slots * 1.5)+"rem";
        this.toggleElement(id, target_height);
    }
    game.player_cards.toggleSpellList = function(player_id) {
        var id = "spell_list_"+player_id;
        var target_height = (this.card_heights[player_id].list * 1.5)+"rem";
        this.toggleElement(id, target_height);
    }
    game.player_cards.toggleElement = function(id, target_height) {
        var elem = document.getElementById(id);
        var final_height = elem.style.maxHeight == "0px" ? target_height : "0px";
        elem.style.maxHeight = final_height;
    }
}

window.onload = function() {
    // Initialise game
    game.initialise();
    // Actions for each frame
    game._intervalId = setInterval(game.run, 500);
    paper.view.onFrame = function(event) {
        game.handlePuddles();
    }
}
window.onbeforeunload = function() {
    game.removeConnection();
}