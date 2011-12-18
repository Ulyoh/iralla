<?php

function is_numeric_lat_lng($lat_lng){
	//test if coordinates of $lat_lng are numeric:
	if(!isset($lat_lng['lat']) || !is_numeric($lat_lng['lat']) || ($lat_lng['lat'] < -3) || ( -1 < $lat_lng['lat'])){
		return false;
	}
	if(!isset($lat_lng['lng']) || !is_numeric($lat_lng['lng']) || ($lat_lng['lng'] < -80) || ( -78 < $lat_lng['lng'])){
		return false;
	}
	return true;
}

function find_nearst_roads($lat_lng, $from_or_to_square, $interval, $ecart_min_between_d_min_and_d_max){
	global $grid_path_mult;
	
	//change position to fit with from or to square coordinates
	$position['lat'] =abs( bcmul($lat_lng['lat'], $grid_path_mult));
	$position['lng'] =abs( bcmul($lat_lng['lng'], $grid_path_mult));

	$position_squares = nearest_squares($position, $interval, $from_or_to_square, $ecart_min_between_d_min_and_d_max);

	//reorganize the result by [bus_line_id][bus_station_id]
	$by_bus_lines_then_by_bus_stations = array();
	foreach ($position_squares as $bus_station_id => $bus_lines_list) {
		foreach ($bus_lines_list as $bus_line_id => $square) {
			if(isset($by_bus_lines_then_by_bus_stations[$bus_line_id]) == false){
				$by_bus_lines_then_by_bus_stations[$bus_line_id] = array();
			}
			$by_bus_lines_then_by_bus_stations[$bus_line_id][$bus_station_id] = $square;
		}
	}
	
	return $by_bus_lines_then_by_bus_stations;
}

//return the communs lines id or false
function find_communs_lines($start_lines, $end_lines){
	$result = array();
	foreach($start_lines as $start_bus_line_id => $start_square){
		foreach($end_lines as $end_bus_line_id => $end_square){
			if($start_bus_line_id == $end_bus_line_id){
				if(isset($result[$start_bus_line_id]['start']) == false){
					$result[$start_bus_line_id]['start'] = array();
				}
				if(isset($result[$start_bus_line_id]['end']) == false){
					$result[$start_bus_line_id]['end'] = array();
				}
				$result[$start_bus_line_id]['start'][] = $start_square;
				$result[$start_bus_line_id]['end'][] = $end_square;
			}
		}
	}
	if(count($result) > 0){
		return $result;
	}
	else{
		return false;
	}
}

function extract_part_line($path, $first_vertex_to_extract, $end_vertex_to_extract){
	if ($end_vertex_to_extract < count($path) - 1){
		$path = array_splice($path, $end_vertex_to_extract + 1);
	}
	$path = array_splice($path, 0, $first_vertex_to_extract - 1);
	return $path;
	
}

function attach_bus_lines_path(&$communs_lines){
	
	//extract bus lines path from bdd:
	//prepare request:
	$values_for_mysql = array_keys();
	foreach($communs_lines as $id => $value){
		$values_for_mysql[] = $id;
		if(isset($mySQL_string) == false){
			$mySQL_string = ' id = ?';
		}
		else{
			$mySQL_string .= ' AND id = ?';
		}
	}
	$req = $bdd->prepare('
			SELECT id, path, name, type
			FROM bus_lines
			WHERE '.$mySQL_string
	);
	
	$req->execute($values_for_mysql);
	
	while($bus_line = $req->fetch()){
		$communs_lines[$bus_line['id']]['path'] = extract_path_from_string($bus_line['path']);
	}
}
/*
 * parameter to pass like:
 * $bus_line['start'][]	: from squares list
 * 			['end'][]	: to squares list
 */
