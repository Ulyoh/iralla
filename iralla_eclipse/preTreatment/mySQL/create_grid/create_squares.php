<?php

function create_squares($bus_line,
				$current_out_coords,
				$previous_link,
				$next_link,
				$distances){
	global $links_squares;
	
	$square_to_save['bl_id'] = $bus_line['id'];
	$square_to_save['bl_name'] = $bus_line['name'];
	$square_to_save['lat'] = $current_out_coords['next_square']->lat;
	$square_to_save['lng'] = $current_out_coords['next_square']->lng;
	$square_to_save['pt_coords'] = json_encode($current_out_coords['intersection']);
	//prev_index_of_pt or the index of the vertex if they are merged
	$square_to_save['prev_index_of_pt'] = $current_out_coords['prev_index_before_intersection'];
	$square_to_save['prev_vertex_of_prev_link'] = $previous_link['pervIndex'];
	$square_to_save['prev_vertex_of_next_link'] = $next_link['pervIndex'];;
	$square_to_save['prev_link_coords'] = json_encode($previous_link);
	$square_to_save['next_link_coords'] = json_encode($next_link);
	$square_to_save['prev_bs_linked_id'] = $previous_link['busStationId'];
	$square_to_save['next_bs_linked_id'] = $next_link['busStationId'];
	$square_to_save['distance_to_prev_link'] = $distances['distance_to_prev_link'];
	$square_to_save['distance_to_next_link'] = $distances['distance_to_next_link'];
	$square_to_save['previous_link_id'] = $previous_link['id'];
	$square_to_save['next_link_id'] = $next_link['id'];
	$square_to_save['flows'] = $bus_line['flows'];
	
	$links_squares[] = $square_to_save;
}


