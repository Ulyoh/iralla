<?php

function create_squares($bus_line,
				$current_out_coords,
				$previous_link,
				$next_link,
				$distances){
	global $links_squares;
	
	$square_to_save = array();
	$square_to_save['bl_id'] = $bus_line['bus_line_id'];
	$square_to_save['bl_name'] = $bus_line['bus_line_name'];
	$square_to_save['lat'] = $current_out_coords['next_square']->lat;
	$square_to_save['lng'] = $current_out_coords['next_square']->lng;
	$square_to_save['pt_coords'] = $current_out_coords['intersection']->get_lat_lng_string();
	//prev_index_of_pt or the index of the vertex if they are merged
	$square_to_save['prev_index_of_pt'] = $current_out_coords['prev_index_before_intersection'];
	$square_to_save['prev_vertex_of_prev_link'] = $previous_link['prevIndex'];
	$square_to_save['prev_vertex_of_next_link'] = $next_link['prevIndex'];;
	$square_to_save['prev_link_coords'] = json_encode(array(
			'lat' => $previous_link['lat'],
			'lng' => $previous_link['lng']));
	$square_to_save['next_link_coords'] = json_encode(array(
			'lat' => $next_link['lat'],
			'lng' => $next_link['lng']));
	$square_to_save['prev_bs_linked_id'] = $previous_link['busStationId'];
	$square_to_save['next_bs_linked_id'] = $next_link['busStationId'];
	$square_to_save['distance_to_prev_link'] = $distances['from_previous_link'];
	$square_to_save['distance_to_next_link'] = $distances['to_next_link'];
	$square_to_save['distance_from_first_vertex'] = $distances['from_first_vertex'];
	$square_to_save['previous_link_id'] = $previous_link['id'];
	$square_to_save['next_link_id'] = $next_link['id'];
	$square_to_save['flows'] = $bus_line['flows'];
	
	$links_squares[] = $square_to_save;
}



