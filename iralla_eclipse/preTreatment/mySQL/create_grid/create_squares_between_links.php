<?php
include_once 'tools.php';
include_once 'create_squares.php';

function create_squares_between_links($previous_link, $next_link, $bus_line){
	global $to_squares;
	global $from_squares;
	
	$path = $bus_line['path'];
	$authorized_areas = $bus_line['authorized_areas'];
	//is there squares to be created between the two links?
	$index_out = first_index_after_square($previous_link['prevIndex'], $previous_link['square'], $path);
	
	if(($previous_link['square'] === $next_link['square'])
			&&($next_link['prevIndex'] < $index_out )){
		return;
	}
	
	$vertex_out = $path[$index_out];
	$last_vertex_in = $path[$index_out - 1];
		
	$previous_out_coords = found_out_point($last_vertex_in, $vertex_out, $previous_link['square']);

	
	if(($previous_out_coords['next_square'] == $next_link['square'])
	&&(all_vertex_to_link_inside_square( $index_out, $next_link, $previous_out_coords['next_square'])===true )){
		return;
	}

	//init:
	$current_square = $previous_out_coords['next_square'];
	$index_out = first_index_after_square($index_out, $current_square, $path);
	$vertex_out = $path[$index_out];
	$last_vertex_in = $path[$index_out - 1];
	$previous_out_coords['prev_index_before_intersection'] = $last_vertex_in;
	
	do{
		$current_out_coords = found_out_point($last_vertex_in, $vertex_out, $current_square);

		$next_square = $previous_out_coords['next_square'];
		$index_out = first_index_after_square($index_out, $next_square, $path);
		$vertex_out = $path[$index_out];
		$last_vertex_in = $path[$index_out - 1];
		
		
		
		$bus_line_part = new Square_Infos(
				$bus_line, 
				$current_out_coords, 
				$previous_out_coords, 
				$previous_link, 
				$next_link);
		
		create_squares($bus_line_part);
		
		$next_square = $previous_out_coords['next_square'];
		$index_out = first_index_after_square($index_out, $next_square, $path);
		$vertex_out = $path[$index_out];
		$last_vertex_in = $path[$index_out - 1];		
		
		$previous_out_coords = $current_out_coords;
		$previous_out_coords['prev_index_before_intersection'] = $last_vertex_in;
		
	}while(reach_link($next_link, $path, $next_square) === false);
}

