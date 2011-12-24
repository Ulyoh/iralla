<?php

class Square_infos{
	public 
	$bus_line_id,
	$bus_line_name,
	$lat,
	$lng,
	$previous_link_lat,
	$previous_link_lng,
	$next_link_lat,
	$next_link_lng,
	$go_in_point_lat,
	$go_in_point_lng,
	$go_out_point_lat,
	$go_out_point_lng,
	$previous_index_of_go_out,
	$length,
	$previous_vertex_of_link,
	$previous_link_id,
	$next_link_id,
	$id_of_previous_bs_linked,
	$id_of_next_bs_linked;
	
	function __construct(
			$bus_line, 
			$current_out_coords,
			$previous_link, 
			$next_link
			){
		$this->bl_id = $bus_line['id'];
		$this->bl_name = $bus_line['name'];
		$this->lat = $current_out_coords['next_square']->lat;
		$this->lng = $current_out_coords['next_square']->lng;
		$this->pt_coords = json_encode($current_out_coords['intersection']);
		//prev_index_of_pt or the index of the vertex if they are merged
		$this->prev_index_of_pt = $current_out_coords['prev_index_before_intersection'];
		$this->prev_vertex_of_prev_link = $previous_link['pervIndex'];
		$this->prev_vertex_of_next_link = $next_link['pervIndex'];;
		$this->prev_link_coords = json_encode($previous_link);
		$this->next_link_coords = json_encode($next_link);
		$this->prev_bs_linked_id = $previous_link['busStationId'];
		$this->next_bs_linked_id = $next_link['busStationId'];
		$this->distance_to_prev_link;
		$this->distance_to_next_link;
		$this->previous_link_id = $previous_link['id'];
		$this->next_link_id = $next_link['id'];
	}
	
}

