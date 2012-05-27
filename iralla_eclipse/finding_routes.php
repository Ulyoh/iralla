<?php
require_once 'access_to_db.php';
require_once 'tools.php';
require_once 'Busline.php';
require_once 'finding_routes/find_routes.php';


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

//extract start and end point
$start['lat'] = $request->start->lat;
$start['lng'] = $request->start->lng;
$end['lat'] = $request->end->lat;
$end['lng'] = $request->end->lng;

//find the routes
$results = find_routes($start, $end);

echo json_encode($results);
