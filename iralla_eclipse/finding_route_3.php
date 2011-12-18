<?php
require_once 'access_to_db.php';
require_once 'tools.php';
require_once 'functions_for_finding_route_3.php';


$multipicador = 10000000;
$denominator_to_get_real_values = 10000000;
$grid_path = 0.001;
$grid_path_mult = bcmul($multipicador, $grid_path)/10;  //TODO why /10???? to check with create grid
$path_of_squares = "c:/squares2/";
$foot_speed = 0.7;
$bus_speed = 7;


$request = $_POST['q'];
$request = json_decode($request);

$start['lat'] = $request->start->lat;
$start['lng'] = $request->start->lng;
$end['lat'] = $request->end->lat;
$end['lng'] = $request->end->lng;

find_route($request);

function find_route($start, $end){
	
	if ( (is_numeric_lat_lng($start) == false) ||  (is_numeric_lat_lng($end) == false)){
		exit("arguments not valids");
	}

	//look for nearest bus lines of the start and end point:
	$interval = 5;
	$ecart_min_between_d_min_and_d_max = 6;
	
	$start_lines = find_nearst_roads($start, 'from_squares', $interval, $ecart_min_between_d_min_and_d_max);
	$end_lines = find_nearst_roads($end, 'to_squares', $interval, $ecart_min_between_d_min_and_d_max);
	
	//look if commun bus lines in $start_lines and $end_lines
	$communs_lines = find_communs_lines($start_lines, $end_lines);
	
	if(is_array($communs_lines)){
		$bus_lines_path = attach_bus_lines_path(&$communs_lines);
		
		$results = array();
		$results_part = array();
		foreach($communs_lines as $bus_line_id => $communs_line){
			//calcutate :
			$results_part = calculate_shortest_time_from_starts_to_ends_on_one_line($communs_line, $start, $end);
			$results = array_merge($results, $results_part);
		}
		
		//sort by time:
		//TODO
		
		
		return json_encode($results);
	}
	
	//if there is not a commun route:
	//find if one start road and one end road have a comun bus station:
	
	find_roads_with_commun_bus_station($start_lines, $end_lines);
	
	//test if commun links roads 
	
	
}