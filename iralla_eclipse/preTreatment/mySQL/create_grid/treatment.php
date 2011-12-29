<?php
require_once 'tools.php';
require_once 'create_squares_between_links.php';
function treatment($bus_line, $last_id){

	global $multipicador;
	global $grid_path;
	global $precision;
	global $bdd;

	$busline['path'] = extract_path_from_string($bus_line['path_string']);
	$busline['path_length'] = count($busline['path']); 
	$next_index = 0;
	$previous_link = NULL;
	$next_link = NULL;
	$links_list_length = count($bus_line['links_list']);

	$busline['authorized_areas'] = find_areas_to_make_squares($bus_line);
	$nbr_of_authorized_areas = count($busline['authorized_areas']);
	
	$links = $bus_line['links_list'];
	$links_lenght = count($links);
	
	//init
	$previous_link = $links[0];
	$previous_link['square'] = found_square_coords_of_vertex($previous_link);
	$area_index = 0;
	
	//create squares between links:
	for($i = 0; $i < $links_lenght-1; $i++){
		$next_link = $links[$i+1];
		$next_link['square'] = found_square_coords_of_vertex($next_link);		
		
		//CASE OF LINK IN THE VERTEX WHICH DELIMIT AN AREA NOT HANDLING, MUST NOT BE NECESSARY TO HANDLE
		//is it possible to create squares in the area between links:
		if($busline['authorized_areas'][$area_index]->enter <= $next_link['prevIndex']){
			create_squares_between_links($previous_link, $next_link, $busline, $busline['authorized_areas'][$area_index]);
		}
		
		//handle $area_index
		if($previous_link['prevIndex'] >= $busline['authorized_areas'][$area_index]->out){
			$area_index++;
			if($area_index >= $nbr_of_authorized_areas){
				break;
			}
		}
		
		$previous_link = $next_link;
	}
}