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
}';*/

//$request = 	'{"start":{"lat":-2.1199100004450315,"lng":-79.91459121704099},"end":{"lat":-2.1525029949886085,"lng":-79.90154495239256}}';
/*
$request = '
{
"start":{
"lat":-2.192814417860611,"lng":-79.8878120422363},"end":{
"lat":-2.1142490416697988,"lng":-79.91373291015623}
}
';*/

//$request = '{"start":{"lat":-2.2121978230220463,"lng":-79.89261856079099},"end":{"lat":-2.1977889441097673,"lng":-79.89227523803709}}';

//two bus lines:
/*$request = '{"start":{
	"lat":-2.172744609908308,"lng":-79.82841720581052},"end":{
		"lat":-2.2128839566288687,"lng":-79.8929618835449}
}';*/

$request = '	{"start":{"lat":-2.089031793406955,"lng":-79.90463485717771},"end":{"lat":-2.2722333014760494,"lng":-79.88300552368162}}';
$request = json_decode($request);


$start['lat'] = $request->start->lat;
$start['lng'] = $request->start->lng;
$end['lat'] = $request->end->lat;
$end['lng'] = $request->end->lng;

$result = find_route($start, $end);

echo json_encode($result);

function find_route($start, $end){
	$create_start_bls_table =  file_get_contents('mySQL/create_start_bls_table.sql');
	$create_end_bls_table =  file_get_contents('mySQL/create_end_bls_table.sql');
	$join_start_end_by_bl_id =  file_get_contents('mySQL/join_start_end_by_bl_id.sql');
	global $grid_path;
	global $grid_path_mult;
	global $bdd;
	
	if ( (is_numeric_lat_lng($start) == false) ||  (is_numeric_lat_lng($end) == false)){
		exit("arguments not valids");
	}
	//change position to fit with from or to square coordinates
	$start_simplify['lat'] =bcmul($start['lat'], $grid_path_mult);
	$start_simplify['lng'] =bcmul($start['lng'], $grid_path_mult);
	$end_simplify['lat'] =bcmul($end['lat'], $grid_path_mult);
	$end_simplify['lng'] =bcmul($end['lng'], $grid_path_mult);	
	
	//create start_bls:
	$req = $bdd->prepare($create_start_bls_table);
	
	$values = array();
	$values[0] = $start_simplify['lat'];
	$values[1] = $start_simplify['lat'];
	$values[2] = $start_simplify['lng'];
	$values[3] = $start_simplify['lng'];

	do {
		$bdd->query('drop table if exists start_bls');
		$values[0] --;
		$values[1] ++;
		$values[2] --;
		$values[3] ++;
		$req->execute($values);
	} while( $req->rowCount() == 0 ) ;
	
	/*$bdd->query('drop table if exists start_bls');
	$values[0] --;
	$values[1] ++;
	$values[2] --;
	$values[3] ++;
	$req->execute($values);*/
	
	//create end_bls table
	$req = $bdd->prepare($create_end_bls_table);
	
	$values[0] = $end_simplify['lat'];
	$values[1] = $end_simplify['lat'];
	$values[2] = $end_simplify['lng'];
	$values[3] = $end_simplify['lng'];
	
	do {
		$bdd->query('drop table if exists end_bls');
		$values[0] --;
		$values[1] ++;
		$values[2] --;
		$values[3] ++;
		$req->execute($values);
	} while( $req->rowCount() == 0 ) ;
	
/*	$bdd->query('drop table if exists end_bls');
		$values[0] --;
		$values[1] ++;
		$values[2] --;
		$values[3] ++;
	$req->execute($values);*/
	
	//found the communs bls:
	$req = $bdd->query($join_start_end_by_bl_id);
	
	/*
	 * 	var bs2Bss = datas.bs2bss;
	var busStations = datas.bus_stations;
	 * 
	 * 
	 */
	$bs2bss = array();
	$bus_stations = array();
	
	if ( $req->rowCount() != 0 ){
		while($route = $req->fetch()){
			echo $route;
		}
	} 
	else{
		//found the communs bs:
		$find_communs_bs =  file_get_contents('mySQL/find_communs_bs.sql');
		//liste of bs on the bl start
		$req = $bdd->query($find_communs_bs);
		
		//liste of the bs on the bl end
		if ( $req->rowCount() != 0 ){
			$routes_length = 0;
			while($one_route = $req->fetch()){
				//if first route or
				//if new couple start / end bls
				//add it to the routes list
				if((!isset($current_start_bl) && (!isset($current_end_bl)))
				||($current_start_bl != $one_route['start_busLineId']) 
				||($current_end_bl != $one_route['end_busLineId'])){
					$current_start_bl = $one_route['start_busLineId'];
					$current_end_bl = $one_route['end_busLineId'];
					$routes[] = $route;
					$routes_length++;
					continue;
				}
				//in order to keep only the last one of following intermediate bs:
				if($one_route['start_inter_links_id'] = $prev_start_inter_links_id){
					
				}
				$prev_start_inter_links_id = $one_route['start_inter_links_id'];
				$routes[$routes_length-1] = $route;
			}
		}
		else{
			//found the communs bl in the bs:
			$find_communs_bls =  file_get_contents('mySQL/find_communs_bls_throw_bs.sql');
			//liste of bl:
			$req = $bdd->query($find_communs_bls);
			
			//liste of the bs on the bl end
			if ( $req->rowCount() != 0 ){
				while($route = $req->fetch()){
					echo $route;
				}
			}
			else{
				echo 'no answer';
				return;
			}
		}	
		
	}
	
	//found the commmuns bs:
	
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
