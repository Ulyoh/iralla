<?php
/*
require_once 'access_to_db.php';
require_once 'tools.php';
require_once 'tools_to_look_for_roads.php';

$req = $bdd->prepare("
		SELECT *
		FROM bus_stations_to_bus_stations
		WHERE 35
		AND 100
		ORDER BY time
");

$req->execute();

$bs2bss = $req->fetch();
*/
$path_of_roads = "c:/roads/";
$file_to_open = $path_of_roads . "35/100";
$road = file_get_contents($file_to_open) or die("can't open file\n");
$road = json_decode($road);

echo "done";