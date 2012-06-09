<?php
require_once 'access_to_db.php';
require_once 'tools.php';
require_once 'functions_for_finding_route_3.php';
require_once 'Busline.php';

ini_set('memory_limit', "2000M");
$multipicador = 10000000;
$denominator_to_get_real_values = 10000000;
$grid_path = 0.001;
$grid_path_mult = bcmul($multipicador, $grid_path)/10;  //TODO why /10???? to check with create grid
$foot_speed = 0.7;
$bus_speed = 7;

//$request = $_POST['q'];
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
		$current_start_bl = 0; //0 does not exists as bl
		$current_end_bl = 0; //0 does not exists as bl
		$prev_start_inter_links_id = 0; //0 does not exists as link
		//liste of the bs on the bl end
		if ( $req->rowCount() != 0 ){
			$routes_length = 0;
			$bl_list = array();
			while($one_route = $req->fetch()){
				//if first route or
				//if new couple start / end bls
				//add it to the routes list
				if((!isset($current_start_bl) && (!isset($current_end_bl)))
				||($current_start_bl != $one_route['start_busLineId']) 
				||($current_end_bl != $one_route['end_busLineId'])){
					$current_start_bl = $one_route['start_busLineId'];
					$current_end_bl = $one_route['end_busLineId'];
					$routes[] = $one_route;
					$routes_length++;
					$bl_list[$one_route['start_busLineId']] = $current_start_bl;
					$bl_list[$one_route['end_busLineId']] = $current_end_bl;
					continue;
				}
				//in order to keep only the last one of following intermediate bs:
				if($one_route['start_inter_links_id'] == $prev_start_inter_links_id){
					$routes[$routes_length-1] = $one_route;
				}
				else{
					//the first route after the loop including continue:
					$routes[] = $one_route;
					$routes_length++;
				}
				$prev_start_inter_links_id = $one_route['start_inter_links_id'];
			}
			
			//extract paths for each bus lines selected in $routes:
			$bls_string = '';
			foreach($bl_list as $bl){
				if ($bls_string == ''){
					$bls_string = $bl;
				}
				else{
					$bls_string .= ' OR ' . $bl;
				}
			}
			$req = $bdd->query('select id, path, flows from bus_lines where id = '. $bls_string);
			
			$bls = array();
			//put in order all the extracted bus lines paths and create the Buslines:
			while($bl = $req->fetch()){
				if(key_exists($bl['id'], $bls)){
					exit("error busline already saved");
				}
				$bls[$bl['id']] = new Busline(extract_path_from_string_to_points($bl['path']), '');
				$bls[$bl['id']]->flows = $bl['flows'];
			}
			
			$start_point = new Point($start['lat'], $start['lng']);
			$end_point = new Point($end['lat'], $end['lng']);
			
			$routes = links_index_of_routes($routes);
			
			foreach ($routes as $route) {
				$bl_start = $bls[$route['start_busLineId']];
				//calculate the nearest point on the busline to the start point
				if( isset($bl_start->nearest_pt_on_bl) === false){
					/**
					 * result of $start_point->projection_on_polyline_between
					 * has changed it s now a Point, must be adapted here:
					 */
					$bl_start->nearest_pt_on_bl = $start_point->projection_on_polyline_between(
							$bl_start,
							//IT S NOT THE ID THAT MUST BE GIVEN BUT THE INDEX OF VERTEX
					//the links are created in the same order as the index, it does not matter to take in consideration
					//the flows
							min((int)$route['start_min_previous_index'], (int)$route['start_min_next_index']), //should have same value????
							max((int)$route['start_max_previous_index'], (int)$route['start_max_next_index']) + 1);
				}
				
				//calculate the nearest point on the busline to the end point
				$bl_end = $bls[$route[end_busLineId]];
				if( isset($bl_end->nearest_pt_on_bl) === false){
					/**
					 * result of $start_point->projection_on_polyline_between
					 * has changed it s now a Point, must be adapted here:
					 */
					$bl_end->nearest_pt_on_bl = $end_point->projection_on_polyline_between(
							$bl_end,
							min($route['end_min_previous_index'],$route['end_min_next_index']), //should have same value????
							max($route['end_max_previous_index'], $route['end_max_next_index']) + 1);
				}
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
	
}
