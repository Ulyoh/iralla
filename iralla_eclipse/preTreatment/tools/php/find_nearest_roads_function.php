<?php
require_once 'tools.php';
require_once 'tools_to_look_for_roads.php';
require_once 'access_to_db.php';
$multipicador = 10000000;
$denominator_to_get_real_values = 10000000;
$grid_path = 0.001;
$grid_path_mult = bcmul($multipicador, $grid_path)/10;  //TODO why /10???? to check with create grid

function find_nearest_roads($position){
	global $bdd;
	global $grid_path_mult;
	//find nearst bus stations :
	$interval = 0.005;
	$path_of_squares = "c:/squares2/";
	
	$position_nearest_bus_stations = nearest_bus_stations($position, $interval, "bus_stations");
	//end find nearest bus stations

	//change position to fit with from square coordinates
	$position['lat'] =abs( bcmul($position['lat'], $grid_path_mult));
	$position['lng'] =abs( bcmul($position['lng'], $grid_path_mult));

	//from square and to square
	$interval = 5;
	$ecart_min_between_d_min_and_d_max = 6;
	$max_group_size = 15;

	$position_squares = nearest_squares($position, $interval, "from_square", $ecart_min_between_d_min_and_d_max, $max_group_size);

	//create false start square of length = 0
	add_bus_stations_to_position_squares(&$position_squares, $position_nearest_bus_stations);

	//end of creation of false  start and end square


	//extract the bus stations path:
	foreach ($position_squares as $square){
		$values[] = $square[bus_line_id];
		$test .= 'OR id = ? ';
	}

	$test = substr($test,2);

	$req = $bdd->prepare("
			SELECT name, path, id
			FROM bus_lines
			WHERE $test
			");

	$req->execute($values);

	while($bus_line = $req->fetch()){
		unset($bus_line[0]);
		unset($bus_line[1]);
		unset($bus_line[2]);
		$bus_lines[]=$bus_line;
}

return $bus_lines;
}