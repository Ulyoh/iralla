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
	foreach($start_lines as $start_bus_line_id => $start_line){
		foreach($end_lines as $end_bus_line_id => $end_line){
			if($start_bus_line_id == $end_bus_line_id){
				if(isset($result[$start_bus_line_id]['start']) == false){
					$result[$start_bus_line_id]['start'] = array();
				}
				if(isset($result[$start_bus_line_id]['end']) == false){
					$result[$start_bus_line_id]['end'] = array();
				}
				$result[$start_bus_line_id]['start'][] = $start_line;
				$result[$start_bus_line_id]['end'][] = $end_line;
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

