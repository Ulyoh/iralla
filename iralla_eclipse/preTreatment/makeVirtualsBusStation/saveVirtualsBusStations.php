<?php
	include 'saveToDb.php';
	include 'modifyDb.php';

	$received = $_POST["q"];
	//$received = "{\"busLineId\":4,\"orderedListOfVirtualBusStationsId\":[0,6,5,4,4,5,6,7],\"virtualBusStopList\":[{\"id\":0,\"coord\":{\"type\":\"Point\",\"x\":-79.896933,\"y\":-2.201685},\"linksList\":[{\"busLineIndex\":0,\"busLineId\":4,\"busLineName\":\"RUTA  007 P.O. 2009_0\",\"indexOfPreviousVertex\":0,\"distanceToThePreviousVertex\":0,\"coord\":{\"type\":\"Point\",\"x\":-79.896933,\"y\":-2.201685},\"id\":0}]},{\"id\":4,\"coord\":{\"type\":\"Point\",\"x\":-79.89222181,\"y\":-2.20292787},\"linksList\":[{\"busLineIndex\":2,\"busLineId\":54,\"busLineName\":\"RUTA 13 P.O. 2009_0\",\"indexOfPreviousVertex\":3,\"distanceToThePreviousVertex\":0.00601404,\"coord\":{\"type\":\"Point\",\"x\":-79.8922222,\"y\":-2.20292774},\"id\":1},{\"busLineIndex\":0,\"busLineId\":4,\"busLineName\":\"RUTA  007 P.O. 2009_0\",\"indexOfPreviousVertex\":23,\"distanceToThePreviousVertex\":0.00465,\"coord\":{\"type\":\"Point\",\"x\":-79.89222181,\"y\":-2.20292787},\"id\":2}]},{\"id\":5,\"coord\":{\"type\":\"Point\",\"x\":-79.8929479,\"y\":-2.20273672},\"linksList\":[{\"busLineIndex\":1,\"busLineId\":5,\"busLineName\":\"RUTA 35-2 P.O. 2010_0\",\"indexOfPreviousVertex\":0,\"distanceToThePreviousVertex\":0.00412129,\"coord\":{\"type\":\"Point\",\"x\":-79.89294814,\"y\":-2.20273664},\"id\":3},{\"busLineIndex\":0,\"busLineId\":4,\"busLineName\":\"RUTA  007 P.O. 2009_0\",\"indexOfPreviousVertex\":23,\"distanceToThePreviousVertex\":0.00543,\"coord\":{\"type\":\"Point\",\"x\":-79.8929479,\"y\":-2.20273672},\"id\":4}]},{\"id\":6,\"coord\":{\"type\":\"Point\",\"x\":-79.8945159,\"y\":-2.20232394},\"linksList\":[{\"busLineIndex\":1,\"busLineId\":5,\"busLineName\":\"RUTA 35-2 P.O. 2010_0\",\"indexOfPreviousVertex\":20,\"distanceToThePreviousVertex\":0,\"coord\":{\"type\":\"Point\",\"x\":-79.894556,\"y\":-2.202412},\"id\":5},{\"busLineIndex\":2,\"busLineId\":54,\"busLineName\":\"RUTA 13 P.O. 2009_0\",\"indexOfPreviousVertex\":16,\"distanceToThePreviousVertex\":0.00441166,\"coord\":{\"type\":\"Point\",\"x\":-79.89450016,\"y\":-2.2023284},\"id\":6},{\"busLineIndex\":0,\"busLineId\":4,\"busLineName\":\"RUTA  007 P.O. 2009_0\",\"indexOfPreviousVertex\":23,\"distanceToThePreviousVertex\":0.007019999999999999,\"coord\":{\"type\":\"Point\",\"x\":-79.8945159,\"y\":-2.20232394},\"id\":7}]},{\"id\":7,\"coord\":{\"type\":\"Point\",\"x\":-79.896943,\"y\":-2.201685},\"linksList\":[{\"busLineIndex\":0,\"busLineId\":4,\"busLineName\":\"RUTA  007 P.O. 2009_0\",\"indexOfPreviousVertex\":24,\"distanceToThePreviousVertex\":0,\"coord\":{\"type\":\"Point\",\"x\":-79.896545646943,\"y\":-2.201685},\"id\":8}]}]}";

	//echo $received;

 	$received = json_decode($received);
	//extract list of links:
	$listOfLinks = array();
	foreach ($received->virtualBusStopList as $busStation){
		
		foreach ($busStation->linksList as $link){
			$linkToSave = array();
			//$listOfLinks[$key]["javascriptId"] = $link->id;
			$linkToSave["busStationId"] = $busStation->id;
			$linkToSave["busLineId"] = $link->busLineId;
			$linkToSave["prevIndex"] = $link->indexOfPreviousVertex;
			$linkToSave["distanceToPrevIndex"] = $link->distanceToThePreviousVertex;
			$linkToSave["lat"] = floatval($link->coord->y);
			$linkToSave["lng"] = floatval($link->coord->x);
			
			$listOfLinks[] = $linkToSave;
		}
	}
	saveToDb($listOfLinks, 'links');
	
	
	$listOfBusStations = array();
	//extract list of busStations:
	foreach ($received->virtualBusStopList as $busStation){
		$busStationToSave = array();
		$busStationToSave["id"] = $busStation->id;
		$busStationToSave["lat"] = $busStation->coord->y;
		$busStationToSave["lng"] = $busStation->coord->x;
		$busStationToSave["type"] = 'virtual';
		
		$listOfBusStations[] = $busStationToSave;
	}
	saveToDb($listOfBusStations, 'bus_stations');
	
	//save ordered of bus station for a bus line:
	/*$busStationIdList = "";
	foreach ($received->orderedListOfVirtualBusStationsId as $busStationId){
		$busStationIdList = $busStationIdList . ';' . $busStationId;
	}
	//remove ',' from the begins of the strings:
	$busLine = array();
	$busLine["busStationsIdsList"] = substr_replace($busStationIdList, '', 0, 1);
	$busLine["id"] = $received->busLineId;
	$busLineList = array();
	$busLineList["line"] = $busLine;
	
	modifyDb($busLineList, 'bus_lines');
*/
	sleep(30);

	echo json_encode('follow');
?>








