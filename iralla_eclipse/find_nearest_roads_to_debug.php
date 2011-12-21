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
$request = '{"lat":-2.1561053360208935,"lng":-79.91647949218748}';
$request = json_decode($request);

$position['lat'] = $request->lat;
$position['lng'] = $request->lng;

//find nearst bus stations :
$interval = 0.005;

$position_nearest_bus_stations = nearest_bus_stations($position, $interval, "bus_stations");
//end find nearest bus stations

//change position to fit with from square coordinates
$position['lat'] =abs( bcmul($position['lat'], $grid_path_mult));
$position['lng'] =abs( bcmul($position['lng'], $grid_path_mult));

//from square
$interval = 5;
$ecart_min_between_d_min_and_d_max = 6;
$max_group_size = 15;

$position_squares = nearest_squares($position, $interval, "from_square", $ecart_min_between_d_min_and_d_max, $max_group_size);

//extract the bus lines path:
$values = array();
$all_squares_coord_by_bus_line_id = array();
foreach ($position_squares as $squares_by_bus_line_id){
	foreach($squares_by_bus_line_id as $bus_line_id => $square){
		if(!isset($all_squares_coord_by_bus_line_id[$bus_line_id])){
			$all_squares_coord_by_bus_line_id[$bus_line_id] = array();
		}
		$all_squares_coord_by_bus_line_id[$bus_line_id][] = array(lat=>-$square['lat']*$grid_path, lng=>-$square['lng']*$grid_path);
		
		if(!in_array($bus_line_id, $values)){
			if(isset($test)){
				$test .= 'OR id = ? ';
			}
			else{
				$test = 'id = ? ';
			}
			$values[] = $bus_line_id;
		}
	}
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
	$bus_line['squares_list'] = $all_squares_coord_by_bus_line_id[$bus_line['id']];
	$bus_lines[]=$bus_line;
}
require_once 'close_bdd.php';

echo json_encode($bus_lines);


