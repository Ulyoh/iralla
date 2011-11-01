<?php 
require_once 'access_to_db.php';

$busLinesListFromBdd = $bdd->query("SELECT * FROM bus_lines ORDER BY  `bus_lines`.`name`");

$busLinesList = array();
$addThisBusLine = array();
while($oneBusLine = $busLinesListFromBdd->fetch()){
	$i = 0;
	foreach($oneBusLine as $key => $value){
		if (!is_numeric($key)){
			$addThisBusLine[$key] = $value;
						$i++;
		}
		
	}
	$busLinesList[] = $addThisBusLine;
}

$busLinesListFromBdd->closeCursor();

$jsonBusLinesList = json_encode($busLinesList);

echo $jsonBusLinesList;

?>
