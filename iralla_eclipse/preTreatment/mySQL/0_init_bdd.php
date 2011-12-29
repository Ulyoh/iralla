<?php
require_once 'access_to_db.php';
	
	
	//create the table of links if not exist
	$query = "CREATE TABLE IF NOT EXISTS links (
            id MEDIUMINT(6) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            busStationId MEDIUMINT(6) UNSIGNED,
            busLineId MEDIUMINT(6) UNSIGNED,
            prevIndex SMALLINT(4) UNSIGNED,
            distanceToPrevIndex MEDIUMINT(6) UNSIGNED,
            lat FLOAT(12,10),
            lng FLOAT(12,10),
            inUse BOOL DEFAULT 1,
            INDEX busLineId (busLineId, prevIndex, distanceToPrevIndex)
            )";
	/*,
	 * ,
            idFromJavascript MEDIUMINT(6) UNSIGNED,
            nextLink1Id MEDIUMINT(6),
            distanceToNextLink1 FLOAT(12,10),
            nextLink2Id MEDIUMINT(6),
            distanceToNextLink2 FLOAT(12,10),
            */

	if (($bdd->exec($query)) === false){
		echo "table links could not be created\n";
		return;
	}
	else{
		echo "table links created or already exists\n";
	}
	
	//create the table of bus stations if not exist
	$query = "CREATE TABLE IF NOT EXISTS bus_stations (
            id MEDIUMINT(6) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            type VARCHAR(20),
            name TINYTEXT,
            lat FLOAT(12,10),
            lng FLOAT(12,10),
            circleCenterLat FLOAT(12,10),
            circleCenterLng FLOAT(12,10),
            circleRadius SMALLINT UNSIGNED,
            linksListIds TEXT,
            layerId MEDIUMINT(6) UNSIGNED,
            inUse BOOL DEFAULT 0,
            idFromJavascript MEDIUMINT(6)
            )";

	if (($bdd->exec($query)) === false){
		echo "table bus_stations could not be created\n";
		return;
	}
	else{
		echo "table bus_stations created or already exists\n";
	}
	
	//create the table of bus lines if not exist
	$query = "CREATE TABLE IF NOT EXISTS bus_lines (
            id MEDIUMINT(6) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            name TINYTEXT,
            layerId MEDIUMINT(6) UNSIGNED,
            layerName TINYTEXT,
            type ENUM('by_foot','mainLine','feeder','other'),
            color VARCHAR(6),
            path TEXT,
            flows TINYTEXT,
            boundariesListId TINYTEXT,
            pathsAreaOfBusStations TEXT,
            areaOnlyBusStations TEXT,
            busStationsIdsList TEXT,
            connectionsIdsList TEXT,
            inUse BOOL DEFAULT 0,
            idFromJavascript MEDIUMINT(6)
            )";

	if (($bdd->exec($query)) === false){
		echo "table bus_lines could not be created\n";
		return;
	}
	else{
		echo "table bus_lines created or already exists\n";
	}

	//create the table of arrows if not exist
	$query = "CREATE TABLE IF NOT EXISTS arrows (
            id MEDIUMINT(6) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            name TINYTEXT,
            layerId MEDIUMINT(6) UNSIGNED,
            color VARCHAR(6),
            path TEXT
            )";

	if (($bdd->exec($query)) === false){
		echo "table arrows could not be created\n";
		return;
	}
	else{
		echo "table arrows created or already exists\n";
	}
	
	//create the table of connections between nearest bus stations if not exist
	$query = "CREATE TABLE IF NOT EXISTS nearest_connected_bus_stations (
            id MEDIUMINT(6) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            
            busStationIdDeparture MEDIUMINT(6) UNSIGNED,
            busStationNameDeparture TINYTEXT,
            busStationLatDeparture FLOAT(12,10),
            busStationLngDeparture FLOAT(12,10),
            busStationTypeDeparture VARCHAR(20),
            
            linkIdDeparture MEDIUMINT(6) UNSIGNED,
            linkPrevIndexDeparture SMALLINT(4) UNSIGNED,
            linkDistanceToPrevIndexDeparture MEDIUMINT(6) UNSIGNED,
            linkLatDeparture FLOAT(12,10),
            linkLngDeparture FLOAT(12,10),
            
            nextLinkId MEDIUMINT(6) UNSIGNED,
            nextLinkPrevIndex SMALLINT(4) UNSIGNED,
            nextLinkDistanceToPrevIndex MEDIUMINT(6) UNSIGNED,
            nextLinkLat FLOAT(12,10),
            nextLinkLng FLOAT(12,10),
            
            nextBusStationId MEDIUMINT(6) UNSIGNED,
            nextBusStationName TINYTEXT,
            nextBusStationLat FLOAT(12,10),
            nextBusStationLng FLOAT(12,10),
            nextBusStationType VARCHAR(20),
            
            busLineId MEDIUMINT(6) UNSIGNED,
            busLineName TINYTEXT,
            busLinePath TEXT,
            busLineType ENUM('none','mainLine','feeder','other'),
            
            length MEDIUMINT(6) UNSIGNED,
            
            INDEX busStationIdDeparture (busStationIdDeparture),
            INDEX nextLinkId (nextLinkId)
            )";
           

	if (($bdd->exec($query)) === false){
		echo "table nearest_connected_bus_stations could not be created\n";
		return;
	}
	else{
		echo "table nearest_connected_bus_stations created or already exists\n";
	}
	
	//create the table of connections between all bus stations if not exist
	$query = "CREATE TABLE IF NOT EXISTS bus_stations_to_bus_stations (
            id MEDIUMINT(6) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            start_bus_station_id MEDIUMINT(6) UNSIGNED,
            start_lat  FLOAT(12,10),
            start_lng  FLOAT(12,10),
            end_bus_station_id MEDIUMINT(6) UNSIGNED,
            end_lat  FLOAT(12,10),
            end_lng  FLOAT(12,10),
            road_datas LONGTEXT,
            time MEDIUMINT(6) UNSIGNED,
            INDEX start_lat_lng (start_lat, start_lng),
            INDEX end_lat_lng (end_lat, end_lng),
            UNIQUE INDEX couple (start_bus_station_id, end_bus_station_id)
            )";

	if (($bdd->exec($query)) === false){
		echo "table bus_stations_to_bus_stations could not be created\n";
		return;
	}
	else{
		echo "table bus_stations_to_bus_stations created or already exists\n";
	}
		
	//create the table of connections between all bus stations if not exist
	$query = "CREATE TABLE IF NOT EXISTS to_square (
			id MEDIUMINT(6) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
			bus_line_id MEDIUMINT(6) UNSIGNED,
			bus_line_name TINYTEXT,
			lat INT,
			lng INT,
			from_link_lat INT(9),
			from_link_lng INT(9),
			go_in_point_lat MEDIUMINT(6) UNSIGNED,
			go_in_point_lng MEDIUMINT(6) UNSIGNED,
			path TEXT,
			previous_index_of_go_out SMALLINT UNSIGNED,
			previous_vertex_of_link SMALLINT UNSIGNED,
			length INT,
			id_of_bus_station_linked MEDIUMINT(6) UNSIGNED,
			link_id MEDIUMINT(6) UNSIGNED ,
			previous_link_id MEDIUMINT(6) UNSIGNED ,
			next_link_id MEDIUMINT(6) UNSIGNED ,
			INDEX lat_lng (lat,lng),
			INDEX bus_line_id (bus_line_id)
            )";

	if (($bdd->exec($query)) === false){
		echo "table to_square could not be created\n";
		return;
	}
	else{
		echo "table to_square created or already exists\n";
	}

		//create the table of connections between all bus stations if not exist
	$query = "CREATE TABLE IF NOT EXISTS from_square (
			id MEDIUMINT(6) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
			bus_line_id MEDIUMINT(6) UNSIGNED,
			bus_line_name TINYTEXT,
			lat INT,
			lng INT,
			go_out_point_lat MEDIUMINT(6) UNSIGNED,
			go_out_point_lng MEDIUMINT(6) UNSIGNED,
			to_link_lat INT(9),
			to_link_lng INT(9),
			path TEXT,
			from_index SMALLINT UNSIGNED,
			to_index SMALLINT UNSIGNED,
			length INT,
			id_of_bus_station_linked MEDIUMINT(6) UNSIGNED,
			INDEX lat_lng (lat,lng)
            )";

	if (($bdd->exec($query)) === false){
		echo "table from_square could not be created\n";
		return;
	}
	else{
		echo "table from_square created or already exists\n";
	}
	
	####################################"""
	//create the table of connections between all bus stations if not exist
	$query = "CREATE TABLE IF NOT EXISTS squares (
		id MEDIUMINT(6) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
		bl_id MEDIUMINT(6) UNSIGNED,
		bl_name TINYTEXT,
		lat INT,
		lng INT,
		pt_coords VARCHAR(50),
		prev_index_of_pt SMALLINT UNSIGNED,
		prev_vertex_of_prev_link SMALLINT UNSIGNED,
		prev_vertex_of_next_link SMALLINT UNSIGNED,
		prev_link_coords VARCHAR(50),
		next_link_coords VARCHAR(50),
		prev_bs_linked_id MEDIUMINT(6) UNSIGNED,
		next_bs_linked_id MEDIUMINT(6) UNSIGNED,
		distance_to_prev_link INT,
		distance_to_next_link INT,
		distance_from_first_vertex INT,
		previous_link_id MEDIUMINT(6) UNSIGNED,
		next_link_id MEDIUMINT(6) UNSIGNED,
		flows TINYTEXT
	)";
	
	if (($bdd->exec($query)) === false){
		echo "table to_square could not be created\n";
		return;
	}
	else{
		echo "table to_square created or already exists\n";
	}
	############################################"
	
	
	//create the table of text to search of rutas
	$query = "CREATE TABLE IF NOT EXISTS words_to_search_rutas (
			id MEDIUMINT(4) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
			bus_line_id MEDIUMINT(6) UNSIGNED,
			word VARCHAR(10),
			INDEX word (word)
            )";

	if (($bdd->exec($query)) === false){
		echo "table words_to_search_rutas could not be created\n";
		return;
	}
	else{
		echo "table words_to_search_rutas created or already exists\n";
	}
	
	
	//create the table of geolocalisation
	$query = "CREATE TABLE IF NOT EXISTS geolocalisation (
			id MEDIUMINT(8) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
			way_name VARCHAR(150),
			way_name_id MEDIUMINT(8) UNSIGNED,
			reference BIGINT UNSIGNED,
			lat VARCHAR(12),
			lng VARCHAR(12),
			INDEX way_name_id (way_name_id),
			INDEX reference (reference)
            )";

	if (($bdd->exec($query)) === false){
		echo "table geolocalisation could not be created\n";
		return;
	}
	else{
		echo "table geolocalisation created or already exists\n";
	}
	
		//create the table of words in way names
	$query = "CREATE TABLE IF NOT EXISTS geo_words_in_ways_names (
			id MEDIUMINT(8) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
			word VARCHAR(40),
			way_name_id MEDIUMINT(8),
			nth TINYINT UNSIGNED,
			INDEX word (word),
			INDEX way_name_id (way_name_id)
            )";

	if (($bdd->exec($query)) === false){
		echo "table words_in_way_name could not be created\n";
		return;
	}
	else{
		echo "table words_in_way_name created or already exists\n";
	}

		//create the table of ways names
	$query = "CREATE TABLE IF NOT EXISTS geo_ways_names (
			id MEDIUMINT(8) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
			way_name VARCHAR(150),
			way_name_reduced VARCHAR(150),
			words_selected_to_search VARCHAR(180),
			word_quantity_to_search TINYINT UNSIGNED,
			geolocalisation_ids MEDIUMTEXT
            )";

	if (($bdd->exec($query)) === false){
		echo "table geo_ways_names could not be created\n";
		return;
	}
	else{
		echo "table geo_ways_names created or already exists\n";
	}
		
	echo'all the tables created';
	
?>