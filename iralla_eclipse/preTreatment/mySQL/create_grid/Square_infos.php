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
			$previous_out_coords, 
			$previous_link, 
			$next_link
			){
		$this->bus_line_id = $bus_line['id'];
		$this->bus_line_name = $bus_line['name'];
		$this->lat = $previous_out_coords['next_square']->lat;
		$this->lng = $previous_out_coords['next_square']->lng;
		$this->previous_link_lat = $previous_link['lat'];
		$this->previous_link_lng = $previous_link['lng'];
		$this->next_link_lat = $next_link['lat'];
		$this->next_link_lng = $next_link['lng'];
		$this->go_in_point_lat = $previous_out_coords['intersection']->lat;
		$this->go_in_point_lng = $previous_out_coords['intersection']->lng;
		$this->go_out_point_lat = $current_out_coords['intersection']->lat;
		$this->go_out_point_lng = $current_out_coords['intersection']->lng;
		$this->previous_index_of_go_in = $previous_out_coords['prev_index_before_intersection'];
		$this->previous_index_of_go_out = $current_out_coords['prev_index_before_intersection'];
		$this->previous_vertex_of_link;
		$this->length;
		$this->id_of_previous_bs_linked;
		$this->id_of_next_bs_linked;
		$this->$previous_link_id;
		$this->next_link_id;
	}
	
}

