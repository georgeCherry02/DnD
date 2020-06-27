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
    game.grid = {};
    game.grid.state = {};
    // Initialise mouse tools
    game.initialise = function() {
        setup(this.canvas);
        this.base_layer = new Layer();
        this.marker_layer = new Layer();
        this.marker_layer.opacity = 0.7;
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
            game.owner.toggleFog(event.point);
            view.draw();
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
    game.use_marker_tool = function() {
        this.marker_tool.activate();
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

    game.markers.size = game.grid.cell_width < game.grid.cell_height ? game.grid.cell_width/2 - 5 : game.grid.cell_height / 2 - 5;
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
                var colour = document.getElementById("marker_colour").value;
                this.addMarker(point_location, colour);
            }
        }
    }
    game.owner.addMarker = function(point_location, colour) {
        if (this.verify()) {
            var marker_coords = game.markers.determineCoordinates(point_location);
            var marker = {"x": marker_coords[0], "y": marker_coords[1], "colour": colour};
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
            })
        }
    }
    game.owner.verify = function() {
        return PLAYER_ID == GAME_OWNER;
    }
    game.owner.toggleFog = function(point_location) {
        var grid_coords = game.grid.determineCoordinates(point_location);
        var x = grid_coords[0];
        var y = grid_coords[1];
        game.grid.state[x][y].fog = !game.grid.state[x][y].fog;
        this.updateGrid();
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
        })
    }
}

window.onload = function() {
    // Initialise game
    game.initialise();
    // Actions for each frame
    game._intervalId = setInterval(game.run, 200);
    paper.view.onFrame = function(event) {
        game.handlePuddles();
    }
}
window.onbeforeunload = function() {
    game.removeConnection();
}