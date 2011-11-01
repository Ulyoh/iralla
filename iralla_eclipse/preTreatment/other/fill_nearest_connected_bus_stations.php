<?php
	require_once 'access_to_db.php';
	require 'saveToDb.php';  //TO DO : OPTIMISER LES OUVERTURES DE LA BDD
	require 'modifyDb.php';
	
	/* Vérification de la connexion */
	if (mysqli_connect_errno()) {
		printf("échec de la connexion : %s\n", mysqli_connect_error());
		exit();
	}
			
	//extract links table ordered by : busLineId, prevIndex, distanceToPrevIndex
	$bus_line_id_list = $bdd->query("
		 SELECT 
			links.id,
			links.lat,
			links.lng,
		 	busStationId,
		 	busLineId,
		 	prevIndex,
		 	distanceToPrevIndex,
		 	bus_lines.path,
		 	bus_lines.flows,
		 	bus_lines.name AS busLineName,
		 	bus_lines.vertexListOfFlowChange,
		 	bus_stations.name AS busStationName,
		 	bus_stations.lat AS busStationLat,
		 	bus_stations.lng AS busStationLng,
		 	bus_stations.type AS busStationType
		FROM 
			links,
			bus_lines,
            bus_stations
		WHERE
			links.busLineId = bus_lines.id
			AND links.busStationId = bus_stations.id
		ORDER BY
		busLineId, prevIndex, distanceToPrevIndex
	");
	$couple_of_coordinates = array();
	$path = array();
	$flows = array();
	$connections_to_save = array();
	$previous_bus_line_id = -1;
	$path_list_by_bus_line_id = array();
	
	while($current_link = $bus_line_id_list->fetch()){
		
		if ($previous_link[busLineId] == $current_link[busLineId]){
			//pre treatment:
			
			//distance from last vertex:
			$distance_from_first_link_to_current_link = distanceFromFirstVertex($path, $current_link[prevIndex]);
			$distance_from_last_link = $distance_from_first_link_to_current_link - $distance_from_previous_link_to_current_link + $current_link[distanceToPrevIndex];
			
			//handling flow value 1/3:
			//if the bus station is a boundary
			if ($current_link[busStationType] == 'boundary'){
				$flow_key++;
				//change value of next flow
				$next_flow = $flows_list[$flow_key];
			}
			
			$connection_to_save[length] = $distance_from_last_link;
			if (($next_flow == 'normal') || ( $next_flow == 'both')){
				$connection_to_save[linkIdDeparture] = $previous_link[id];
				$connection_to_save[linkPrevIndexDeparture] = $previous_link[prevIndex];
				$connection_to_save[linkDistanceToPrevIndexDeparture] = $previous_link[distanceToPrevIndex];
				//$connection_to_save[linkLatLngDeparture] = $previous_link[lat] . "," . $previous_link[lng];
				$connection_to_save[busStationIdDeparture] =$previous_link[busStationId];
				$connection_to_save[busStationNameDeparture] =$previous_link[busStationName];
				//$connection_to_save[busStationLatLngDeparture] = $previous_link[busStationLat] . "," .$previous_link[busStationLng];
				
				$connection_to_save[nextLinkId] = $current_link[id];
				$connection_to_save[nextLinkPrevIndex] = $current_link[prevIndex];
				$connection_to_save[nextLinkDistanceToPrevIndex] = $current_link[distanceToPrevIndex];
				//$connection_to_save[nextLinkLatLng] = $current_link[lat] . "," . $current_link[lng];
				$connection_to_save[nextBusStationId] = $current_link[busStationId];
				$connection_to_save[nextBusStationName] = $current_link[busStationName];
				//$connection_to_save[nextBusStationLatLng] = $current_link[busStationLat] . "," . $current_link[busStationLng];
				
				array_push($connections_to_save, $connection_to_save);
				
			}
			if (($previous_flow == 'reverse') || ($previous_flow == 'both')){
				$connection_to_save[linkIdDeparture] = $current_link[id];
				$connection_to_save[linkPrevIndexDeparture] = $current_link[prevIndex];
				$connection_to_save[linkDistanceToPrevIndexDeparture] = $current_link[distanceToPrevIndex];
				//$connection_to_save[linkLatLngDeparture] = $current_link[lat] . "," . $current_link[lng];
				$connection_to_save[busStationIdDeparture] =$current_link[busStationId];
				$connection_to_save[busStationNameDeparture] =$current_link[busStationName];
				//$connection_to_save[busStationLatLngDeparture]= $current_link[busStationLat] . "," . $current_link[busStationLng];
				
				$connection_to_save[nextLinkId] = $previous_link[id];
				$connection_to_save[nextLinkPrevIndex] = $previous_link[prevIndex];
				$connection_to_save[nextLinkDistanceToPrevIndex] = $previous_link[distanceToPrevIndex];
				//$connection_to_save[nextLinkLatLng] = $previous_link[lat] . "," . $previous_link[lng];
				$connection_to_save[nextBusStationId] = $previous_link[busStationId];
				$connection_to_save[nextBusStationName] = $previous_link[busStationName];
				//$connection_to_save[nextBusStationLatLng] = $previous_link[busStationLat] . "," . $previous_link[busStationLng];
				
				array_push($connections_to_save, $connection_to_save);
			}
			
			//handling flow value 2/3:
			//if the bus station is a boundary
			if ($current_link[busStationType] == 'boundary'){
				//change value of previous flow for the next connection to save:
				$previous_flow = $flows_list[$flow_key];
			}
			
		}
		else{
			//extract the path of the line:
			$couple_of_coordinates = explode(",", $current_link[path]);
			
			foreach ($couple_of_coordinates as $coordCouple){
				array_push($path, explode(" ", $coordCouple));
			}
			
			$distance_from_first_link_to_current_link = distanceFromFirstVertex($path, $current_link[prevIndex]);
			$connection_to_save[busLineId] = $current_link[busLineId];
			$connection_to_save[busLineName] = $current_link[busLineName];
			$connection_to_save[busLinePath] = $current_link[path];
			
			//handling flow value:
			$flows_list = explode(" ", $current_link[flows]);
			$flow_key = 0;
			$previous_flow = $flows_list[0];
			$next_flow = $flows_list[0];
		}
		
		
		$previous_link = $current_link;
		$distance_from_previous_link_to_current_link = $distance_from_first_link_to_current_link;
		//$prevDistanceToPrevIndex = $current_link[distanceToPrevIndex;
	}

	saveToDb($connections_to_save, 'nearest_connected_bus_stations');
	echo finished;
	
	
	function distanceFromFirstVertex($path, $vertex_index){
		$distance = 0;
		foreach ($path as $key => $coords) {
			if ($key == 0){
				continue;
			}
			if ($key > $vertex_index){
				break;
			}
			$distance += distanceBetweenTwoPoint($path[$key-1], $path[$key]);
		}
		return $distance;
	}
	
	function distanceBetweenTwoPoint($point_1, $point_2){
		return sqrt(( pow($point_1[0] - $point_2[0], 2) + pow($point_1[1] - $point_2[1], 2) ) );
	}
?>
