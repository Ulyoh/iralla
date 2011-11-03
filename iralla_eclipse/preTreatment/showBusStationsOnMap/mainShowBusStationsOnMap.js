
function mainShowBusStationsOnMap(){
	
	//send to the servor:
	request({
		phpFileCalled: mysite + 'preTreatment/showBusStationsOnMap/getBusStationsToShow.php',
		//argumentsToPhpFile: 'q=' + listToLoad,
		type: "",
		callback: showBusBusStationsOnMap,
		asynchrone: true
	});
}

function showBusBusStationsOnMap(answer){
	//remove bus stations if there are:
	if ((typeof(SubMap._busStationArray) != 'undefined')
	&& (SubMap._busStationArray.length>0)
	&& (SubMap._busStationArray[0]!= '')){
		while(SubMap._busStationArray.length > 0){
			SubMap._busStationArray.shift().setMap(null);
		}
	}
	var parseAnswer = JSON.parse(answer);

	if (parseAnswer.length > 0) {
		map.addBusStationsFromDb(parseAnswer);
		preTreatmentBusStation(parseAnswer);
		SubMap._busStationArray.busStationsSizingDependingOnZoom();
	}
}

function preTreatmentBusStation(busStationFromDbList){
	var busStation;
	for (var h = 0; h < SubMap._busStationArray.length; h++) {
	
		//look for in the busStationFromDbList, the busStationFromDb which correspond:
		for (var n = 0; n < busStationFromDbList.length; n++){
			if(busStationFromDbList[n].id == SubMap._busStationArray[h].id){
				busStationFromDb = busStationFromDbList[n];
				break;
			}	
		}
	
		busStation = SubMap._busStationArray[h];
		
		busStation.layerId = busStationFromDb.layerId;
		
		busStation.id= busStationFromDb.id;
				
		busStation.layerId= busStationFromDb.layerId;
				
				
		if (busStation.type == 'boundary') {
			iconPath = "data/boundary.png";
			var iconStation = new gmap.MarkerImage(iconPath, null, null, null, new gmap.Size(4, 4));
			busStation.setIcon(iconStation);
			busStation.iconPath = "data/boundary.png";
		}
		//show circle of bus stations:
		var center;
		var radius;
		
		if (busStationFromDb.circleCenterLat != null) {
			center = new gmap.LatLng(busStationFromDb.circleCenterLat, busStationFromDb.circleCenterLng);
			radius = parseInt(busStationFromDb.circleRadius);
		}
		else {
			center = busStation.getPosition();
			radius = radiusInMetre;
		}
		busStation.circle = new gmap.Circle({
			map: map,
			clickable: true,
			draggable: true,
			fillColor: '#FFFF00',
			fillOpacity: 0.4,
			strokeColor: '#000000',
			strokeOpacity: 0.5,
			strokeWeight: 2,
			zIndex: 2,
			radius: radius,
			center: center
		});
		busStation.circle.busStation = busStation;
		
		if (typeof(map.newIdForBusStation) == 'undefined') {
			map.newIdForBusStation = 0;
		}
		
		if (parseInt(busStationFromDb.id) + 1 > map.newIdForBusStation) {
			map.newIdForBusStation = parseInt(busStationFromDb.id) + 1;
		}
		
	}
}
