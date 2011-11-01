<?php
require_once 'access_to_db.php';
require_once 'saveToDb.php';  //TO DO : OPTIMISER LES OUVERTURES DE LA BDD
require_once 'modifyDb.php';
require_once 'Vertex.php';
require_once 'tools.php';
	
function create_begin_end_bus_stations(){
	global $bdd;
	//distance max between first and last vertex to make only one bus station:
	$dmax_first_last = 1000;
	//radius of the bus station circle:
	$bus_station_circle_radius = 50;
	
	//remove all invisible links
	$bus_lines_list_db = $bdd->query("
		DELETE links . * 
				
		FROM 
			links,
			(
			SELECT id, bus_stations.type
			FROM bus_stations
			WHERE bus_stations.type =  'invisible'
			) AS idsToDelete
					
		WHERE links.busStationId = idsToDelete.id
		

	");

	//remove all invisible bus station
	$bus_lines_list_db = $bdd->query("
		DELETE 
		FROM bus_stations
		WHERE bus_stations.type =  'invisible'
	");
	
	//remove all unused links
	$bus_lines_list_db = $bdd->query("
		DELETE links . * 	

			FROM links
			LEFT JOIN bus_stations ON links.busStationId = bus_stations.id
			WHERE bus_stations.id IS NULL

	");

	//extract the bus lines of the database
	$bus_lines_list_db = $bdd->query("
		 SELECT 
			id,
		 	name,
		 	path,
		 	type,
		 	flows
		 	
		FROM 
			bus_lines
	");
	
	//preparation to create the new bus stations:
	//get the highest id of the bus stations:
	$highest_bus_station_id_db = $bdd->query("
		 SELECT 
			MAX(id) AS maxId
		 	
		FROM 
			bus_stations
	");
	$bus_station = $highest_bus_station_id_db->fetch();
	$current_max_bus_station_id = $bus_station[maxId];
	
	echo "max bus station id before treatment : $current_max_bus_station_id \n";
	
	//arrays to save the news bus stations
	$new_bus_stations = array();
	
	//preparation to create the links:
	//get the highest id of the bus stations:
	$highest_link_id_db = $bdd->query("
		 SELECT 
			MAX(id) AS maxBusLinkId
		 	
		FROM 
			links
	");
	$link = $highest_link_id_db->fetch();
	$current_max_link_id = $link[maxBusLinkId];

	echo "max link id before treatment : $current_max_link_id \n";
	
	//array to save the news links:
	$new_links = array();

	while($bus_line = $bus_lines_list_db->fetch()){
		
		////////////////////////////////////////////////////////////
		//change this condition to get working with all bus lines //
		// == 'feeder' =>  != 'mainLine'						  //
		////////////////////////////////////////////////////////////
		
		
		if($bus_line[type] != 'mainLine'){
			
			echo "create begin and end links of bus line with id : " . $bus_line[id] . " if not exist\n";
			
			$path = json_decode($bus_line[path]);
			$length_path = count($path);
		
			//if the first and end vertex don't have links
			//get the links of the bus line:
			$links_of_bus_line = $bdd->query("
				SELECT 	* 	
				FROM 	links
				WHERE links.busLineId = $bus_line[id]
			");
			
			//get the last index of the path:
			$last_index = $length_path - 1;
			
			$first_vertex_has_a_link = false;
			$last_vertex_has_a_link = false;
			
			while($link = $links_of_bus_line->fetch()){
				
				//if the first vertex have a link
				if(($link[prevIndex] == 0) && ($link[distanceToPrevIndex] == 0)){
					$first_vertex_has_a_link = true;
				}
				
				//if the last vertex have a link
				if(($link[prevIndex] == $last_index) && ($link[distanceToPrevIndex] == 0)){
					$last_vertex_has_a_link = true;
				}
			}
			
			if(($first_vertex_has_a_link == true) && ($last_vertex_has_a_link == true)){
				continue;
			}
			elseif(($first_vertex_has_a_link == false) && ($last_vertex_has_a_link == false)){
				//just do nothing
			}
			elseif (($first_vertex_has_a_link == false) || ($last_vertex_has_a_link == false)){
				echo("\n\tWARNING : only one of the last and end vertex has a link \n");
			}
			
			//calculate the distance between the first and last vertex of the busline:
			$first_vertex = new Vertex($path[0]->lat, $path[0]->lng);
			$last_vertex = new Vertex($path[$length_path - 1]->lat, $path[$length_path - 1]->lng);
			
			//if distance between two vertex > $dmax_first_last
			//or one of two vertex as a link:
			if((distanceBetweenTwoVertex($last_vertex, $first_vertex) > $dmax_first_last)
			|| ((( this_link_do_not_exist($bus_line[id], 0, 0))
				&& !this_link_do_not_exist($bus_line[id], $length_path - 1, 0))
				|| 
				(( !this_link_do_not_exist($bus_line[id], 0, 0))
				&& this_link_do_not_exist($bus_line[id], $length_path - 1, 0)))){
					
				//if the first vertex not already connected by a link :
				if ( this_link_do_not_exist($bus_line[id], 0, 0)){
					//the first bus station:
					$new_bus_station = array();
					$new_bus_station[id] = ++$current_max_bus_station_id;
					$new_bus_station[type] = "invisible";
					$new_bus_station[lat] = $first_vertex->get_lat();
					$new_bus_station[lng] = $first_vertex->get_lng();
					$new_bus_station[circleCenterLat] = $first_vertex->get_lat();
					$new_bus_station[circleCenterLng] = $first_vertex->get_lng();
					$new_bus_station[circleRadius] = $bus_station_circle_radius;
					
					array_push($new_bus_stations, $new_bus_station);
	
					//the first link:
					$new_link = array();
					$new_link[busStationId] = $current_max_bus_station_id;
					$new_link[busLineId] = $bus_line[id];
					$new_link[prevIndex] = 0;
					$new_link[distanceToPrevIndex] = 0;
					$new_link[lat] = $first_vertex->get_lat();
					$new_link[lng] = $first_vertex->get_lng();
					$new_link[inUse] = 1;
					
					array_push($new_links, $new_link);
					
					echo "first link created\n";
				}
				
				if ( this_link_do_not_exist($bus_line[id], $length_path - 1, 0)){
					//the last bus station:
					$new_bus_station = array();
					$new_bus_station[id] = ++$current_max_bus_station_id;
					$new_bus_station[type] = "invisible";
					$new_bus_station[lat] = $last_vertex->get_lat();
					$new_bus_station[lng] = $last_vertex->get_lng();
					$new_bus_station[circleCenterLat] = $last_vertex->get_lat();
					$new_bus_station[circleCenterLng] = $last_vertex->get_lng();
					$new_bus_station[circleRadius] = $bus_station_circle_radius;
					
					array_push($new_bus_stations, $new_bus_station);
					
					//the last link
					$new_link = array();
					$new_link[busStationId] = $current_max_bus_station_id;
					$new_link[busLineId] = $bus_line[id];
					$new_link[prevIndex] = $length_path - 1;
					$new_link[distanceToPrevIndex] = 0;
					$new_link[lat] = $last_vertex->get_lat();
					$new_link[lng] = $last_vertex->get_lng();
					$new_link[inUse] = 1;
					
					array_push($new_links, $new_link);
					echo "end link created\n";
				}
			}
			else{
				if (( this_link_do_not_exist($bus_line[id], 0, 0))
				&& this_link_do_not_exist($bus_line[id], $length_path - 1, 0)){
				//create 1 invisible bus stations and  2 links:
					//the bus station:
					$new_bus_station = array();
					$new_bus_station[id] = ++$current_max_bus_station_id;
					$new_bus_station[type] = "invisible";
					$new_bus_station[lat] = $last_vertex->get_lat() + ($first_vertex->get_lat() - $last_vertex->get_lat()) / 2;
					$new_bus_station[lng] = $last_vertex->get_lng() + ($first_vertex->get_lng() - $last_vertex->get_lng()) / 2;
					$new_bus_station[circleCenterLat] = $new_bus_station[lat];
					$new_bus_station[circleCenterLng] = $new_bus_station[lng];
					$new_bus_station[circleRadius] = $bus_station_circle_radius;
					
					array_push($new_bus_stations, $new_bus_station);
	
					//the first link:
					$new_link = array();
					$new_link[busStationId] = $current_max_bus_station_id;
					$new_link[busLineId] = $bus_line[id];
					$new_link[prevIndex] = 0;
					$new_link[distanceToPrevIndex] = 0;
					$new_link[lat] = $first_vertex->get_lat();
					$new_link[lng] = $first_vertex->get_lng();
					$new_link[inUse] = 1;
					
					array_push($new_links, $new_link);
					
					//the last link
					$new_link = array();
					$new_link[busStationId] = $current_max_bus_station_id;
					$new_link[busLineId] = $bus_line[id];
					$new_link[prevIndex] = $length_path - 1;
					$new_link[distanceToPrevIndex] = 0;
					$new_link[lat] = $last_vertex->get_lat();
					$new_link[lng] = $last_vertex->get_lng();
					$new_link[inUse] = 1;
					
					array_push($new_links, $new_link);
					
					echo "first and end links created\n";
				}
				else if ((( this_link_do_not_exist($bus_line[id], 0, 0))
				&& !this_link_do_not_exist($bus_line[id], $length_path - 1, 0))
				|| 
				(( !this_link_do_not_exist($bus_line[id], 0, 0))
				&& this_link_do_not_exist($bus_line[id], $length_path - 1, 0))){
					exit("error creating begin end links\n");
				}
			}
		}
	}
	

	saveToDb($new_bus_stations, 'bus_stations');
	saveToDb($new_links, 'links');
	
	
	echo "create begin end connections done \n";
}

function this_link_do_not_exist($bus_line_id, $prev_index, $distance_to_prev_index){
	global $bdd;
	$link = $bdd->query("
		SELECT *
		FROM links
		WHERE links.busLineId = $bus_line_id
		AND links.prevIndex = $prev_index
		AND links.distanceToPrevIndex = $distance_to_prev_index
	");
	
	if(!$link->fetch()){
		return true;
	}
	else{
		return false;
	}
}

create_begin_end_bus_stations();

?>

