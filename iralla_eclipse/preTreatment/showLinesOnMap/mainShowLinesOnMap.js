
function mainShowLinesOnMap(){
	
	var checkBoxList = document.getElementsByTagName('input');

	//get list to load:
	var listToLoad = [];
	for ( var i = 0; i < checkBoxList.length; i++){
		if ((checkBoxList[i].className == 'selectToShow' ) && (checkBoxList[i].checked == true)){
			listToLoad.push(checkBoxList[i].getAttribute("myid"));
		}
	}
	
	//send to the servor:
	request({
		phpFileCalled: mysite + 'preTreatment/showLinesOnMap/getLinesToShow.php',
		argumentsToPhpFile: 'q=' + JSON.stringify(listToLoad),
		type: "",
		callback: showLinesOnMap,
		asynchrone: true
	});
}

function showLinesOnMap(answer){
	
	var parseAnswer = JSON.parse(answer);

	//remove arrows:
	/*for(var i = 0; i < arrayOfBusBusLines.length; i++){
		if(arrayOfBusBusLines)
	}*/

	map.removeAllBusLines();

	if (typeof(parseAnswer.busLinesList) != "undefined")  {
		map.linesListFromBdd = parseAnswer.busLinesList;
		
		if (typeof(parseAnswer.arrowsList) != "undefined") {
			map.arrowsListFromBdd = parseAnswer.arrowsList;
			
			//parseAnswer.arrowsList[i].path change to an array:
			for (var i = 0; i < map.linesListFromBdd.length; i++) {
				/*var arrayOfStringPoints = map.linesListFromBdd[i].path.split(',');
				
				var path = [];
				var latAndLng;
				for (var j = 0; j < arrayOfStringPoints.length; j++) {
					latAndLng = arrayOfStringPoints[j].split(' ');
					path.push(latAndLng);
				}*/
				map.linesListFromBdd[i].path = JSON.parse(map.linesListFromBdd[i].path);
			}
		}
		map.addBusLinesFromDb(parseAnswer.busLinesList);
		preTreatmentBusLines(parseAnswer.busLinesList);
	}
}

