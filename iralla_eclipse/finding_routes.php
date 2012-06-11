<?php
require_once 'access_to_db.php';
require_once 'tools.php';
require_once 'Busline.php';
require_once 'finding_routes/find_first_and_last_squares_of_nearest_bls.php';
require_once 'finding_routes/communs_lines_in_start_and_end_squares.php';
require_once 'functions_for_finding_route_3.php';

ini_set('memory_limit', "2000M");
$multipicador = 10000000;
$denominator_to_get_real_values = 10000000;
$grid_path = 0.001;
$grid_path_mult = bcmul($multipicador, $grid_path)/10;  //TODO why /10???? to check with create grid
$foot_speed = 0.7;
$bus_speed = 7;

$request = $_POST['q'];
//bl id 24 to bl id 25
//$request = '	{"start":{"lat":-2.089031793406955,"lng":-79.90463485717771},"end":{"lat":-2.2722333014760494,"lng":-79.88300552368162}}';

//bl id 55 to bl id 55
//$request = '{"start":{"lat":-2.1589679045116914,"lng":-79.91697301864622},"end":{"lat":-2.251757192239456,"lng":-79.90175952911375}}';

$request = '{"start":{"lat":-2.1549045566235523,"lng":-79.8603462219238},"end":{"lat":-2.1610799834251004,"lng":-79.85141983032224}}';

$request = json_decode($request);

//extract start and end point;
$start = new Point($request->start->lng, $request->start->lat);
$end = new Point($request->end->lng, $request->end->lat);

//find nearest bls of start point
//create $start->first_and_last_squares
find_first_and_last_square_of_nearest_bls($start);

//idem for end point:
find_first_and_last_square_of_nearest_bls($end);

//look for communs buslines in $start_squares and $end_squares
$results = communs_lines_in_start_and_end_squares($start, $end);

if ($results != false){
	//prepare datas to be sent:
	$datas = prepare_datas_temporary($results);
	
	echo json_encode($datas);
	exit();
}

//look if two lines from start and end cross each other:
$sq_to_sq_results = $cross_lines_in_start_and_end_squares($start, $end);
if ($results != false){
	echo json_encode($results);
	exit();
}

//look if a third line exists to link two lines from start to end:
$sq_to_sq_results = $third_lines_from_start_bl_to_end_squares_bl($start, $end);
if ($results != false){
	echo json_encode($results);
	exit();
}

function prepare_datas_temporary($results){
	$datas = array();
	$datas['bs2bss'] = $results;
	foreach ($results as $result){
		$busstations[] = null;
	}
	$busstations[] = null;
	$datas['bus_stations'] = $busstations;
	return $datas;
}


