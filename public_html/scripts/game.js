with (paper) {
    var game = {
        animate: true
    };
    // Define basic properties
    game.canvas = document.getElementById("game_canvas");
    game.canvas_size = new Size(1024, BACKGROUND_HEIGHT);
    game.puddles = [];
    // Initialise mouse tools
    game.initialise = function() {
        setup(this.canvas);
        this.base_layer = new Layer();
        this.fog_layer  = new Layer();
        this.fog_layer.opacity = 0.95;
        this.mouse_puddle_layer = new Layer();
        this.mouse_circling_layer = new Layer();
        this.mouse_circling_layer.opacity = 0.25;
        this.mouse_track_layer = new Layer();
        this.mouse_track_layer.opacity = 0.5;

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
        this.point_tool.activate();

        this.addConnection(PLAYER_ID);
        this.renderBackground();
        this.generateGrid();
    }

    game.use_point_tool = function() {
        this.point_tool.activate();
    }
    game.use_fog_tool = function() {
        this.fog_tool.activate();
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
    game.fetchGrid = function() {
        var process = "fetchGrid";
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
                var response = JSON.parse(data);
                if (response.status == "Success") {
                    game.grid.state = response.grid;
                    game.renderGrid();
                } else {
                    console.log(response.error_message);
                }
            },
            error: function() {
                console.log("Error!");
            }
        })
    }
    game.generateGrid = function() {
        this.grid = {};
        this.grid.state = {};
        this.fog = {};
        this.grid.cell_width = 1024 / BACKGROUND_GRID_WIDTH;
        this.grid.cell_height = BACKGROUND_HEIGHT / BACKGROUND_GRID_HEIGHT;
        for (var i = 0; i < BACKGROUND_GRID_WIDTH; i++) {
            this.grid.state[i] = {};
            this.fog[i] = {};
            for (var j = 0; j < BACKGROUND_GRID_HEIGHT; j++) {
                this.fog_layer.activate();
                var fog = new Path.Rectangle((i * this.grid.cell_width - 2), (j * this.grid.cell_height) - 2, this.grid.cell_width + 4, this.grid.cell_height + 4);
                fog.fillColor = "black";
                this.grid.state[i][j] = {"fog": true}
                this.fog[i][j] = fog;
            }
        }
        view.draw();
        this.owner.updateGrid();
    }
    game.renderGrid = function() {
        for (var i = 0; i < BACKGROUND_GRID_WIDTH; i++) {
            for (var j = 0; j < BACKGROUND_GRID_HEIGHT; j++) {
                this.fog[i][j].visible = this.grid.state[i][j].fog;
            }
        }
    }
    game.run = function() {
        if (game.background.width !== 1024) {
            game.background.width = 1024;
            game.background.height = BACKGROUND_HEIGHT;
        }
        game.fetchPuddles();
        game.fetchGrid();
    }

    game.owner = {};
    game.owner.verify = function() {
        return PLAYER_ID == GAME_OWNER;
    }
    game.owner.toggleFog = function(point_location) {
        var grid_x = Math.floor(point_location.x / game.grid.cell_width);
        var grid_y = Math.floor(point_location.y / game.grid.cell_height);
        game.grid.state[grid_x][grid_y].fog = !game.grid.state[grid_x][grid_y].fog;
        this.updateGrid();
    }
    game.owner.updateGrid = function() {
        if (this.verify) {
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