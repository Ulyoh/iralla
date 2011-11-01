<?php
require_once 'access_to_db.php';

$datasToSend = array();

//extract the list of bus lines asked
$busLinesListFromBdd = $bdd->query("
	SELECT name, type, color, path, flows
	FROM bus_lines 
	WHERE type = 'mainLine' OR type = 'feeder'"
);

$busLinesList = array();
$addThisBusLine = array();

while($oneBusLine = $busLinesListFromBdd->fetch()){
	foreach($oneBusLine as $key => $value){
		if (!is_numeric($key)){
			$addThisBusLine[$key] = $value;
		}
	}
	//$addThisBusLine[areaOnlyBusStations] = json_decode($addThisBusLine[areaOnlyBusStations]);
	$busLinesList[] = $addThisBusLine;
}
$busLinesListFromBdd->closeCursor();

$jsonDatasToSend = json_encode($busLinesList);

echo 'var mainBusLinesList =' . $jsonDatasToSend;
?>

