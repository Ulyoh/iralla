<?php

function distances_to_links($busline, $previous_link, $next_link, $current_area, $current_out_coords){
	/*
	 * used parameters:
	$previous_link['distanceFromFirstVertex']
	
	$current_out_coords['intersection']
	$current_out_coords['prev_index_before_intersection']
	
	$next_link['distanceFromFirstVertex']
	*/
	
	global $multipicador;
	
	$distance_from_first_vertex = real_distance_from_first_vertex($busline['real_path'], $current_out_coords['prev_index_before_intersection'])
		+ real_distance_between_2_vertex($busline['path'][$current_out_coords['prev_index_before_intersection']], $current_out_coords['intersection'], $multipicador);
	
	$distances = array();
	$distances['from_first_vertex'] = $distance_from_first_vertex;
	$distances['from_previous_link'] = $distance_from_first_vertex - $previous_link['distance_from_first_vertex'];
	$distances['to_next_link'] = $next_link['distance_from_first_vertex'] - $distance_from_first_vertex;
	
	return $distances;
	
}


