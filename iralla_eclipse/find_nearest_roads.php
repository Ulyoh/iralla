<?php
require_once 'access_to_db.php';
require_once 'tools.php';
require_once 'tools_to_look_for_roads.php';

$multipicador = 10000000;
$denominator_to_get_real_values = 10000000;
$grid_path = 0.001;
$grid_path_mult = bcmul($multipicador, $grid_path)/10;  //TODO why /10???? to check with create grid
$path_of_squares = "c:/squares2/";
$foot_speed = 0.7;
$bus_speed = 7;

$request = $_POST['q'];
//$request = '{"lat":-2.1561053360208935,"lng":-79.91647949218748}';
$request = json_decode($request);

$position['lat'] = $request->lat;
$position['lng'] = $request->lng;

//find nearst bus stations :
$interval = 0.005;

//$position_nearest_bus_stations = nearest_bus_stations($position, $interval, "bus_stations");
//end find nearest bus stations

//change position to fit with from square coordinates
$position['lat'] =abs( bcmul($position['lat'], $grid_path_mult));
$position['lng'] =abs( bcmul($position['lng'], $grid_path_mult));

//from square
$interval = 5;
$ecart_min_between_d_min_and_d_max = 6;

$bus_lines_ids = nearest_squares_2($position, $interval, $ecart_min_between_d_min_and_d_max);

//extract the bus lines path:
$values = array();
foreach ($bus_lines_ids as $bus_line_id){
	if(isset($test)){
		$test .= 'OR id = ? ';
	}
	else{
		$test = 'id = ? ';
	}
	$values[] = $bus_line_id;
}

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
require_once 'close_bdd.php';

echo json_encode($bus_lines);


