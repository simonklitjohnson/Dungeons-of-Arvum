<?php

echo "Loaded: config.php";

$ip = "0.0.0.0"; //0.0.0.0 exposes the server at your public-facing IP address. Change this only if you know what you're doing.
$port = 9300;
$max_players = 4; //Roadmap: Additional players will be added as spectators.

$map_width = 600;
$map_height = 600;

$generate_new_map = true; //Set to false if you want to load a map from pre-existing map.doafile and rooms.doafile files in the "libs" folder.

$constant_tick = true; //Game ticks in DoA traditionally used to only happen whenever a player would hit a key. If no one did anything, the game would effectively be paused. Setting this to false retains that functionality. Setting it to true spawns a fake player that is performing an action 3 times a second, effectively giving you 3 game ticks per second.

$single_player_mode = false; //NOT DONE. Singleplayer mode changes the objective of the game. Now you have to kill all the legendaries (bosses), and then defeat the final boss, Kali the King of Thieves, and retrieve the Amulet of Yendor from him.  

/*
	In-game specific configuratons
*/

$default_timeout_duration = 5; //The amount of seconds a timeout/suspension lasts for.
$default_auto_timeout = 5; //The default HP at which a player will automatically use a timeout. Can be changed by the individual player. Set to 0 to disable as default, or -1 to disable the auto timeout functionality completely, so that no player can enable it for themselves.

/*
	Performance configurations
*/

$mob_safe_room_check = true; //Before a mob with range attacks, it checks whether the player is in a saferoom. If you have a large map, disabling this functionality might improve performance, although this means that mobs can attack players in saferooms.


/*
	MDoA configurations
*/
$massive = false; // EXPERIMENTAL: Enable this to allow interacting with an MDoA-API compatible database server.
$encryption_key = "1234"; // EXPERIMENTAL: Ensures no tampering with data transferred from MDoA-server to client to DoA-server. This should NOT be public.
$mdoa_api_base = "http://127.0.0.1:8000/api/"; // EXPERIMENTAL: Base URL for server hosting MDoA-API.

/*

	Expert configurations

*/

$display_width = 41; //Default: 41 – Only touch this if you're using a custom client.
$display_height = 21; //Default: 21 – Only touch this if you're using a custom client.
$allow_cheats = false; //Used for debugging. Very easy to crash server with this.
$allow_map_cheats = false; //Used for debugging. Map cheats include changing tiles. Very easy to crash server with this.
$enable_player_movement_speed = false; // EXPERIMENTAL: Player movement speed is experimental and does not function all too well because of the tiles of the game. This will be enabled per default as soon as I get it fixed to function properly.
$profiling = false;
$reporting = false; //Enable error reporting.