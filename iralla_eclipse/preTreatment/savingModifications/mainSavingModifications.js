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
	
	if ((SubMap._busStationArray[0] != '') && (typeof(which) == 'undefined')){
		for (i = 0; i < SubMap._busStationArray.length; i++) {
			
			//INIT
			pathString = null;
			flows = null;
			boundariesListId = null;
			pathsAreaStrings = null;
			areaOnlyBusStations = null;
		
			//extract boundaries id of flow if exist:
			boundariesListId = null;
			if ((typeof(SubMap._busStationArray[i].arrayOfBoundaries) != 'undefined') &&
			(SubMap._busStationArray[i].arrayOfBoundaries.length > 0)) {
				for (j = 0; j < SubMap._busStationArray[i].arrayOfBoundaries.length; j++) {
					boundaryId = SubMap._busStationArray[i].arrayOfBoundaries[j].id;
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
			if (typeof(SubMap._busStationArray[i].sections) != 'undefined') {
				for (j = 0; j < SubMap._busStationArray[i].sections.length; j++) {
					flows += ' ' + SubMap._busStationArray[i].sections[j].flowOrder;
				}
				flows = flows.removeFirstLetter();
			}
			
			//extract the path:
			pathString = JSON.stringify(pathToArray(SubMap._busStationArray[i].getPath()));
			
			//if area around bus line, extract the path:
			pathsAreaStrings = null;
			if (typeof(SubMap._busStationArray[i].areaSurrounded) != 'undefined') {
				pathsArea = SubMap._busStationArray[i].areaSurrounded.getPaths();
				pathsAreaStrings = [];
				var length1 = pathsArea.getLength();
				for (j = 0; j < length1; j++) {
					pathArea = pathsArea.getAt(j);
					pathsAreaStrings.push(pathToArray(pathArea));
				}
				pathsAreaStrings = JSON.stringify(pathsAreaStrings);
			}
			
			//extract the vertex inside area:
			if ( (typeof(SubMap._busStationArray[i].vertexInsideMainLineArea) != 'undefined')
			 && (SubMap._busStationArray[i].vertexInsideMainLineArea.length > 0)){
			 	var areaOnlyBusStations = [];
				var enter;
				var out;
				for( j = 0; j < SubMap._busStationArray[i].vertexInsideMainLineArea.length; j++){
					 enter = SubMap._busStationArray[i].vertexInsideMainLineArea[j].enter;
					 out = SubMap._busStationArray[i].vertexInsideMainLineArea[j].out;
					
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
				id: SubMap._busStationArray[i].id,
				name: SubMap._busStationArray[i].name,
				type: SubMap._busStationArray[i].type,
				color: SubMap._busStationArray[i].strokeColor.removeFirstLetter(),
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
	
	for( i = 0; i < SubMap._busStationArray.length; i++) {
		if (((typeof(which) == 'undefined') && (SubMap._busStationArray[i].type != 'virtual')) ||
		((which == 'virtual') && (SubMap._busStationArray[i].type == 'virtual'))) {
			//busStations
			busStationDatas = {
				id: SubMap._busStationArray[i].id,
				layerId: SubMap._busStationArray[i].layerId,
				name: SubMap._busStationArray[i].name,
				lat: SubMap._busStationArray[i].getPosition().lat(),
				lng: SubMap._busStationArray[i].getPosition().lng(),
				circleCenterLat: SubMap._busStationArray[i].circle.getCenter().lat(),
				circleCenterLng: SubMap._busStationArray[i].circle.getCenter().lng(),
				circleRadius: SubMap._busStationArray[i].circle.getRadius(),
				idFromJavascript: SubMap._busStationArray[i].javascriptId,
				type: SubMap._busStationArray[i].type
			};
			allBusStationsDatas.push(busStationDatas);
			
			//links:
			if (typeof(SubMap._busStationArray[i].arrayOfConnectionsMarkers) != 'undefined') {
				for (j = 0; j < SubMap._busStationArray[i].arrayOfConnectionsMarkers.length; j++) {
					
					link = SubMap._busStationArray[i].arrayOfConnectionsMarkers[j];
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

