<?php
require_once 'access_to_db.php';

$busStationsListFromBdd = $bdd->query("SELECT * FROM bus_stations");

$busStationsList = array();
$addThisBusStation = array();
while($oneBusStation = $busStationsListFromBdd->fetch()){
	$i = 0;
	foreach($oneBusStation as $key => $value){
		if (!is_numeric($key)){
			$addThisBusStation[$key] = $value;
						$i++;
		}
	}
	$busStationsList[] = $addThisBusStation;
}

$busStationsListFromBdd->closeCursor();

$jsonbusStationsList = json_encode($busStationsList);

echo $jsonbusStationsList;

?>

