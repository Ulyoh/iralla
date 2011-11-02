function mainSavingModifications(which){

	var dataToSend;
	
	//datas of bus lines:
	var allBusLinesDatas = [];
	var busLineDatas;
	var boundaries;
	var boundaryCenter;
	var path;
	var pathsArea;
	var j = 0;
	var i = 0;
	var k = 0;
	
	if ((arrayOfBusLines[0] != '') && (typeof(which) == 'undefined')){
		for (i = 0; i < arrayOfBusLines.length; i++) {
			
			//INIT
			pathString = null;
			flows = null;
			boundariesListId = null;
			pathsAreaStrings = null;
			areaOnlyBusStations = null;
		
			//extract boundaries id of flow if exist:
			boundariesListId = null;
			if ((typeof(arrayOfBusLines[i].arrayOfBoundaries) != 'undefined') &&
			(arrayOfBusLines[i].arrayOfBoundaries.length > 0)) {
				for (j = 0; j < arrayOfBusLines[i].arrayOfBoundaries.length; j++) {
					boundaryId = arrayOfBusLines[i].arrayOfBoundaries[j].id;
					if(boundariesListId == null){
						boundariesListId = boundaryId;
					}
					else{
						boundariesListId += ' ' + boundaryId;
					}
				}
			}
			
			//extract flows order if exist:
			var flows = "";
			if (typeof(arrayOfBusLines[i].sections) != 'undefined') {
				for (j = 0; j < arrayOfBusLines[i].sections.length; j++) {
					flows += ' ' + arrayOfBusLines[i].sections[j].flowOrder;
				}
				flows = flows.removeFirstLetter();
			}
			
			//extract the path:
			pathString = JSON.stringify(pathToArray(arrayOfBusLines[i].getPath()));
			
			//if area around bus line, extract the path:
			pathsAreaStrings = null;
			if (typeof(arrayOfBusLines[i].areaSurrounded) != 'undefined') {
				pathsArea = arrayOfBusLines[i].areaSurrounded.getPaths();
				pathsAreaStrings = [];
				var length1 = pathsArea.getLength();
				for (j = 0; j < length1; j++) {
					pathArea = pathsArea.getAt(j);
					pathsAreaStrings.push(pathToArray(pathArea));
				}
				pathsAreaStrings = JSON.stringify(pathsAreaStrings);
			}
			
			//extract the vertex inside area:
			if ( (typeof(arrayOfBusLines[i].vertexInsideMainLineArea) != 'undefined')
			 && (arrayOfBusLines[i].vertexInsideMainLineArea.length > 0)){
			 	var areaOnlyBusStations = [];
				var enter;
				var out;
				for( j = 0; j < arrayOfBusLines[i].vertexInsideMainLineArea.length; j++){
					 enter = arrayOfBusLines[i].vertexInsideMainLineArea[j].enter;
					 out = arrayOfBusLines[i].vertexInsideMainLineArea[j].out;
					
					areaOnlyBusStations.push({
						enter:{
							lat: enter.lat(),
							lng: enter.lng()
						},
						out:{
							lat: out.lat(),
							lng: out.lng()
						}
					});
				}
			}
			
			busLineDatas = {
				id: arrayOfBusLines[i].id,
				name: arrayOfBusLines[i].name,
				type: arrayOfBusLines[i].type,
				color: arrayOfBusLines[i].strokeColor.removeFirstLetter(),
				path: pathString,
				flows: flows,
				boundariesListId: boundariesListId,
				pathsAreaOfBusStations: pathsAreaStrings,
				areaOnlyBusStations: JSON.stringify(areaOnlyBusStations)
			//busStationsIdsList:
			//connectionsIdsList:
			//inUse:		
			};
			
			allBusLinesDatas.push(busLineDatas);
		}
	}

//datas of bus stations and links:
	var allBusStationsDatas = [];
	var busStationDatas;
	var allLinksDatas = [];
	var linkDatas;
	var link;
	var position;
	var nearVertex;
	var busLineId;
	var linkProyection;
	var distanceToPrevIndex;
	var path;
	var lastIndex;
	var linkToTest;
	//var canBeFirstLink;
	
	for( i = 0; i < arrayOfBusStations.length; i++) {
		if (((typeof(which) == 'undefined') && (arrayOfBusStations[i].type != 'virtual')) ||
		((which == 'virtual') && (arrayOfBusStations[i].type == 'virtual'))) {
			//busStations
			busStationDatas = {
				id: arrayOfBusStations[i].id,
				layerId: arrayOfBusStations[i].layerId,
				name: arrayOfBusStations[i].name,
				lat: arrayOfBusStations[i].getPosition().lat(),
				lng: arrayOfBusStations[i].getPosition().lng(),
				circleCenterLat: arrayOfBusStations[i].circle.getCenter().lat(),
				circleCenterLng: arrayOfBusStations[i].circle.getCenter().lng(),
				circleRadius: arrayOfBusStations[i].circle.getRadius(),
				idFromJavascript: arrayOfBusStations[i].javascriptId,
				type: arrayOfBusStations[i].type
			};
			allBusStationsDatas.push(busStationDatas);
			
			//links:
			if (typeof(arrayOfBusStations[i].arrayOfConnectionsMarkers) != 'undefined') {
				for (j = 0; j < arrayOfBusStations[i].arrayOfConnectionsMarkers.length; j++) {
					
					link = arrayOfBusStations[i].arrayOfConnectionsMarkers[j];
					position = link.getPosition();
					linkProyection = link.busLine.findNearestProyectionOrthogonal(Point.latLngToPoint(position));

					path = link.busLine.getPath();
					lastIndex = path.length - 1;

					if (linkProyection.type == 'vertex') {
						distanceToPrevIndex = 0;
					}
					else {
						distanceToPrevIndex = google.maps.geometry.spherical.computeDistanceBetween(path.getAt(linkProyection.index), linkProyection.coord, 6378137);
					//distanceToPrevIndex = Point.latLngToPoint(path.getAt(linkProyection.index)).distanceOf(Point.latLngToPoint(linkProyection.coord));
					}
					
					if ((typeof(link.busStation.id) != 'undefined') || (link.busStation.id >= 0)) {
						busStationId = link.busStation.id;
					}
					else {
						error("id not define");
					//busStationId = link.busStation.javascriptId;
					}
					
					//if it s the second link to have the last index at the same position
					//on the same bus line
					if((linkProyection.index == lastIndex) 
					&& (distanceToPrevIndex == 0)){
						for(k = 0; k < allLinksDatas.length; k++){
							linkToTest = allLinksDatas[k];
							if((link.busLine.id == linkToTest.busLineId)
							&& (linkToTest.prevIndex == lastIndex )
							&& (linkToTest.distanceToPrevIndex == 0 )){
								//set the link to the first index:
								linkToTest.prevIndex = 0;
								break;
							}
						}
					}
					
					linkDatas = {
						busStationId: busStationId,
						busLineId: link.busLine.id,
						prevIndex: linkProyection.index,
						distanceToPrevIndex: distanceToPrevIndex,
						lat: position.lat(),
						lng: position.lng()
					};
					allLinksDatas.push(linkDatas);
				}
				
				
				
				
				
			}
		}
	}

	dataToSend = {
		which: which,
		busLinesList: allBusLinesDatas,
		buStationsList: allBusStationsDatas,
		busLinksList: allLinksDatas
	};
	
	dataToSend = JSON.stringify(dataToSend);

	request({
		phpFileCalled: mysite + 'preTreatment/savingModifications/saveAllModifications.php',
		argumentsToPhpFile: 'q=' + dataToSend,
		callback: showResultInInfosPreBox
	});

}

function pathToArray(path){
	var length = path.getLength();
	var latLng;
	var pathArray = [];
	var lat;
	var lng;
	
	for (var i= 0; i < length; i++) {
		latLng = path.getAt(i);
		
		pathArray.push({
			lat:round(latLng.lat(), 8),
			lng: round(latLng.lng(), 8)
		});
	}
	return pathArray;
}

if (typeof(loaded.savingModifications) == 'undefined') {
	loaded.savingModifications = [];
}

loaded.savingModifications.push("mainSavingModifications.js");

