<?php
require_once 'tools.php';
require_once 'create_squares_between_links.php';
function treatment($bus_line, $last_id){

	global $multipicador;
	global $grid_path;
	global $precision;
	global $bdd;

	$bus_line_part = new Bus_line_part($bus_line);
	$busline['path'] = extract_path_from_string($bus_line['path_string']);
	$busline['path_length'] = count($busline['path']); 
	$next_index = 0;
	$previous_link = NULL;
	$next_link = NULL;
	$links_list_length = count($bus_line['links_list']);

	$busline['authorized_areas'] = find_areas_to_make_squares($bus_line);
	
	$links = $bus_line['links_list'];
	$links_lenght = count($links);
	//create squares between links:
	for($i = 0; $i < $links_lenght-1; $i++){
		$previous_link = $links[$i];
		$previous_link['square'] = found_square_coords_of_vertex($previous_link);
		$next_link = $links[$i+1];
		$next_link['square'] = found_square_coords_of_vertex($next_link);
		
		create_squares_between_links($previous_link, $next_link, $busline);
		
	}
}