function preTreatmentBusLines(DbList, busLine){
	
	for (var h = 0; h < SubMap._busStationArray.length; h++) {
	
		busLine = SubMap._busStationArray[h];
	
		busLine.id = DbList[h].id;
		busLine.layerId = DbList[h].layerId;
		busLine.inUse = DbList[h].inUse;
		
		busLine.setOptions({
			strokeColor: '#' + DbList[h].color
		});
		
		var i = 0;
		//load the boundaries to the bus line:
		if ((DbList[h].flows != null) && (DbList[h].flows != "")) {
			var flowsList = DbList[h].flows.split(' ');
			
			if ((DbList[h].boundariesListId != "") && (DbList[h].boundariesListId != null)) {
				var boundariesListId = DbList[h].boundariesListId.split(' ');
				var j = 0;
				var indexOf;
				
				if (boundariesListId.length > 0) {
					for (i = 0; i < boundariesListId.length; i++) {
						for (j = 0; j < SubMap._busStationArray.length; j++) {
							if (boundariesListId[i] == SubMap._busStationArray[j].id) {
								//busLine.showBoundary()
								busLine.addBoundary(SubMap._busStationArray[j].getPosition(), true, SubMap._busStationArray[j]);
								break;
							}
						}
					}

					var arrayOfBoundariesIndex = busLine.extractCorrespondingVertexListFromMarkerList(busLine.arrayOfBoundaries);
					
					//add the first vertex index and the last one to
					//arrayOfBoundariesIndex:
					arrayOfBoundariesIndex.unshift(0);
					arrayOfBoundariesIndex.push(busLine.getPath().getLength()-1);
					
					/*indexOf = {
						previousBoundary: 0,
						nextBoundary: arrayOfBoundariesIndex[0],
						sectionIndex: 0
					};
					busLine.addArrows(flowsList[i], indexOf);*/
					
					for (i = 0; i < flowsList.length; i++) {
						indexOf = {
							previousBoundary: arrayOfBoundariesIndex[i],
							nextBoundary: arrayOfBoundariesIndex[i + 1],
							sectionIndex: i
						};
						busLine.addArrows(flowsList[i], indexOf);
					}
				}
			}
			/******************************************************************
			 * 	TO SHOW ARROWS
			 * 
			 * 		next else removed to do not used too much memory
			 * 		can be used with a better computer
			 * 
			 * 
			 * 
			 ******************************************************************/
			/*else {
				busLine.arrayOfBoundaries = [];
				busLine.nbrOfBoundaries = 0;
				indexOf = {
					previousBoundary: 0,
					nextBoundary: busLine.getPath().getLength() - 1,
					sectionIndex: 0
				};
				busLine.addArrows(flowsList[0], indexOf);
			}*/
		
		}
		
		//load the area if exists
		if (DbList[h].pathsAreaOfBusStations != null) {
			if (busLine.type == 'mainLine') {
				var pathsString = JSON.parse(DbList[h].pathsAreaOfBusStations);
				var paths = [];
				for( var p = 0; p < pathsString.length; p++ ){
					var path = [];
					for (var j = 0; j < pathsString[p].length; j++) {
						if((typeof(pathsString[p][j].lat) != 'undefined') && (typeof(pathsString[p][j].lng) != 'undefined'))
						path.push(new gmap.LatLng(pathsString[p][j].lat, pathsString[p][j].lng));
					}
					paths.push(path);
				}
				
			
				busLine.areaSurrounded = new gmap.Polygon({
					map: busLine.map,
					strokeColor: '#00008b',
					strokeOpacity: 0.5,
					//fillColor: '#6495ed',
					fillColor: '#00008b',
					fillOpacity: 0.2,
					strokeWeight: 1,
					zIndex: 1,
					paths: paths
				});
				if (typeof(troncalAreas) == 'undefined') {
					troncalAreas = [];
				}
				troncalAreas.push(busLine.areaSurrounded);
			}
		}
		
		//load the limits of the areas:
		if ((DbList[h].areaOnlyBusStations != null) && (DbList[h].areaOnlyBusStations != "null") && (DbList[h].areaOnlyBusStations != "")) {
			var buffer = JSON.parse(DbList[h].areaOnlyBusStations);
			busLine.vertexInsideMainLineArea = [];
			for (i = 0; i < buffer.length; i++) {
				busLine.vertexInsideMainLineArea.push({
					enter: new gmap.LatLng(parseFloat(buffer[i].enter.lat), parseFloat(buffer[i].enter.lng)),
					out: new gmap.LatLng(parseFloat(buffer[i].out.lat), parseFloat(buffer[i].out.lng))
				});
				new gmap.Circle({
					map: map,
					clickable: false,
					draggable: false,
					fillColor: '#00FF00',
					fillOpacity: 0.4,
					strokeColor: '#00FF00',
					strokeOpacity: 0.8,
					strokeWeight: 2,
					zIndex: 10,
					radius: 20,
					center: new gmap.LatLng(parseFloat(buffer[i].enter.lat), parseFloat(buffer[i].enter.lng))
				});
				new gmap.Circle({
					map: map,
					clickable: false,
					draggable: false,
					fillColor: '#FF0000',
					fillOpacity: 0.4,
					strokeColor: '#FF0000',
					strokeOpacity: 0.8,
					strokeWeight: 2,
					zIndex: 10,
					radius: 20,
					center: new gmap.LatLng(parseFloat(buffer[i].out.lat), parseFloat(buffer[i].out.lng))
				});
			}
		}
		
		//remove the hightlighting and associate events:
		gmap.event.removeListener(busLine.listenerMouseOver);
		gmap.event.removeListener(busLine.listenerMouseOut);
		
	}
	
}

function convertPathStringToArray(pathString){
	//DbList[i].path change to an array:
	var arrayOfStringPoints = pathString.split(',');
	var latAndLng;
	var path = [];
	for (i = 0; i < arrayOfStringPoints.length; i++) {
		latAndLng = arrayOfStringPoints[i].split(' ');
		path.push(new gmap.LatLng(parseFloat(latAndLng[0]), parseFloat(latAndLng[1])));
	}
	return path;
}

