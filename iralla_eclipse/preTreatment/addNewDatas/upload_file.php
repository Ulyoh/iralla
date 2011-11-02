<?php
include 'saveToDb.php';
require_once 'access_to_db.php';

/*	if ($_FILES["file"]["type"] != "xml"){
		header('Location: /iralla/preTreatment/addNewDatas/index.html');
		return;
	}*/

	$doc = new DOMDocument();
	$doc->load($_FILES["file"]["tmp_name"] );
	//$doc->load('C:\xampp\htdocs\iralla\1Ac2oGhS26J\troncales.xml');

	//extract the coordinate 0
	$elt = $doc->getElementsByTagName('O')->item(0);
	$latRef = str_replace(',', '.',$elt->getattribute('lat'));
	$lngRef = str_replace(',', '.',$elt->getattribute('lng'));
	
	//extract the list of bus lines:
	$layers_list = $doc->getElementsByTagName('L');

	//find the highest layerId:
	//$highestLayerId = $bdd->query("SELECT MAX(layerId) FROM arrows.layerId");
	$highestLayerIdFromBdd = $bdd->query("SELECT MAX(layerId) FROM arrows");
	$highestLayerId = $highestLayerIdFromBdd->fetch();
	$highestLayerIdFromBdd->closeCursor();
	$highestLayerId = $highestLayerId[0];
	
	if(isset($highestLayerId) == false){
		$highestLayerId = -1;
	}
	
	//set the next value for a new layer
	$newIdOfLayer = ++$highestLayerId;
	
	$layerNameListFromBdd = $bdd->query("SELECT DISTINCT layerName,layerId FROM bus_lines ORDER BY layerName ASC");
	$layerNameList = $layerNameListFromBdd->fetchAll();
	$layerNameListFromBdd->closeCursor();
	
	
	//create an object for each layer:
	for ($i = 0; $i < $layers_list->length; $i++) {
    	$layer = $layers_list->item($i);
		
		//bus line name:
		$bus_line_name = $layer->getAttribute('name');
		
		//layer name:
		$layer_name = $bus_line_name;
		
		//set the id dependind on the name of the layer:
		$layerId = $newIdOfLayer;
		$newIdOfLayer++;
		//if the layer name already exist:
		$lengthOfLayerNameList = count($layerNameList);
		for ($n = 0; $n < $lengthOfLayerNameList - 1; $n++) {
			if ($layerNameList[$n]['layerName'] == $layer_name){
				//used the layerId already given
				$layerId = $layerNameList[$n]['layerId'];
				//reset $newIdOfLayer:
				$newIdOfLayer--;
				break;
			}
		}

		//TODO: MAKE MODIFICATION IN THE XML FILE TO GET THE VALUE DIRECTLY
		
		//if the first 6 letter of $data[name] == TRONCA
		if (( stristr($bus_line_name,'troncal') != FALSE) || ( stristr($bus_line_name,'mainLine') != FALSE)){
			$type = 'mainLine';
		}
			
		//if the first 6 letter of $data[name] == ALIMEN
		else if (( stristr($bus_line_name,'alimen') != FALSE) || ( stristr($bus_line_name,'feeder') != FALSE)){
			$type = 'feeder';
		}
		//else
		else{
			$type = 'other';
		}
		
		//END TODO
		
		//bus Lines treatment:
		$polylines_list = $layer->getElementsByTagName('P');
		
		for ($j = 0; $j < $polylines_list->length; $j++) {
			//one polyline:
			$polyline = $polylines_list->item($j);

			//its color:
			$color = $polyline->getElementsByTagName('C')->item(0)->getattribute('value');
			
			//extract the path:
			$points_list = $polyline->getElementsByTagName('V');
			$path  = array();
			for ($k = 0; $k < $points_list->length; $k++) {
				$latLng = array();
				$latLng [lat] = $latRef + str_replace(',', '.',$points_list->item($k)->getattribute('lat'));
				$latLng [lng] = $lngRef + str_replace(',', '.',$points_list->item($k)->getattribute('lng'));
				//$path .= ',' . $lat . ' ' . $lng;
				$path[] = $latLng;
			}
			//$path = substr_replace($path, '', 0, 1);
		
			
			$encode_path = json_encode($path);
			
			$bus_lines_list[] = array(
				'name' => $bus_line_name,
				'layerId' => $layerId,
				'layerName' => $layer_name,
				'type' => $type,
				'color' => $color,
				'path' => $encode_path
			);
		}
		
		//arrows treatment
		$polylines_list = $layer->getElementsByTagName('A');

		for ($j = 0; $j < $polylines_list->length; $j++) {
			//one polyline:
			$polyline = $polylines_list->item($j);

			//its color:
			$color = $polyline->getElementsByTagName('C')->item(0)->getattribute('value');
			
			//extract the path:
			$points_list = $polyline->getElementsByTagName('V');
			$path = "";
			for ($k = 0; $k < $points_list->length; $k++) {
				$lat = $latRef + str_replace(',', '.',$points_list->item($k)->getattribute('lat'));
				$lng = $lngRef + str_replace(',', '.',$points_list->item($k)->getattribute('lng'));
				$path .= ',' . $lat . ' ' . $lng;
			}
			$path = substr_replace($path, '', 0, 1);
			
			$arrows_list[] = array(
				'name' => $bus_line_name,
				'layerId' => $layerId,
				'color' => $color,
				'path' => $path
			);
		}
	}
		
	if (isset($bus_lines_list)){
		//save the bus lines in mySQL db:
		saveToDb($bus_lines_list, 'bus_lines');
	}
	
	if (isset($arrows_list)){
		//save the arrows in mySQL db:
		saveToDb($arrows_list, 'arrows');
	}
	
	
	//bus stations treatment:
	$layerNameListFromBdd = $bdd->query("SELECT DISTINCT layerName,layerId FROM bus_lines ORDER BY layerId ASC");
	$layerNameList = $layerNameListFromBdd->fetchAll();
	$layerNameListFromBdd->closeCursor();
	
	$bus_stations_from_xml = $doc->getElementsByTagName('S');

	for($i = 0; $i < $bus_stations_from_xml->length; $i++){
			
		//getlayer name:
		$layerName = $bus_stations_from_xml->item($i)->parentNode->getAttribute('name');
		
		//find layerId:
		$lengthOfLayerNameList = count($layerNameList);
		for ($n = 0; $n < $lengthOfLayerNameList; $n++) {
			if ($layerNameList[$n]['layerName'] == $layerName){
				//used the layerId already given
				$layerId = $layerNameList[$n]['layerId'];
				break;
			}
		}
		
		$list = $bus_stations_from_xml->item($i)->getElementsByTagName('V');
		for($j = 0; $j < $list->length; $j++){
			//extract coordinates:
			$lat = $latRef + str_replace(',', '.',$list->item($j)->getattribute('lat'));
			$lng = $lngRef + str_replace(',', '.',$list->item($j)->getattribute('lng'));
			
			//extract name:
			$stationName = $list->item($j)->getattribute('n');
			
			$bus_stations_list[] = array(
			'layerId' => $layerId,
			'name'=> $stationName,
			'lat' => $lat,
			'lng' => $lng,
			'type' => 'normal'
			);
		}
	}
		
	if (isset($bus_stations_list)){
		//save the bus station in mySQL db:
		saveToDb($bus_stations_list, 'bus_stations');
	}
	
	header('Location: http://www.cortocamino.com/guayaquil/preTreatment/addNewDatas/responseAddNewDatas.html');
?>




