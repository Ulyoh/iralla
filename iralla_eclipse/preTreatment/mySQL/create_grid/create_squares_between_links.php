<?php
include_once 'tools.php';
include_once 'create_squares.php';

function create_squares_between_links($previous_link, $next_link, $bus_line, $current_area){
	global $to_squares;
	global $from_squares;
	
	$path = $bus_line['path'];
	
	//find first square
	$first_square = found_square_coords_of_vertex($current_area->enter);
	
	//find last square
	$last_square = found_square_coords_of_vertex($current_area->out);
	
	//is there squares to be created between the two links?
	$first_index_out = first_index_after_square($current_area->enter, $first_square, $path);
	
	//is there squares between the first and last one:
	if(($first_square === $last_square)
			&&($current_area->out < $first_index_out )){
		return;
	}	
	
	$vertex_out = $path[$first_index_out];
	$last_vertex_in = $path[$first_index_out - 1];
		
	//init:
	$current_out_coords = found_out_point($last_vertex_in, $vertex_out, $first_square);
	
	do{	
		create_squares($bus_line,
				$current_out_coords,
				$previous_link,
				$next_link);
		
		$index_out = first_index_after_square($index_out, $current_out_coords['next_square'], $path);
		$vertex_out = $path[$index_out];
		$last_vertex_in = $path[$index_out - 1];		
		
		$current_out_coords = found_out_point($last_vertex_in, $vertex_out, $current_square);
		$current_out_coords['prev_index_before_intersection'] = $last_vertex_in;

	}while($vertex_out > $current_area->out);
}

