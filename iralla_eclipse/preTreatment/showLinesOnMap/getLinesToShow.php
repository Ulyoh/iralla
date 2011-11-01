<?php
$idList = json_decode($_POST['q']);
//$idList = json_decode('["3","9","23","24","4","5","28","29","30","31","32","2","17","39","18","38","19","21","22","36","25","26","27","14","15","20","16","33","37","10","8","13","6","7","12","34","35","1"]');
require_once 'access_to_db.php';

$datasToSend = array();

if(count($idList)>0){
	//verification of the received datas:
	$i = 0;
	
	while(isset($idList[$i])){
		if(! is_numeric($idList[$i])){
			die("it is not a number");
		}
		$i++;
	}
	
	$idListString = "(" . implode(",",$idList) . ")";
	
	//extract the list of bus lines asked
	$busLinesListFromBdd = $bdd->query("SELECT * FROM bus_lines WHERE id IN $idListString");
	
	$busLinesList = array();
	$addThisBusLine = array();
	$layerIdsList = array();
	
	while($oneBusLine = $busLinesListFromBdd->fetch()){
		foreach($oneBusLine as $key => $value){
			if (!is_numeric($key)){
				$addThisBusLine[$key] = $value;
				if($key == 'layerId'){
					$layerIdsList[] = $value;
				}
			}
		}
		//$addThisBusLine[areaOnlyBusStations] = json_decode($addThisBusLine[areaOnlyBusStations]);
		$busLinesList[] = $addThisBusLine;
	}
	$busLinesListFromBdd->closeCursor();
	
	$layerIdsListString = "(" . implode(",",$layerIdsList) . ")";
	
	//find the arrows of the line asked:
	$arrowsListFromBdd = $bdd->query("SELECT * FROM arrows WHERE layerId IN $layerIdsListString");
	
	$arrowsList = array();
	$addThisArrow = array();
	while($oneArrow = $arrowsListFromBdd->fetch()){
		
		foreach($oneArrow as $key => $value){
			if (!is_numeric($key)){
				$addThisArrow[$key] = $value;
				if($key == 'layerId'){
					$layerId = $value;
				}
			}
		}
		if(!array_key_exists($layerId, $arrowsList)){
			$arrowsList[$layerId] = array();
		}
		$arrowsList[$layerId][] = $addThisArrow;
	}
	
	$datasToSend['busLinesList'] = $busLinesList;
	$datasToSend['arrowsList'] = $arrowsList;
	
	
	$jsonDatasToSend = json_encode($datasToSend);
	
	echo $jsonDatasToSend;
}
else{
	echo json_encode(array());
}
?>

