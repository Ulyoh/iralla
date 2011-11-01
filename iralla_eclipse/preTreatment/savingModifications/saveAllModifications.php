<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
	require_once 'access_to_db.php';
	require 'saveToDb.php';  //TO DO : OPTIMISER LES OUVERTURES DE LA BDD
	require 'modifyDb.php';
	
	//$received=
	$received = $_POST["q"];
	
	$received = json_decode($received);

	if (!empty($received->busLinesList)){
		modifyDb($received->busLinesList, 'bus_lines');
	}
	
	//for the new bus station: adding
	if (!empty($received->buStationsList)){
		modifyDb($received->buStationsList, 'bus_stations');
	}
	
	global $bdd;
	
	if (!empty($received->busLinksList)){
		global $bdd;
		
		if($received->which == 'undefined'){
			$bdd->query("
				DELETE links . * 
				
				FROM 
					links,
					(
					SELECT id, bus_stations.type
					FROM bus_stations
					WHERE bus_stations.type <>  'virtual'
					) AS idsToDelete
					
				WHERE links.busStationId = idsToDelete.id
				");			
		}
		elseif ($received->which == 'virtual'){
			/*$bdd->query("
				DELETE links . * 
				
				FROM 
					links,
					(
					SELECT id, bus_stations.type
					FROM bus_stations
					WHERE bus_stations.type =  'virtual'
					) AS idsToDelete
					
				WHERE links.busStationId = idsToDelete.id
				");*/
		}

		saveToDb($received->busLinksList, 'links');
	
/*		//replace the id from javascript by id from db in the buslineid column of links table:
		//extract all the link with an busLineId negative and the 
		
		$toReplaceBusStationsIdsValues = $bdd->query("SELECT links.id,bus_stations.id,bus_stations.idFromJavascript  FROM links,bus_stations  WHERE links.busStationId = bus_stations.idFromJavascript");
        
		while($links = $toReplaceBusStationsIdsValues->fetch()){
			$req = $bdd->prepare('UPDATE links SET busStationId = ?  WHERE links.id=' .$links[0] ); //links->id from links has been over write by bus_stations.id 
			$req->execute(array($links[1])); // links->id <=> $links[1]
		}
		if($req){
			$req->closeCursor();
		}*/
	}
	
	//remove all the IdFromJavascript:
	//$bdd->query("UPDATE bus_stations SET idFromJavascript = NULL");
	
	echo 'all datas saved';

?>
