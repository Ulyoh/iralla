<?php


require_once 'find_nearest_roads_function.php';

$request = $_POST['q'];
$request = '{"lat":-2.1561053360208935,"lng":-79.91647949218748}';
$request = json_decode($request);


$position['lat'] = $request->lat;
$position['lng'] = $request->lng;

$bus_lines = find_nearest_roads($position);

require_once 'close_bdd.php';
echo json_encode($bus_lines);


