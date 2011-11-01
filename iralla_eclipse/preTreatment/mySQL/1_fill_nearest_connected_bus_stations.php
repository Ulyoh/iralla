<?php
require_once 'access_to_db.php';
require_once 'saveToDb.php';  //TO DO : OPTIMISER LES OUVERTURES DE LA BDD
require_once 'modifyDb.php';
require_once 'tools.php';
	
function fill_nearest_connected_bus_stations(){
	
	bcscale(8);
	
	global $bdd;
	
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
		 	bus_lines.type AS busLineType,
		 	bus_lines.path,
		 	bus_lines.flows,
		 	bus_lines.name AS busLineName,
		 	bus_lines.boundariesListId,
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
	
	
	/*
	 * 
	 * bus_lines.type != 'other'
			AND 
	 * 
	 */
	
	$couple_of_coordinates = array();
	$path = array();
	$flows = array();
	$connections_to_save = array();
	$previous_bus_line_id = -1;
	$path_list_by_bus_line_id = array();
	$distance_from_first_vertex_to_previous_link = 0;
	
	//todebug
	$bus_stations_to_check = array();
	//end to debug
	
	$all_links = array();
	
	while($current_link = $bus_line_id_list->fetch()){
		
		$all_links[] = $current_link;
		
		if ($previous_link[busLineId] == $current_link[busLineId]){
			//pre treatment:
			
			//distance from last vertex:
			
			$distance_from_last_link = distance_between_2_vertex_on_path($path, $previous_link[prevIndex], $current_link[prevIndex]) ;
		/*	
			if($previous_link[prevIndex] < $current_link[prevIndex]){
				$distance_from_last_link += $current_link[distanceToPrevIndex] - $previous_link[distanceToPrevIndex];
			}
			else if ($previous_link[prevIndex] > $current_link[prevIndex]){
				$distance_from_last_link += $previous_link[distanceToPrevIndex] - $current_link[distanceToPrevIndex];
			}
			else{
				$distance_from_last_link = $current_link[distanceToPrevIndex] - $previous_link[distanceToPrevIndex];
			}
		*/
			$distance_from_last_link += $current_link[distanceToPrevIndex] - $previous_link[distanceToPrevIndex];
			if ($distance_from_last_link <= 0){
				//exit("error: distance from last link <= 0");
				echo "error: distance from last link <= 0\n";
				$bus_stations_to_check[] = $previous_link.busStationId;
				$bus_stations_to_check[] = $current_link.busStationId;
			}
			
			/* * $distance_from_first_vertex_to_current_link = distanceFromFirstVertex($path, $current_link[prevIndex]) + $current_link[distanceToPrevIndex];
			$distance_from_first_vertex_to_previous_link = distanceFromFirstVertex($path, $previous_link[prevIndex]) + $previous_link[distanceToPrevIndex];
			$distance_from_last_link = $distance_from_first_vertex_to_current_link - $distance_from_first_vertex_to_previous_link;
			*/
			
			//handling flow value 1/3:
			//if the bus line has boundaries and the bus station is a boundary
			if (($flows_list[1]) && ($current_link[busStationType] == 'boundary')){
				$flow_key++;
				//change value of next flow
				$next_flow = $flows_list[$flow_key];
			}
			
			$connection_to_save[length] = $distance_from_last_link;
			if (($next_flow == 'normal') || ( $next_flow == 'both')){
				$connection_to_save[linkIdDeparture] = $previous_link[id];
				$connection_to_save[linkPrevIndexDeparture] = $previous_link[prevIndex];
				$connection_to_save[linkDistanceToPrevIndexDeparture] = $previous_link[distanceToPrevIndex];
				$connection_to_save[linkLatDeparture] = $previous_link[lat];
				$connection_to_save[linkLngDeparture] = $previous_link[lng];
				$connection_to_save[busStationIdDeparture] =$previous_link[busStationId];/////////
				$connection_to_save[busStationNameDeparture] =$previous_link[busStationName];
				$connection_to_save[busStationLatDeparture] = $previous_link[busStationLat];
				$connection_to_save[busStationLngDeparture] = $previous_link[busStationLng];
				$connection_to_save[busStationTypeDeparture] = $previous_link[busStationType];
				
				$connection_to_save[nextLinkId] = $current_link[id];
				$connection_to_save[nextLinkPrevIndex] = $current_link[prevIndex];
				$connection_to_save[nextLinkDistanceToPrevIndex] = $current_link[distanceToPrevIndex];
				$connection_to_save[nextLinkLat] = $current_link[lat];
				$connection_to_save[nextLinkLng] = $current_link[lng];
				$connection_to_save[nextBusStationId] = $current_link[busStationId];/////////
				$connection_to_save[nextBusStationName] = $current_link[busStationName];
				$connection_to_save[nextBusStationLat] = $current_link[busStationLat];
				$connection_to_save[nextBusStationLng] = $current_link[busStationLng];
				$connection_to_save[nextBusStationType] = $current_link[busStationType];
				
				array_push($connections_to_save, $connection_to_save);
				
			}
			if (($previous_flow == 'reverse') || ($previous_flow == 'both')){
				$connection_to_save[linkIdDeparture] = $current_link[id];
				$connection_to_save[linkPrevIndexDeparture] = $current_link[prevIndex];
				$connection_to_save[linkDistanceToPrevIndexDeparture] = $current_link[distanceToPrevIndex];
				$connection_to_save[linkLatDeparture] = $current_link[lat];
				$connection_to_save[linkLngDeparture] = $current_link[lng];
				$connection_to_save[busStationIdDeparture] =$current_link[busStationId];
				$connection_to_save[busStationNameDeparture] =$current_link[busStationName];
				$connection_to_save[busStationLatDeparture]= $current_link[busStationLat];
				$connection_to_save[busStationLngDeparture]= $current_link[busStationLng];
				$connection_to_save[busStationTypeDeparture] = $current_link[busStationType];
				
				$connection_to_save[nextLinkId] = $previous_link[id];
				$connection_to_save[nextLinkPrevIndex] = $previous_link[prevIndex];
				$connection_to_save[nextLinkDistanceToPrevIndex] = $previous_link[distanceToPrevIndex];
				$connection_to_save[nextLinkLat] = $previous_link[lat];
				$connection_to_save[nextLinkLng] = $previous_link[lng];
				$connection_to_save[nextBusStationId] = $previous_link[busStationId];
				$connection_to_save[nextBusStationName] = $previous_link[busStationName];
				$connection_to_save[nextBusStationLat] = $previous_link[busStationLat];
				$connection_to_save[nextBusStationLng] = $previous_link[busStationLng];
				$connection_to_save[nextBusStationType] = $previous_link[busStationType];
				
				array_push($connections_to_save, $connection_to_save);
			}
			
			//handling flow value 2/3:
			//if the bus line has boundaries  and the bus station is a boundary
			if (($flows_list[1]) && ($current_link[busStationType] == 'boundary')){
				//change value of previous flow for the next connection to save:
				$previous_flow = $flows_list[$flow_key];
			}
			
		}
		else{
		/*	//extract the path of the line:
			$couple_of_coordinates = explode(",", $current_link[path]);
			
			foreach ($couple_of_coordinates as $coordCouple){
				array_push($path, explode(" ", $coordCouple));
			}
			*/
			$path = json_decode($current_link[path]);
			
			$distance_from_first_vertex_to_current_link = distanceFromFirstVertex($path, $current_link[prevIndex]) + $current_link[distanceToPrevIndex];
			$connection_to_save[busLineId] = $current_link[busLineId];
			$connection_to_save[busLineName] = $current_link[busLineName];
			$connection_to_save[busLinePath] = $current_link[path];
			$connection_to_save[busLineType] = $current_link[busLineType];
			
			//handling flow value:
			$flows_list = explode(" ", $current_link[flows]);
			$flow_key = 0;
			$previous_flow = $flows_list[0];
			$next_flow = $flows_list[0];
		}
		
		
		$previous_link = $current_link;
		$distance_from_first_vertex_to_previous_link = $distance_from_first_vertex_to_current_link;
		//$prevDistanceToPrevIndex = $current_link[distanceToPrevIndex;
	}
	
	
	//find all the bus stations on main lines:
	$bus_stations_in_main_road = $bdd->query("
		 SELECT
		 	bus_stations.id AS busStationId,
		 	bus_stations.name AS busStationName,
		 	bus_stations.lat AS busStationLat,
		 	bus_stations.lng AS busStationLng,
		 	bus_stations.type AS busStationType
		FROM 
            bus_stations
		WHERE
			bus_stations.type = 'normal'
	");
	
	
	//find all the bus stations not on main lines:
	$all_bus_stations_from_db = $bdd->query("
		 SELECT
		 	bus_stations.id AS busStationId,
		 	bus_stations.name AS busStationName,
		 	bus_stations.lat AS busStationLat,
		 	bus_stations.lng AS busStationLng,
		 	bus_stations.type AS busStationType
		FROM 
            bus_stations
	");
	
	$all_bus_stations = array();
	while($bus_station = $all_bus_stations_from_db->fetch()){
		$all_bus_stations[] = $bus_station;
	}
	
	$connection_to_save = array();
	//create connections between bus station on main lines and bus stations
	//nearest than 500m ~ 2.5km/h = 720 s
	$vertex_of_main_bus_station = array();
	$vertex_of_bus_station = array();
	while($main_bus_station = $bus_stations_in_main_road->fetch()) {
		$vertex_of_main_bus_station[lat] = $main_bus_station[busStationLat];
		$vertex_of_main_bus_station[lng] = $main_bus_station[busStationLng];
		
		foreach ($all_bus_stations as $bus_station) {
			$vertex_of_bus_station[lat] = $bus_station[busStationLat];
			$vertex_of_bus_station[lng] = $bus_station[busStationLng];
			$length_between_the_bus_stations = real_distance_between_2_vertex($vertex_of_main_bus_station, $vertex_of_bus_station);
			
			if(($bus_station[busStationId] != $main_bus_station[busStationId])
			//distance between the two bus stations < 500m
			&&($length_between_the_bus_stations < 500)
			){
				//$connection_to_save[busLineId] = 0;
				//$connection_to_save[busLineName] = "";
				//$connection_to_save[busLinePath] = "";
				$connection_to_save[busLineType] = "by_foot";
				$connection_to_save[length] = $length_between_the_bus_stations;
				
				//$connection_to_save[linkIdDeparture] = $current_link[id];
				//$connection_to_save[linkPrevIndexDeparture] = $current_link[prevIndex];
				//$connection_to_save[linkDistanceToPrevIndexDeparture] = $current_link[distanceToPrevIndex];
				//$connection_to_save[linkLatDeparture] = $current_link[lat];
				//$connection_to_save[linkLngDeparture] = $current_link[lng];
				$connection_to_save[busStationIdDeparture] = $main_bus_station[busStationId];
				$connection_to_save[busStationNameDeparture] = $main_bus_station[busStationName];
				$connection_to_save[busStationLatDeparture]= $main_bus_station[busStationLat];
				$connection_to_save[busStationLngDeparture]= $main_bus_station[busStationLng];
				$connection_to_save[busStationTypeDeparture] = $main_bus_station[busStationType];
				
				//$connection_to_save[nextLinkId] = $previous_link[id];
				//$connection_to_save[nextLinkPrevIndex] = $previous_link[prevIndex];
				//$connection_to_save[nextLinkDistanceToPrevIndex] = $previous_link[distanceToPrevIndex];
				//$connection_to_save[nextLinkLat] = $previous_link[lat];
				//$connection_to_save[nextLinkLng] = $previous_link[lng];
				$connection_to_save[nextBusStationId] = $bus_station[busStationId];
				$connection_to_save[nextBusStationName] = $bus_station[busStationName];
				$connection_to_save[nextBusStationLat] = $bus_station[busStationLat];
				$connection_to_save[nextBusStationLng] = $bus_station[busStationLng];
				$connection_to_save[nextBusStationType] = $bus_station[busStationType];
				
				array_push($connections_to_save, $connection_to_save);
			}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

	$bdd->query("TRUNCATE TABLE nearest_connected_bus_stations");
	saveToDb($connections_to_save, 'nearest_connected_bus_stations');
	echo "fill nearest connection finished  \n";
	
}





















/*
	function distanceFromFirstVertex($path, $vertex_index){
		$distance = 0;
		foreach ($path as $key => $coords) {
			if ($key == 0){
				continue;
			}
			if ($key > $vertex_index){
				break;
			}
			$distance = bcadd($distance, distance_between_2_point_2($path[$key-1], $path[$key]));
		}
		return $distance;
	}*/
/*	"
	SELECT * FROM
	 (SELECT 
			links.id,
			links.lat,
			links.lng,
		 	links.busStationId,
		 	links.busLineId,
		 	links.prevIndex,
		 	links.distanceToPrevIndex,
		 	bus_lines.id AS busLineIdBis,
		 	bus_lines.type AS busLineType,
		 	bus_lines.path,
		 	bus_lines.flows,
		 	bus_lines.name AS busLineName,
		 	bus_lines.boundariesListId,
		 	bus_stations.id AS busStationIdBis,
		 	bus_stations.name AS busStationName,
		 	bus_stations.lat AS busStationLat,
		 	bus_stations.lng AS busStationLng,
		 	bus_stations.type AS busStationType
		FROM 
			links,
			bus_lines,
            bus_stations
		WHERE
			bus_lines.type != 'other'
			AND links.busLineId = bus_lines.id
			AND bus_lines
		
		) AS buffer
		WHERE
			busStationIdBis = busStationId
		ORDER BY
		busLineId, prevIndex, distanceToPrevIndex
	"
+
	"SELECT links.id,
			links.lat,
			links.lng,
		 	links.busStationId,
		 	links.busLineId,
		 	links.prevIndex,
		 	links.distanceToPrevIndex, bus_stations.id AS busStationIdBis,
		 	bus_stations.name AS busStationName,
		 	bus_stations.lat AS busStationLat,
		 	bus_stations.lng AS busStationLng,
		 	bus_stations.type AS busStationType, bus_lines.id AS busLineIdBis,
		 	bus_lines.type AS busLineType,
		 	bus_lines.path,
		 	bus_lines.flows,
		 	bus_lines.name AS busLineName,
		 	bus_lines.boundariesListId 
	FROM 	links,
			`bus_stations`, 
			bus_lines 
	WHERE bus_stations.id = links.busStationId 
	AND links.busStationId = 81 
	AND bus_lines.id = links.busLineID
	ORDER BY
		busLineId, prevIndex, distanceToPrevIndex
	"*/
fill_nearest_connected_bus_stations();
	
?>