function calculate_shortest_time_from_starts_to_ends_on_one_line($bus_line, $start_point, $end_point){
	global $foot_speed;
	global $bus_speed;
	
	//calculate time by foot:
	foreach($bus_line['start'] as $start_squares){
		foreach($start_squares as $start_square){
			$start_square['time_by_foot'] = real_distance_between_2_vertex($start_point, $start_square) / $foot_speed;//calculate_time_by_foot_to_segment($start_point, new Segment($start_squares['segment']));
		}
	}
	foreach($bus_line['end'] as $end_squares){
		foreach($end_squares as $end_square){
			$end_squares['time_by_foot'] = real_distance_between_2_vertex($end_point, $end_square) / $foot_speed; //calculate_time_by_foot_to_segment($end_point, new Segment($end_squares['segment']));
		}
	}
	
	$results = array();
	$result = array();
	foreach($bus_line['start'] as $start_squares){
		foreach($start_squares as $start_square){
			foreach($bus_line['end'] as $end_squares){
				foreach($end_squares as $end_square){
					$result['time_by_foot'] = array();
					$result['time_by_foot'][] = $start_square['time_by_foot'];
					$result['time_by_foot'][] = $end_square['time_by_foot'];
					$result['$time_by_bus'] = time_between_2_vertex_by_bus($bus_line['path'], $start_square['from_index'], $end_square['to_index']);
					$result['$time_total'] = $result['$time_by_bus'] + $result['time_by_foot'][0] + $result['time_by_foot'][1];
					$result['path'] = extract_part_line($bus_line['path'], $start_square['from_index'], $end_square['to_index']);
					$result['busline'] = $bus_line;
					$results[] = $result;
				}
			}
		}
	}

	return $result;
}

function time_between_2_vertex_by_bus($path, $vertex_index_1, $vertex_index_2){
	global $bus_speed;
	return distance_between_2_vertex_on_path($path, $vertex_index_1, $vertex_index_2) / $bus_speed;;
	
}

function find_roads_with_commun_bus_station($start_lines, $end_lines){
	
	//found all the links for each start and end roads
	//do all with sql:
	foreach($start_lines as $id => $value){
		if(isset($mySQL_string) == false){
			$bus_lines_ids_start = ' id = '.$id;
		}
		else{
			$bus_lines_ids_start .= ' AND id = '.$id;
		}
	}
	foreach($end_lines as $id => $value){
		if(isset($mySQL_string) == false){
			$bus_lines_ids_end = ' id = '.$id;
		}
		else{
			$bus_lines_ids_end .= ' AND id = '.$id;
		}
	}
	
	$req = $bdd->query('
		SELECT *
			FROM
			(
				SELECT
					busStationId AS busStationIdStart,
					busLineId AS busLineIdStart,
					prevIndex AS prevIndexStart,
					distanceToPrevIndex AS distanceToPrevIndexStart
				FROM
					links
				WHERE
					'. $bus_lines_ids_start .'
			) AS start,
			(
			SELECT
				busStationId AS busStationIdEnd,
				busLineId AS busLineIdEnd,
				prevIndex AS prevIndexEnd,
				distanceToPrevIndex AS distanceToPrevIndexEnd
			FROM
				links
			WHERE
				'. $bus_lines_ids_end .'
			) AS end,
			WHERE
				busStationIdStart = busStationIdEnd
	');
	
	while( $result = $req->fetch()){
		$start_squares = $start_lines[$result['busLineIdStart']];
		$end_squares = $start_lines[$result['busLineIdEnd']];
		
		$start_square['time_by_foot'] = real_distance_between_2_vertex($start_point, $start_square) / $foot_speed;//calculate_time_by_foot_to_segment($start_point, new Segment($start_squares['segment']));
		$end_squares['time_by_foot'] = real_distance_between_2_vertex($end_point, $end_square) / $foot_speed; //calculate_time_by_foot_to_segment($end_point, new Segment($end_squares['segment']));
		
		
	}
}















function calculate_time_by_foot_to_segment($lat_lng, $segment){
	global $foot_speed;
	$segment;
}
