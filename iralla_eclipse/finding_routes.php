<?php
require_once 'access_to_db.php';
require_once 'tools.php';
require_once 'Busline.php';
require_once 'finding_routes/find_first_and_last_squares_of_nearest_bls.php';
require_once 'functions_for_finding_route_3.php';

ini_set('memory_limit', "2000M");
$multipicador = 10000000;
$denominator_to_get_real_values = 10000000;
$grid_path = 0.001;
$grid_path_mult = bcmul($multipicador, $grid_path)/10;  //TODO why /10???? to check with create grid
$foot_speed = 0.7;
$bus_speed = 7;

//$request = $_POST['q'];
$request = '	{"start":{"lat":-2.089031793406955,"lng":-79.90463485717771},"end":{"lat":-2.2722333014760494,"lng":-79.88300552368162}}';
$request = json_decode($request);

//extract start and end point;
$start = new Point($request->start->lng, $request->start->lat);
$end = new Point($request->end->lng, $request->end->lat);

//find nearest bls of start point
//create $start->first_and_last_squares
$start_squares = find_first_and_last_square_of_nearest_bls($start);

//idem for end point:
$end_squares = find_first_and_last_square_of_nearest_bls($end);



echo json_encode($results);
