<?php

function extract_datas_from_db(){
	global $bus_lines_list;
	global $bdd;

	//todebug, removing the truncates:
	////////////////////////////////////////////////////////////////////// SEGURE
	$bdd->query("TRUNCATE TABLE squares");

	//extract the links by bus lines and in order:
	$links_by_bus_lines_db = $bdd->query("
			SELECT
			*
			FROM
				links

			ORDER BY
				busLineId, prevIndex, distanceToPrevIndex
			");

	//extract the bus lines of the database
	$bus_lines_list_db = $bdd->query("
			SELECT
				bus_lines.id AS bus_line_id,
				name AS bus_line_name,
				path AS path_string,
				type,
				flows,
				areaOnlyBusStations

			FROM
				bus_lines
			");

	while($bus_line = $bus_lines_list_db->fetch()){
		$bus_lines_list[$bus_line['bus_line_id']] = $bus_line;
	}

	//make list of connection and distance between them
	//for each bus line
	$previous_bus_line_id = NULL;

	while($one_link = $links_by_bus_lines_db->fetch()){
		$bus_line_id = $one_link['busLineId'];

		//if the begining of a busline:
		if(($previous_bus_line_id == NULL) || ($bus_line_id != $previous_bus_line_id)){
			//save the links list of the previous bus line:
			if(($previous_bus_line_id != NULL)
					&& ($bus_lines_list[$previous_bus_line_id]['type'] != 'mainLine'))
			{
				//if the path of the previous bus line is a loop:
				/*if(($path[0][0] == $path[$path_length-1][0])
				 && ($path[0][1] == $path[$path_length-1][1]))
				{
				$links_list_length = count($links_list);
				$links_list[$links_list_length-1][next_link_distance] =
				$bus_line_length
				- $links_list[$links_list_length-1][distance_from_first_vertex]
				+ $links_list[0][distance_from_first_vertex];

				$links_list[0][previous_link_distance] =
				$links_list[$links_list_length-1][next_link_distance];
				}*/

				//save the links list:
				$bus_lines_list[$previous_bus_line_id]['links_list'] = $links_list;
				$links_list = NULL;
			}
			$path = extarct_path($bus_lines_list[$bus_line_id]['path_string']);
			$path_length = count($path);
			$one_link['distance_from_first_vertex'] =
			real_distance_from_first_vertex($path, $one_link['prevIndex'])
			+ $one_link['distanceToPrevIndex'];
			$bus_line_length = real_distance_from_first_vertex($path, $path_length-1);
			$distance_to_last_vertex = $bus_line_length/* - $distance_from_first_vertex*/;

		}
		else if (($bus_line_id == $previous_bus_line_id)
				&& ($bus_lines_list[$bus_line_id]['type'] != 'mainLine')){
			$one_link['distance_from_first_vertex'] =
			real_distance_from_first_vertex($path, $one_link['prevIndex'])
			+ $one_link['distanceToPrevIndex'];

			$links_list_length = count($links_list);
			$one_link['previous_link_distance'] =
			$one_link['distance_from_first_vertex']
			- $links_list[$links_list_length-1]['distance_from_first_vertex'];

			$links_list[$links_list_length-1]['next_link_distance'] = $one_link['previous_link_distance'];
		}
		if($bus_lines_list[$bus_line_id]['type'] != 'mainLine'){
			$links_list[] = $one_link;
		}
		$previous_bus_line_id = $bus_line_id;
	}

	$links_by_bus_lines_db = null;
	$bus_lines_list_db = null;

	unset($bus_line_id, $bus_line_length, $distance_to_last_vertex);

	//last bus line links to save:
	if($bus_lines_list[$previous_bus_line_id]['type'] != 'mainLine'){
		//if the path of the previous bus line is a loop:
		/*if(($path[0][0] == $path[$path_length-1][0])
		 && ($path[0][1] == $path[$path_length-1][1])){
		$links_list_length = count($links_list);
		$links_list[$links_list_length-1][next_link_distance] =
		$bus_line_length
		- $links_list[$links_list_length-1][distance_from_first_vertex]
		+ $links_list[0][distance_from_first_vertex];

		$links_list[0][previous_link_distance] =
		$links_list[$links_list_length-1][next_link_distance];
		}*/
		//save the links list:
		$bus_lines_list[$previous_bus_line_id]['links_list'] = $links_list;
		$links_list = NULL;
	}
}
