<?php
include_once 'tools.php';
include_once 'create_squares.php';
include_once 'distances_to_links.php';

function create_squares_between_links($previous_link, $next_link, $bus_line, $current_area){
	
	$path = $bus_line['path'];
	
	//find first square:
	if($previous_link['prevIndex'] >= $current_area->enter){
		//find square of first link
		$first_square = found_square_coords_of_vertex($previous_link);
		
		//is there squares to be created between the two links?
		$first_index_out = first_index_after_square($previous_link['prevIndex'], $first_square, $path, $bus_line['path_length']);
	}
	else{
		//find first square of area
		$first_square = found_square_coords_of_vertex($path[$current_area->enter]);
		
		//is there squares to be created between the two links?
		$first_index_out = first_index_after_square($current_area->enter, $first_square, $path, $bus_line['path_length']);
	}
	
	//if the last index is reach:
	if($first_index_out === null){
		return;
	}
	
	//find last square:
	if($next_link['prevIndex'] < $current_area->out){
		//find square of last link
		$last_square = found_square_coords_of_vertex($next_link);
		$index_to_compare = $next_link['prevIndex'];
	}
	else{
		//find last square of area
		$last_square = found_square_coords_of_vertex($path[$current_area->out]);
		$index_to_compare = $current_area->out;
	}
	
	//if there is not squares between the first and last one:
	if(($first_square === $last_square)
			&&($index_to_compare < $first_index_out )){
		return;
	}
	
	$vertex_out = $path[$first_index_out];
	$last_vertex_in = $path[$first_index_out - 1];
	
	//init:
	$current_out_coords = found_out_point($last_vertex_in, $vertex_out, $first_square);
	$current_out_coords['prev_index_before_intersection'] = $first_index_out - 1;
	$distances = distances_to_links($bus_line, $previous_link, $next_link, $current_area, $current_out_coords);
	$index_out = $first_index_out;
	$current_square = $current_out_coords['next_square'];
	
	do{	
		create_squares($bus_line,
				$current_out_coords,
				$previous_link,
				$next_link,
				$distances);
		
		$index_out = first_index_after_square($index_out-1, $current_square, $path, $bus_line['path_length']);
		
		//if the last index is reach:
		if($index_out === null){
			break;
		}
		$vertex_out = $path[$index_out];
		/*$i = 1;
		
		do{
			$last_vertex_in=$path[$index_out - $i];
			$i++;
		}while (Vertex::are_egal($last_vertex_in, $vertex_out) == true);
		*/
		$last_vertex_in = $path[$index_out - 1];
		
		$current_out_coords = found_out_point($last_vertex_in, $vertex_out, $current_square);
		$current_out_coords['prev_index_before_intersection'] = $index_out - 1;
		$distances = distances_to_links($bus_line, $previous_link, $next_link, $current_area, $current_out_coords);
		$current_square = $current_out_coords['next_square'];
		
	}while(($index_out <= $current_area->out) && ($distances['from_first_vertex'] <= $next_link['distance_from_first_vertex']));
}


