<?php
require_once 'access_to_db.php';
require_once 'tools.php';
require_once 'functions_for_finding_route_3.php';

ini_set('memory_limit', "2000M");
$multipicador = 10000000;
$denominator_to_get_real_values = 10000000;
$grid_path = 0.001;
$grid_path_mult = bcmul($multipicador, $grid_path)/10;  //TODO why /10???? to check with create grid
$foot_speed = 0.7;
$bus_speed = 7;

$request = $_POST['q'];
/*$request = 	'
{"start":{
	"lat":-2.1601365170652644,"lng":-79.8958801269531},"end":{
		"lat":-2.118709192736059,"lng":-79.90995635986326}
}';
*/
//$request = 	'{"start":{"lat":-2.1199100004450315,"lng":-79.91459121704099},"end":{"lat":-2.1525029949886085,"lng":-79.90154495239256}}';

$request = '
{
"start":{
"lat":-2.192814417860611,"lng":-79.8878120422363},"end":{
"lat":-2.1142490416697988,"lng":-79.91373291015623}
}
';
$request = json_decode($request);


$start['lat'] = $request->start->lat;
$start['lng'] = $request->start->lng;
$end['lat'] = $request->end->lat;
$end['lng'] = $request->end->lng;

$result = find_route($start, $end);

echo json_encode($result);

function find_route($start, $end){
	
	if ( (is_numeric_lat_lng($start) == false) ||  (is_numeric_lat_lng($end) == false)){
		exit("arguments not valids");
	}

	//look for nearest bus lines of the start and end point:
	$interval = 2;
	$ecart_min_between_d_min_and_d_max = 100;
	
	$start_lines = find_nearst_roads($start, 'from', $interval, $ecart_min_between_d_min_and_d_max);
	$end_lines = find_nearst_roads($end, 'to', $interval, $ecart_min_between_d_min_and_d_max);
	
	//look if commun bus lines in $start_lines and $end_lines
	$communs_lines = find_communs_lines($start_lines, $end_lines);
	
	if(is_array($communs_lines)){
		$communs_lines = attach_bus_lines_path($communs_lines);
		
		$results = array();
		$results_part = array();
		foreach($communs_lines as $bus_line_id => $communs_line){
			//calcutate :
			$results_part = calculate_shortest_time_from_starts_to_ends_on_one_line($communs_line, $start, $end);
			$results = array_merge($results, $results_part);
		}
		
		//sort by time total:
		usort($results, "cmp_sort_by_total_time");
		$results_sort_by_total_time = $results;
		
		//sort by time by foot
		//usort($results, "cmp_sort_by_time_by_foot");
		$results_sort_by_time_by_foot = $results;
		
		return $results[0];
	}
	
	//if there is not a commun route:
	//find if one start road and one end road have a comun bus station:
	
	find_roads_with_commun_bus_station($start_lines, $end_lines);
	
	//test if commun links roads 
	
	
}