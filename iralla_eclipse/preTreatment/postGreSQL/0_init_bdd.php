<?php
require_once 'access_to_postgreSQL.php';
	
	
	//create the table of links if not exist
	$query = 'CREATE TABLE IF NOT EXISTS links (
            id SERIAL PRIMARY KEY,					--mySQL UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            busStationId INTEGER NOT NULL,			--mySQL MEDIUMINT(6) UNSIGNED,
            busLineId INTEGER NOT NULL,				--mySQL MEDIUMINT(6) UNSIGNED,
            prevIndex SMALLINT NOT NULL,			--mySQL SMALLINT(4) UNSIGNED,
            distanceToPrevIndex SMALLINT NOT NULL, 	--mySQL MEDIUMINT(6),
            lat FLOAT(35) NOT NULL, 				--mySQL FLOAT(12,10),
            lng FLOAT(35) NOT NULL, 				--mySQL FLOAT(12,10),
            inUse BOOL DEFAULT true NOT NULL		--mySQL inUse BOOL DEFAULT 1,
           											--mySQL INDEX busLineId (busLineId, prevIndex, distanceToPrevIndex)
            );';

	
	if (($bdd->exec($query)) === false){
		echo "table links could not be created\n";
		return;
	}
	else{
		echo "table links created or already exists\n";
	}
	
	create_index('busLineId_idx', 'busLineId, prevIndex, distanceToPrevIndex');

	//create the table of bus stations if not exist
	$query = "CREATE TABLE IF NOT EXISTS bus_stations (
			id SERIAL PRIMARY KEY,		            --mySQL id MEDIUMINT(6) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
			type VARCHAR(20),			            --mySQL type VARCHAR(20),
			name VARCHAR(100),						--mySQL name TINYTEXT,
			lat FLOAT(35) NOT NULL,		            --mySQL lat FLOAT(12,10),
			lng FLOAT(35) NOT NULL, 	            --mySQL lng FLOAT(12,10),
			circleCenterLat	FLOAT(35) NOT NULL,		--mySQL circleCenterLat FLOAT(12,10),
			circleCenterLng	FLOAT(35) NOT NULL,		--mySQL circleCenterLng FLOAT(12,10),
			circleRadius  SMALLINT NOT NULL,		--mySQL circleRadius SMALLINT UNSIGNED,
			linksListIds TEXT,			            --mySQL linksListIds TEXT,
			layerId INTEGER NOT NULL,				--mySQL layerId MEDIUMINT(6) UNSIGNED,
			inUse BOOL DEFAULT true NOT NULL,		--mySQL inUse BOOL DEFAULT 0,
			idFromJavascript INTEGER NOT NULL		--mySQL idFromJavascript MEDIUMINT(6)
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
			id SERIAL PRIMARY KEY,		            --mySQL id MEDIUMINT(6) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
			name VARCHAR(100),			            --mySQL name TINYTEXT,
			layerId INTEGER NOT NULL,				--mySQL layerId MEDIUMINT(6) UNSIGNED,
			layerName VARCHAR(100),		            --mySQL layerName TINYTEXT,
			type VARCHAR(8),			            --mySQL type ENUM('by_foot','mainLine','feeder','other'),
			CHECK (type IN ('by_foot','mainLine','feeder','other')),
			color VARCHAR(6),			            --mySQL color VARCHAR(6),
			path TEXT,					            --mySQL path TEXT,
			flows SMALLINT,				            --mySQL flows TINYTEXT,
			CHECK ( 0 <= flows AND flows <= 3),
			boundariesListId VARCHAR(255),			--mySQL boundariesListId TINYTEXT,
			pathsAreaOfBusStations TEXT,			--mySQL pathsAreaOfBusStations TEXT,
			areaOnlyBusStations TEXT,				--mySQL areaOnlyBusStations TEXT,
			busStationsIdsList TEXT,				--mySQL busStationsIdsList TEXT,
			connectionsIdsList TEXT,				--mySQL connectionsIdsList TEXT,
			inUse BOOL DEFAULT false NOT NULL,		--mySQL inUse BOOL DEFAULT 0,
			idFromJavascript INTEGER NOT NULL		--mySQL idFromJavascript MEDIUMINT(6)
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
            id SERIAL PRIMARY KEY,		            --mySQL id MEDIUMINT(6) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(100),			            --mySQL name TINYTEXT,
            layerId INTEGER NOT NULL,				--mySQL layerId MEDIUMINT(6) UNSIGNED,
            color VARCHAR(6),						--mySQL color VARCHAR(6),
            path TEXT								--mySQL path TEXT
            )";

	if (($bdd->exec($query)) === false){
		echo "table arrows could not be created\n";
		return;
	}
	else{
		echo "table arrows created or already exists\n";
	}
	
	return;
	
	//create the table of connections between nearest bus stations if not exist
	$query = "CREATE TABLE IF NOT EXISTS nearest_connected_bus_stations (
            id SERIAL PRIMARY KEY,		            					--mySQL MEDIUMINT(6) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            
            busStationIdDeparture INTEGER NOT NULL						--mySQL MEDIUMINT(6) UNSIGNED,
            busStationNameDeparture VARCHAR(100)						--mySQL TINYTEXT,
            busStationLatDeparture FLOAT(35) NOT NULL,					--mySQL FLOAT(12,10),
            busStationLngDeparture FLOAT(35) NOT NULL,					--mySQL FLOAT(12,10),
            busStationTypeDeparture VARCHAR(20),						--mySQL VARCHAR(20),
            
            linkIdDeparture INTEGER NOT NULL							--mySQL MEDIUMINT(6) UNSIGNED,
            linkPrevIndexDeparture SMALLINT NOT NULL,					--mySQL SMALLINT(4) UNSIGNED,
            linkDistanceToPrevIndexDeparture INTEGER NOT NULL			--mySQL MEDIUMINT(6) UNSIGNED,
            linkLatDeparture FLOAT(35) NOT NULL,		            	--mySQL FLOAT(12,10),
            linkLngDeparture FLOAT(35) NOT NULL,		            	--mySQL FLOAT(12,10),
            
            nextLinkId INTEGER NOT NULL									--mySQL MEDIUMINT(6) UNSIGNED,
            nextLinkPrevIndex SMALLINT NOT NULL,						--mySQL SMALLINT(4) UNSIGNED,
            nextLinkDistanceToPrevIndex INTEGER NOT NULL				--mySQL MEDIUMINT(6) UNSIGNED,
            nextLinkLat FLOAT(35) NOT NULL,		            			--mySQL FLOAT(12,10),
            nextLinkLng FLOAT(35) NOT NULL,		            			--mySQL FLOAT(12,10),
            
            nextBusStationId INTEGER NOT NULL							--mySQL MEDIUMINT(6) UNSIGNED,
            nextBusStationName VARCHAR(100),			        		--mySQL TINYTEXT,
            nextBusStationLat FLOAT(35) NOT NULL,		            	--mySQL FLOAT(12,10),
            nextBusStationLng FLOAT(35) NOT NULL,		            	--mySQL FLOAT(12,10),
            nextBusStationType VARCHAR(20),								--mySQL VARCHAR(20),
            
            busLineId INTEGER NOT NULL									--mySQL MEDIUMINT(6) UNSIGNED,
            busLineName VARCHAR(100),			            			--mySQL TINYTEXT,
            busLinePath TEXT,											--mySQL TEXT,
            busLineType VARCHAR(8),										--mySQL ENUM('none','mainLine','feeder','other'),
            CHECK busLineType IN ('none','mainLine','feeder','other'),
            length  INTEGER NOT NULL									--mySQL MEDIUMINT(6) UNSIGNED,
            
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
            id SERIAL PRIMARY KEY,		            		--mySQL id MEDIUMINT(6) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            start_bus_station_id INTEGER NOT NULL			--mySQL MEDIUMINT(6) UNSIGNED,
            start_lat FLOAT(35) NOT NULL,		            --mySQL  FLOAT(12,10),
            start_lng FLOAT(35) NOT NULL,		            --mySQL  FLOAT(12,10),
            end_bus_station_id INTEGER NOT NULL				--mySQL MEDIUMINT(6) UNSIGNED,
            end_lat FLOAT(35) NOT NULL,		            	--mySQL  FLOAT(12,10),
            end_lng FLOAT(35) NOT NULL,		           		--mySQL  FLOAT(12,10),
            road_datas LONGTEXT,							--mySQL TEXT,
            time INTEGER NOT NULL							--mySQL MEDIUMINT(6) UNSIGNED,
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
			id SERIAL PRIMARY KEY,		            			--mySQL id MEDIUMINT(6) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
			bus_line_id INTEGER NOT NULL						--mySQL MEDIUMINT(6) UNSIGNED,
			bus_line_name VARCHAR(100),			            	--mySQL TINYTEXT,
			lat INTEGER											--mySQL INT,
			lng INTEGER											--mySQL INT,
			from_link_lat INTEGER								--mySQL INT(9),
			from_link_lng INTEGER								--mySQL INT(9),
			go_in_point_lat INTEGER NOT NULL					--mySQL MEDIUMINT(6) UNSIGNED,
			go_in_point_lng INTEGER NOT NULL					--mySQL MEDIUMINT(6) UNSIGNED,
			path TEXT,											--mySQL TEXT,
			previous_index_of_go_out SMALLINT NOT NULL,			--mySQL SMALLINT UNSIGNED,
			previous_vertex_of_link SMALLINT NOT NULL,			--mySQL SMALLINT UNSIGNED,
			length INTEGER										--mySQL INT,
			id_of_bus_station_linked INTEGER NOT NULL			--mySQL MEDIUMINT(6) UNSIGNED,
			link_id INTEGER NOT NULL							--mySQL MEDIUMINT(6) UNSIGNED ,
			previous_link_id INTEGER NOT NULL					--mySQL MEDIUMINT(6) UNSIGNED ,
			next_link_id INTEGER NOT NULL						--mySQL MEDIUMINT(6) UNSIGNED ,
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
			id SERIAL PRIMARY KEY,		            			--mySQL id MEDIUMINT(6) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
			bus_line_id INTEGER NOT NULL						--mySQL MEDIUMINT(6) UNSIGNED,
			bus_line_name VARCHAR(100),			           		--mySQL TINYTEXT,
			lat INTEGER											--mySQL INT,
			lng INTEGER											--mySQL INT,
			go_out_point_lat INTEGER NOT NULL					--mySQL MEDIUMINT(6) UNSIGNED,
			go_out_point_lng INTEGER NOT NULL					--mySQL MEDIUMINT(6) UNSIGNED,
			to_link_lat INTEGER									--mySQL INT(9),
			to_link_lng INTEGER									--mySQL INT(9),
			path TEXT,											--mySQL TEXT,
			from_index SMALLINT NOT NULL,						--mySQL SMALLINT UNSIGNED,
			to_index SMALLINT NOT NULL,							--mySQL SMALLINT UNSIGNED,
			length INTEGER										--mySQL INT,
			id_of_bus_station_linked INTEGER NOT NULL			--mySQL MEDIUMINT(6) UNSIGNED,
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
		id SERIAL PRIMARY KEY,		            				--mySQL id MEDIUMINT(6) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
		bl_id INTEGER NOT NULL									--mySQL MEDIUMINT(6) UNSIGNED,
		bl_name VARCHAR(100),			            			--mySQL TINYTEXT,
		lat INTEGER												--mySQL INT,
		lng INTEGER												--mySQL INT,
		pt_coords VARCHAR(50),									--mySQL VARCHAR(50),
		prev_index_of_pt SMALLINT NOT NULL,						--mySQL SMALLINT UNSIGNED,
		prev_vertex_of_prev_link SMALLINT NOT NULL,				--mySQL SMALLINT UNSIGNED,
		prev_vertex_of_next_link SMALLINT NOT NULL,				--mySQL SMALLINT UNSIGNED,
		prev_link_coords VARCHAR(50),							--mySQL VARCHAR(50),
		next_link_coords VARCHAR(50),							--mySQL VARCHAR(50),
		prev_bs_linked_id INTEGER NOT NULL						--mySQL MEDIUMINT(6) UNSIGNED,
		next_bs_linked_id INTEGER NOT NULL						--mySQL MEDIUMINT(6) UNSIGNED,
		distance_to_prev_link INTEGER							--mySQL INT,
		distance_to_next_link INTEGER							--mySQL INT,
		distance_from_first_vertex INTEGER						--mySQL INT,
		previous_link_id INTEGER NOT NULL						--mySQL MEDIUMINT(6) UNSIGNED,
		next_link_id INTEGER NOT NULL							--mySQL MEDIUMINT(6) UNSIGNED,
		flows SMALLINT,				            				--mySQL flows TINYTEXT,
		CHECK ( 0 <= flows AND flows <= 3),
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
			id SERIAL PRIMARY KEY,		            			--mySQL id MEDIUMINT(4) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
			bus_line_id INTEGER NOT NULL						--mySQL MEDIUMINT(6) UNSIGNED,
			word VARCHAR(10),									--mySQL VARCHAR(10),
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
			id SERIAL PRIMARY KEY,		            			--mySQL id MEDIUMINT(8) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
			way_name VARCHAR(150),								--mySQL VARCHAR(150),
			way_name_id INTEGER NOT NULL						--mySQL MEDIUMINT(8) UNSIGNED,
			reference BIGINT NOT NULL							--mySQL BIGINT UNSIGNED,
			lat VARCHAR(12),									--mySQL VARCHAR(12),
			lng VARCHAR(12),									--mySQL VARCHAR(12),
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
			id SERIAL PRIMARY KEY,		            			--mySQL id MEDIUMINT(8) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
			word VARCHAR(40),									--mySQL VARCHAR(40),
			way_name_id INTEGER NOT NULL						--mySQL MEDIUMINT(8),
			nth	SMALLINT,										--mySQL TINYINT UNSIGNED,
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
			id SERIAL PRIMARY KEY,		           				--mySQL id MEDIUMINT(8) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
			way_name VARCHAR(150),								--mySQL VARCHAR(150),
			way_name_reduced VARCHAR(150),						--mySQL VARCHAR(150),
			words_selected_to_search VARCHAR(180),				--mySQL VARCHAR(180),
			word_quantity_to_search	SMALLINT,					--mySQL TINYINT UNSIGNED,
			geolocalisation_ids	INTEGER							--mySQL  MEDIUMTEXT ???seems that was an error
            )";

	if (($bdd->exec($query)) === false){
		echo "table geo_ways_names could not be created\n";
		return;
	}
	else{
		echo "table geo_ways_names created or already exists\n";
	}
		
	echo'all the tables created';
	
	
	function create_index($index_name, $columns){
		global $bdd;
		try {
			//create the table of links if not exist
			$query = '	CREATE INDEX "'.$index_name.'"
			ON links
			USING btree
			('. $columns .');';
	
			$bdd->exec($query);
			echo "index ".$index_name." created \n";
	
		} catch (Exception $e) {
			echo "index ".$index_name." already exists \n";
		}
	}
?>