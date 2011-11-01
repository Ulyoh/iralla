<?php
//$idList = $_POST['q'];
//$idList = "('11','3','9','23','24','4','5','28','29','30','31','32','2','17')";
require_once 'access_to_db.php';

$busStationsListFromBdd = $bdd->query("SELECT * FROM bus_stations where id = 945");
// id = 251 or id = 541 or id = 489 or id = 586
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

