/**
 * @author Yoh
 */
//args = {state: 'string', index: x}
function makeVirtualsBusStationOldVersion(){
	//what is the first id of the bus stations saved on the server:
	var argsFromServor = JSON.stringify('follow');
	makeVirtualsBusStations(argsFromServor);
}

function makeVirtualBusStationFrom(){
	var argsFromServor = JSON.stringify('follow');
	var args ={
		index: parseInt(document.getElementById('input_index').value),
		i: parseInt(document.getElementById('input_i').value)
	};
	makeVirtualsBusStations(argsFromServor, args);
}


function makeVirtualsBusStations(argsFromServor, args){
	var radius = 0.001;//0.00015
	var step = radius/5;
	var state;
	var index;
	var nextConnectionId;
	var orderedListOfVirtualBusStationsId = [];
	var listOfVirtualBusStopReplaced = [];   ///to send to the servor!!!!!!!!!!!!!!!!!!!
	var virtualBusStationListAlreadySaved = [];
	var i = 0;

	if (typeof(argsFromServor) != 'undefined'){
		//argsFromServor = eval(argsFromServor);
		argsFromServor = JSON.parse(argsFromServor);
		
		if (argsFromServor != 'follow'){
			return;
		}
		
		//var virtualBusStationId =0;
		
		if (typeof(argsFromServor.virtualBusStationListAlreadySaved) != 'undefined') {
			virtualBusStationListAlreadySaved = argsFromServor.virtualBusStationListAlreadySaved;
		}
		
		if ((typeof(args) == 'undefined') || (typeof(args.state) == 'undefined')) {
			state = 'step 1';
		}
		else {
			state = args.state;
		}
		if ((typeof(args) == 'undefined') || (typeof(args.index) == 'undefined')) {
			//DEBUG
			index = 0; // 0
		}
		else {
			
			index = args.index;
			
		}
		if ((typeof(args) == 'undefined') || (typeof(args.nextConnectionId) == 'undefined')) {
			nextConnectionId = 0;
		}
		else {
			nextConnectionId = args.nextConnectionId;
		}
		
		if (index >= arrayOfBusLines.length) {
			return;
		}
		
		while ((arrayOfBusLines[index].type != 'feeder') && (arrayOfBusLines[index].type != 'other')) {
			index++;
		}
		
		//for each ruta/alimentadora (made by the function calling itself (mainMakeVirtualsBusStation) and pass the next index as argument)
		//if index < nbr de buslines and budlines is a feeder or a other busline and step 1
		
		
		//END DEBUG
		arrayOfBusLines[index].setOptions({strokeColor: '#FF0000',  strokeOpacity: 1});
		
		if ((index < arrayOfBusLines.length) && ((arrayOfBusLines[index].type == 'feeder') || (arrayOfBusLines[index].type == 'other')) && (state == 'step 1')) {
			
			//INIT
			//list of virtual bus lines:
			var virtualBusStopList = [];
			
			//list of bus links:
			var listOfBusLinks = [];
			
			//previous list of bus links:
			var previousListOfBusLinks = [];

			//path of the ruta/alimentadora considerated:
			var path = arrayOfBusLines[index].getPath();

			//list of circles to show busStation created:
			if (typeof(arrayOfBusLines.busStationCircle) == 'undefined'){
				arrayOfBusLines.busStationCircle = [];
			}
			else{
				for (i = 0; i < arrayOfBusLines.busStationCircle.length; i++ ){
					arrayOfBusLines.busStationCircle[i].setMap(null);
					arrayOfBusLines.lastIndexOfBusStationCircle = -1;
				}
			}

			//END INIT
			
			//for the first vertex :
			listOfBusLinks = findBusLinksAroundThisPoint({
				//nextConnectionId: nextConnectionId,
				indexOfPreviousVertex: 0,
				distanceToThePreviousVertex: 0,
				point: Point.latLngToPoint(path.getAt(0)),
				path: path,
				radius: radius,
				exception: [index],
				mode: 'findBusLinksAroundThisPoint'
			});
			
			//saving a virtual bus station:
			orderedListOfVirtualBusStationsId.push(map.newIdForBusStation);
			virtualBusStopList.push({
				id: map.newIdForBusStation++,
				coord: Point.latLngToPoint(path.getAt(0)),
				linksList: listOfBusLinks
			});

			previousListOfBusLinks = listOfBusLinks;
	
			//verification that i+1 different of i = 0
			if ((typeof(args) != 'undefined') && (typeof(args.i) != 'undefined')){
				i = args.i;
			}
			else{
				i = 0;
			}
			while ((path.getAt(i) == path.getAt(i + 1)) && ( i < path.getLength() - 1)) {
				i++;
			}

			//DEBUG
			//i = 200;
			
			arrayOfBusLines.argumentsScan = new Object();
			arrayOfBusLines.argumentsScan = {
				searchOfBusStationCenter: false,
				i: i,
				path: path,
				step: step,
				radius: radius,
				index: index,
				previousListOfBusLinks: previousListOfBusLinks,
				virtualBusStopList: virtualBusStopList,
				virtualBusStationListAlreadySaved: virtualBusStationListAlreadySaved,
				listOfVirtualBusStopReplaced: listOfVirtualBusStopReplaced,
				//virtualBusStationId: map.newIdForBusStation,
				orderedListOfVirtualBusStationsId: orderedListOfVirtualBusStationsId,
				nextConnectionId:nextConnectionId,
				lastBusStationPosition: {
						i: 0,
						distanceFromI: 0
					}
			};
			//for each segment of the ruta/alimentadora:
			scanSegmentRecursif(arrayOfBusLines.argumentsScan);
		}
	}
}

function scanSegmentRecursif(args){

	var i = args.i;
	var path = args.path;
	var step = args.step;
	var radius = args.radius;
	var index = args.index;
	var previousListOfBusLinks= args.previousListOfBusLinks;
	var virtualBusStopList= args.virtualBusStopList;
	var virtualBusStationListAlreadySaved= args.virtualBusStationListAlreadySaved;
	var listOfVirtualBusStopReplaced= args.listOfVirtualBusStopReplaced;
	//var virtualBusStationId= args.virtualBusStationId;
	var orderedListOfVirtualBusStationsId= args.orderedListOfVirtualBusStationsId;
	var nextConnectionId = args.nextConnectionId;
	if (typeof(args.previousIdenticalMore) != 'undefined'){
		var previousIdenticalMore = args.previousIdenticalMore;
	}
	else{
		var previousIdenticalMore = false;
	}
	

	var searchOfBusStationCenter = args.searchOfBusStationCenter;
	var iBeginningSearchOfBusStationCenter = args.iBeginningSearchOfBusStationCenter;
	var initialDistanceFromIBeginningSearchOfBusStationCenter = args.initialDistanceFromIBeginningSearchOfBusStationCenter;
	var previousDistanceInterLink = args.previousDistanceInterLink;

	var lastBusStationPosition = args.lastBusStationPosition;

	var distanceFromFirstPosition;
	var diffBetweenCurrentAndPreviousDistanceInterLink;
	var currentDistanceInterLink;
	var pointToTest;
	var listOfBusLinks;
	var denominator;

	var distanceFromLastBusStation = Infinity;
	var distanceMiniBetween2BusStations	= 4 * radius;
	var distanceFromFirstPositionMax = 4 * radius;
		
	if(distanceFromFirstPositionMax > distanceMiniBetween2BusStations){
		alert('error distanceFromFirstPositionMax > distanceMiniBetween2BusStations');
		exit;
	}

	var symetricBoundariesOfBusLine;
	var centerOfBusStation;

	var currentBusLine = arrayOfBusLines[index];
	var identical;
	//show index on the map:
	//showCircles(path.getAt(i));

	//except the ones in the area around a troncal:
	if ((typeof(currentBusLine.vertexNearTroncal) != 'undefined') &&
		(currentBusLine.vertexNearTroncal[i] === true) &&
		(currentBusLine.vertexNearTroncal[i + 1] === true))
	{
		while((typeof(currentBusLine.vertexNearTroncal[i + 1]) != 'undefined') && (typeof(currentBusLine.vertexNearTroncal) != 'undefined') &&
		(currentBusLine.vertexNearTroncal[i] === true) &&
		(currentBusLine.vertexNearTroncal[i + 1] === true)){
			i++;
		}

		if (i < path.getLength){
			arrayOfBusLines.argumentsScan = {
				i: i,
				path: path,
				step: step,
				radius: radius,
				index: index,
				previousListOfBusLinks: previousListOfBusLinks,
				virtualBusStopList: virtualBusStopList,
				virtualBusStationListAlreadySaved: virtualBusStationListAlreadySaved,
				listOfVirtualBusStopReplaced: listOfVirtualBusStopReplaced,
				//virtualBusStationId: virtualBusStationId,
				orderedListOfVirtualBusStationsId: orderedListOfVirtualBusStationsId,
				nextConnectionId:nextConnectionId
			};
			scanSegmentRecursif(arrayOfBusLines.argumentsScan);
		}
		else{
			return;
		}
	}
	//scan all the bus lines every step
	//for each segment of the ruta/alimentadora
	else if ( i < path.getLength() - 1) {
		//vector for the steps:
		var stepVector = new Vector(Point.latLngToPoint(path.getAt(i)), Point.latLngToPoint(path.getAt(i + 1)));
		var nbrOfSteps = stepVector.magnitude() / step;
		stepVector = stepVector.setMagnitude(step);
		

		pointToTest = Point.latLngToPoint(path.getAt(i));

		//for each step :
		for (var j = 0; j < nbrOfSteps; j++) {

			//found the bus links around the point:
			listOfBusLinks = findBusLinksAroundThisPoint({
				//nextConnectionId: nextConnectionId,
				indexOfPreviousVertex: i,
				distanceToThePreviousVertex: j * step,
				point: pointToTest,
				path: path,
				radius: radius,
				exception: [index] //TO MODIFIED
			});

			//is the listOfBusLinks and previousListOfBusLinks are not identical:
			identical = areTheyIdenticals2(previousListOfBusLinks, listOfBusLinks);

			//if the previous list of bus link have one link more and the current list do not have one more
			if ((searchOfBusStationCenter === false ) &&(previousIdenticalMore === true) && (identical.more === false)){
				searchOfBusStationCenter = true;
				iBeginningSearchOfBusStationCenter = i;
				initialDistanceFromIBeginningSearchOfBusStationCenter = j * step;
				//symetricBoundariesOfBusLine = currentBusLine.extractBoundariesAroundThisPoint(i, j * step, 3 * radius);
				previousDistanceInterLink = calculSumDistanceInterLink(previousListOfBusLinks, currentBusLine, symetricBoundariesOfBusLine);
			}
			
			if (searchOfBusStationCenter === true) {
				if (identical.more === false){
					symetricBoundariesOfBusLine = currentBusLine.extractBoundariesAroundThisPoint(i, j * step, 3 * radius);
					currentDistanceInterLink = calculSumDistanceInterLink(listOfBusLinks, currentBusLine, symetricBoundariesOfBusLine);
					diffBetweenCurrentAndPreviousDistanceInterLink = previousDistanceInterLink - currentDistanceInterLink;
					distanceFromFirstPosition = currentBusLine.distanceBetweenTwoVertex(iBeginningSearchOfBusStationCenter, i) - initialDistanceFromIBeginningSearchOfBusStationCenter + j * step;
					previousDistanceInterLink = currentDistanceInterLink;
					denominator = listOfBusLinks.length - 1;
				}
				else{
					symetricBoundariesOfBusLine = currentBusLine.extractBoundariesAroundThisPoint(i, j * step, 3 * radius);
					distanceFromFirstPosition = currentBusLine.distanceBetweenTwoVertex(iBeginningSearchOfBusStationCenter, i) - initialDistanceFromIBeginningSearchOfBusStationCenter + j * step;
					previousDistanceInterLink = calculSumDistanceInterLink(listOfBusLinks, currentBusLine,symetricBoundariesOfBusLine);
				}
			}
			
			if (identical.more === true){
				previousIdenticalMore = true;
			}
			else{
				previousIdenticalMore = false;
			}

			if(identical.less === true){
				distanceFromLastBusStation = currentBusLine.distanceBetweenTwoVertex(lastBusStationPosition.i, i) - lastBusStationPosition.distanceFromI + j *step;
			}

			//if at least one link less in the new listOfBusLink or the last list had one more link
			if (((identical.less === true) && (distanceFromLastBusStation > distanceMiniBetween2BusStations)) ||
			((searchOfBusStationCenter == true) && 
				( diffBetweenCurrentAndPreviousDistanceInterLink / denominator < 0/*(step / 15)*/ ) || 
				( distanceFromFirstPosition > distanceFromFirstPositionMax ) ||
				(identical.less == true)
			)){
				//init:
					centerOfBusStation = previousListOfBusLinks[previousListOfBusLinks.length - 1].coord;
					//saving the listOfBusLinksAroundBarycenter in the virtualbustoplist:
					orderedListOfVirtualBusStationsId.push(map.newIdForBusStation);
					virtualBusStopList.push({
						id: map.newIdForBusStation++,
						coord: centerOfBusStation,
						linksList: previousListOfBusLinks
					});

					//showOnePolygonCircle(centerOfBusStation.convertToLatLng(), radius, '#550000');
					lastBusStationPosition = {
						i: i,
						distanceFromI: j*step
					}

		/*			symetricBoundariesOfBusLine = currentBusLine.extractBoundariesAroundThisPoint(i, j * step, 3 * radius);
					//determinate the list of others bus lines just after the virtual bus station:
					//move forward of 2 * radius from the center of the virtual bus station:
					var result = currentBusLine.moveOn(2 * radius, symetricBoundariesOfBusLine, centerOfBusStation.convertToLatLng());

					//reinitialisation:
					i = result.index;

					if ( i < path.getLength() - 1){
						pointToTest = result.coord;
						stepVector = (new Vector(Point.latLngToPoint(path.getAt(i)), Point.latLngToPoint(path.getAt(i + 1))));
						nbrOfSteps = stepVector.magnitude() / step;
						stepVector = stepVector.setMagnitude(step);

						if (result.distanceFromI % step !== 0) {
							j = Math.floor(result.distanceFromI / step);
						}
						else {
							j = (result.distanceFromI / step) - 1;
						}

						previousListOfBusLinks = findBusLinksAroundThisPoint({
							//nextConnectionId: nextConnectionId,
							indexOfPreviousVertex: i,
							distanceToThePreviousVertex: result.distanceFromI,
							point: pointToTest,
							path: path,
							radius: radius,
							exception: [index]
							});
					}*/
					searchOfBusStationCenter = false;
					distanceFromFirstPosition = 0;
				}
				else {
					//previousListOfBusLinks = listOfBusLinks;
					pointToTest = pointToTest.addVector(stepVector);
				}
				previousListOfBusLinks = listOfBusLinks;
			}

			if (i < path.getLength() - 1){
				i++;
			}
			while ( (i < path.getLength() - 1) && (path.getAt(i) == path.getAt(i + 1)) ) {
				i++;
			}
			arrayOfBusLines.argumentsScan = {
				searchOfBusStationCenter:searchOfBusStationCenter,
				iBeginningSearchOfBusStationCenter:iBeginningSearchOfBusStationCenter,
				initialDistanceFromIBeginningSearchOfBusStationCenter:initialDistanceFromIBeginningSearchOfBusStationCenter,
				previousDistanceInterLink:previousDistanceInterLink,
				i: i,
				path: path,
				step: step,
				radius: radius,
				index: index,
				previousListOfBusLinks: previousListOfBusLinks,
				virtualBusStopList: virtualBusStopList,
				virtualBusStationListAlreadySaved: virtualBusStationListAlreadySaved,
				listOfVirtualBusStopReplaced: listOfVirtualBusStopReplaced,
				//virtualBusStationId: virtualBusStationId,
				orderedListOfVirtualBusStationsId: orderedListOfVirtualBusStationsId,
				nextConnectionId:nextConnectionId,
				previousIdenticalMore: previousIdenticalMore,
				lastBusStationPosition: lastBusStationPosition
			};
			showState();
			setTimeout(function(){scanSegmentRecursif(arrayOfBusLines.argumentsScan)},100);
		}
		else{

		//for the last vertex :
		listOfBusLinks = findBusLinksAroundThisPoint({
			//nextConnectionId: nextConnectionId,
			indexOfPreviousVertex: i,
			distanceToThePreviousVertex: 0,
			point: Point.latLngToPoint(path.getAt(i)),
			path: path,
			radius: radius,
			exception: [index]
		});

		//saving a virtual bus station:
		orderedListOfVirtualBusStationsId.push(map.newIdForBusStation);
		virtualBusStopList.push({
			id: map.newIdForBusStation++,
			coord: Point.latLngToPoint(path.getAt(i)),
			linksList: listOfBusLinks
		});

		//replace the ids which are in orderedListOfVirtualBusStationsId;
		for (i = 0; i < orderedListOfVirtualBusStationsId.length; i++) {
			for (j = 0; j < listOfVirtualBusStopReplaced.length; j++) {
				if (listOfVirtualBusStopReplaced[j].id == orderedListOfVirtualBusStationsId[i]) {
					orderedListOfVirtualBusStationsId[i] = listOfVirtualBusStopReplaced[j].newId;
				}
			}
		}
		//add ids to the links:
		for( i = 0; i < virtualBusStopList.length; i++){
			for( j = 0; j < virtualBusStopList[i].linksList.length; j++){
				virtualBusStopList[i].linksList[j].id = nextConnectionId++;
			}
		}

		var parametersToSend = {
			busLineId: parseInt(currentBusLine.id),
			orderedListOfVirtualBusStationsId: orderedListOfVirtualBusStationsId,
			virtualBusStopList: virtualBusStopList
		};
		parametersToSend = JSON.stringify(parametersToSend);
		parametersToSend = 'q=' + parametersToSend;

		currentBusLine.setOptions({strokeColor: '#000000',  strokeOpacity: 1});

		var argumentsCallback = {
			state: 'step 1',
			index: ++index,
			nextConnectionId: nextConnectionId
		};

		document.getElementById('direction').innerHTML = index + "/" + arrayOfBusLines.length + " done.";
		showState();
		//removed to debug
		request({phpFileCalled: mysite + 'preTreatment/makeVirtualsBusStation/saveVirtualsBusStations.php',
			argumentsToPhpFile: parametersToSend,
			type: '',
			callback: makeVirtualsBusStations,
			argumentsCallback: argumentsCallback,
			asynchrone: true
		});
	}
}
/*
 * return a list of connection around a point given
 * in the list is include the point as a connection
 * to the bus line tested
 */
function findBusLinksAroundThisPoint(args){
	//var connectionId = args.nextConnectionId;
	var index = args.indexOfBusLine; //index of BusLine tested
	var path = args.path;			//path of the Busline tested
	var exception = args.exception;	//exception where to do not look for
	var mode = args.mode;			//mode 'makeVirtualsBusStation' or 'troncalesAndAlimentadoras'
	
	var isIndexInArea;
	
	//FIND THE INTERSECTION BETWEEN A CIRCLE AROUND THE VERTEX AND ALL THE OTHER RUTAS/ALIMENTADORAS:
	//INIT
	///////////////var circle = new Circle(args.point, radius);
	var circle = args.circle;
	var center = circle.getCenter();
	var radius = circle.getRadius();
	//to store the results:
	var intersectionsList = [];
	//result to send:
	var listOfConnections = [];
	
	//END INIT
	var segmentToCompare;
	
	//foreach other ruta/alimentadora:
	label_1: for (var i = 0; i < arrayOfBusLines.length; i++) {
		//if the index of the considerated ruta/alimentadora is in the exception list:
		for (var g = 0; g < exception.length; g++) {
			if (i == exception[g]) {
				//do not make the  test
				continue label_1;
			}
		}
		
		//path of the busline to compare with the bus station tested:
		var pathToCompare = arrayOfBusLines[i].getPath();
		
		var j = 0;
		
		var lengthOfPathToCompare = pathToCompare.getLength();
		//up to which vertex of the busline to compare, have to be done the looking for of
		//a segment near the point of the busline tested:
		var end = lengthOfPathToCompare - 1;
		
		//test if the first point is inside the circle:
		if (google.maps.geometry.spherical.computeDistanceBetween(center, pathToCompare.getAt(0)) <= circle.radius) {
			intersectionsList.push({
				//connectionId: connectionId++,
				busLineIndex: i,
				busLineId: parseInt(arrayOfBusLines[i].id),
				busLineName: arrayOfBusLines[i].lineName,
				indexOfPreviousVertex: 0,
				distanceToThePreviousVertex: 0,
				coord: Point.latLngToPoint(pathToCompare.getAt(0))
			});
			//find the first vertex outside the circle:
			do {
				j++;
			}
			while (google.maps.geometry.spherical.computeDistanceBetween(center, pathToCompare.getAt(j)) <= radius);
		}
		//test if the last point is inside the circle:
		if (google.maps.geometry.spherical.computeDistanceBetween(center, pathToCompare.getAt(lengthOfPathToCompare - 1)) <= radius) {
			// (the vertex is include in the list after the research on each segment (after the loop "while( j < end)" )
			//find the last vertex outside the circle:
			do {
				end--;
			}
			while (google.maps.geometry.spherical.computeDistanceBetween(center, pathToCompare.getAt(end)) <= radius);
		}
		
		//for each segment:
		while (j < end) {
			//case of a feeder:
			if (arrayOfBusLines[i].type == 'feeder') {
				
				//if there are vertex inside main line area:
				isIndexInArea = arrayOfBusLines[i].isIndexInsideMainLineArea(j);
				
				if ((preTreatment.current == 'makeVirtualsBusStation') && (isIndexInArea.isInside == true))  {
					j = isIndexInArea.nextBoundary;
					
					//if j >= end break the loop, the vertex is out of the circle
					if (j >= end){
						break;
					}
				}	
			}
			/*
			if ((typeof(args.mode)) && (args.mode == 'makeVirtualsBusStation')) {
				//in case of an alimentadora, if the vertex considerated is inside the area of the troncales:
				if ((typeof(arrayOfBusLines[i].vertexNearTroncal) != 'undefined') &&
				(arrayOfBusLines[j].vertexNearTroncal[j] === true) &&
				(arrayOfBusLines[j].vertexNearTroncal[j + 1] === true)) {
					//go to the next vertex
					continue;
				}
			}*/
			segmentToCompare = new Segment(Point.latLngToPoint(pathToCompare.getAt(j)), Point.latLngToPoint(pathToCompare.getAt(j + 1)));
			var intersection = circle.intersectWith(segmentToCompare);
			
			//if the segment tested is at least apart inside the circle:
			if (intersection !== false) {
				var nearestIntersection;
				var shortestDistance = Infinity;
				var distance;
				do {
					if (intersection == 'pt1 of segment') {
						distance = google.maps.geometry.spherical.computeDistanceBetween(center, pathToCompare.getAt(j));
						if (distance < shortestDistance) {
							nearestIntersection = {
								busLineIndex: i,
								busLineId: parseInt(arrayOfBusLines[i].id),
								busLineName: arrayOfBusLines[i].lineName,
								indexOfPreviousVertex: j,
								distanceToThePreviousVertex: 0,
								coord: Point.latLngToPoint(pathToCompare.getAt(j))
							};
							shortestDistance = distance;
						}
					}
					else if (intersection == 'pt2 of segment') {
						distance = google.maps.geometry.spherical.computeDistanceBetween(center,pathToCompare.getAt(j + 1));
						if (distance < shortestDistance) {
							nearestIntersection = {
								busLineIndex: i,
								busLineId: parseInt(arrayOfBusLines[i].id),
								busLineName: arrayOfBusLines[i].lineName,
								indexOfPreviousVertex: j + 1,
								distanceToThePreviousVertex: 0,
								coord: Point.latLngToPoint(pathToCompare.getAt(j + 1))
							};
							shortestDistance = distance;
						}
					}
					else {
						distance = google.maps.geometry.spherical.computeDistanceBetween(center, intersection.convertToLatLng());
						if (distance < shortestDistance) {
							nearestIntersection = {
								busLineIndex: i,
								busLineId: parseInt(arrayOfBusLines[i].id),
								busLineName: arrayOfBusLines[i].lineName,
								indexOfPreviousVertex: j,
								distanceToThePreviousVertex: google.maps.geometry.spherical.computeDistanceBetween(pathToCompare.getAt(j), intersection.convertToLatLng()),
								coord: intersection
							};
							shortestDistance = distance;
						}
					}
					j++;
					if (j < lengthOfPathToCompare - 2) {
						segmentToCompare = new Segment(Point.latLngToPoint(pathToCompare.getAt(j)), Point.latLngToPoint(pathToCompare.getAt(j + 1)));
						intersection = circle.intersectWith(segmentToCompare);
					}
				}
				while ((intersection !== false) && (j < lengthOfPathToCompare - 1));
				
				
				//nearestIntersection.connectionId = connectionId++;
				intersectionsList.push(nearestIntersection);
				
			}
			else {
				j++;
			}
		}
		
		//if the last point is inside the circle:
		if (end < lengthOfPathToCompare - 1) {
			//saving the vertex in the intersection list:
			intersectionsList.push({
				//connectionId: connectionId++,
				busLineIndex: i,
				busLineId: parseInt(arrayOfBusLines[i].id),
				busLineName: arrayOfBusLines[i].lineName,
				indexOfPreviousVertex: lengthOfPathToCompare - 1,
				distanceToThePreviousVertex: 0,
				coord: Point.latLngToPoint(pathToCompare.getAt(lengthOfPathToCompare-1))
			});
		}
	}
	
	listOfConnections = intersectionsList;
	
	if ((typeof(args.mode)) && (args.mode == 'makeVirtualsBusStation')) {
		//add the center of the circle as a connection:
		listOfConnections.push({
			//connectionId: connectionId++,
			busLineIndex: args.exception[0],
			busLineId: parseInt(arrayOfBusLines[args.exception].id),
			busLineName: arrayOfBusLines[args.exception].lineName,
			indexOfPreviousVertex: args.indexOfPreviousVertex,
			distanceToThePreviousVertex: args.distanceToThePreviousVertex,
			coord: args.point
		});
	}
		
	return listOfConnections;
}

function areTheyIdenticals(list1, list2, radius){
	//var distance;
	if (list1.length == list2.length) {
		label_0: for (var l = 0; l < list1.length; l++) {
			for (var m = 0; m < list1.length; m++) {
				//if the connection is on the same line
				if (list1[l].busLineIndex == list2[m].busLineIndex) {
					//calculate the distance between the two connections:
			/*		if (list2[l].indexOfPreviousVertex > list1[l].indexOfPreviousVertex){
						distance = arrayOfBusLines[list1[l].busLineIndex].distanceBetweenTwoVertex(list1[l].indexOfPreviousVertex, list2[l].indexOfPreviousVertex);
						distance += list2[l].distanceToThePreviousVertex;
						distance -= list1[l].distanceToThePreviousVertex;
					}
					else if (list2[l].indexOfPreviousVertex < list1[l].indexOfPreviousVertex){
						distance = arrayOfBusLines[list1[l].busLineIndex].distanceBetweenTwoVertex(list2[l].indexOfPreviousVertex, list1[l].indexOfPreviousVertex);
						distance += list1[l].distanceToThePreviousVertex;
						distance -= list2[l].distanceToThePreviousVertex;
					}
					else{
						distance = Math.abs(list1[l].distanceToThePreviousVertex - list2[l].distanceToThePreviousVertex);
					}
					if (distance < 4 * radius){*/
						break label_0;
					//}
					
				}
			}
			return false;
		}
	}
	else {
		return false;
	}
	return true;
}

function areTheyIdenticals2(list1, list2){
	var atLeastOneBusLineLess = false;
	var atLeastOneBusLineMore = false;
	var nbrOfBusLineIdenticalFound = 0;
		label_0: for (var l = 0; l < list1.length; l++) {
			for (var m = 0; m < list2.length; m++) {
				//if the connection is on the same line
				if (list1[l].busLineIndex == list2[m].busLineIndex) {
					nbrOfBusLineIdenticalFound++;
					continue label_0;
				}
			}
			//if a bus line in list1 is not in list2:
			atLeastOneBusLineLess = true;
		}

	if (nbrOfBusLineIdenticalFound < list2.length){
		atLeastOneBusLineMore = true;
	}

	return {
		less: atLeastOneBusLineLess,
		more: atLeastOneBusLineMore
	};
}

function mySuite(q){
	var n = 2;
	var result = 1;

	if (q < 2){
		return 0;
	}
	else{
		while( n < q){
			result += n;
			n++;
		}
	}
	return result;
}

function calculSumDistanceInterLink(listOfBusLinks, busLine, symetricBoundariesOfBusLine){
	if (listOfBusLinks.length > 0) {
		var sum = 0;
		var center = listOfBusLinks[listOfBusLinks.length-1].coord;
		//var proyection;
		for (var i = 0; i < listOfBusLinks.length - 1; i++) {
			//proyection = busLine.findNearestProyectionOrthogonal(listOfBusLinks[i].coord.convertToLatLng(), symetricBoundariesOfBusLine)
			//sum += center.distanceOf(proyection);
			sum += google.maps.geometry.spherical.computeDistanceBetween(center, listOfBusLinks[i].coord.convertToLatLng());
		//showOneCircle(listOfBusLinks[i].coord.convertToLatLng(), 5);
		}
		return sum;
	}
	else{
		return Infinity;
	}
}
function showOneCircle(latLng, radius){
	new gmap.Circle({
			 map: map,
			 clickable: false,
			 fillColor: '#eeeeee',
			 fillOpacity: 0.8,
			 strokeColor: '#000000',
			 strokeOpacity: 0.5,
			 strokeWeight: 2,
			 zIndex: 100000,
			 radius: radius,
			 center: latLng
		});
}
function showOnePolygonCircle(latLng, radius, color){
				 new PolygonCircle({
				 map: map,
				 clickable: false,
				 fillColor: '#FF0000',
				 fillOpacity: 0.4,
				 strokeColor: color,
				 strokeOpacity: 0.5,
				 strokeWeight: 2,
				 zIndex: 100000,
				 radius: radius,
				 center: latLng
				 });
}
function showState(){

	if( document.getElementById("showBusStationIdAndVertexIndex") == null){
		var showBusStationIdAndVertexIndex = document.createElement("div");
		showBusStationIdAndVertexIndex.setAttribute('id', 'showBusStationIdAndVertexIndex');
		showBusStationIdAndVertexIndex.style.top = '0px';
		showBusStationIdAndVertexIndex.style.right = '0px';
		showBusStationIdAndVertexIndex.style.width = '200px';
		showBusStationIdAndVertexIndex.style.height = '50px';
		showBusStationIdAndVertexIndex.style.position = 'fixed';
		showBusStationIdAndVertexIndex.style.background = '#FFFFFF';
		showBusStationIdAndVertexIndex.style.zIndex = "10000";
	}
	else{
		showBusStationIdAndVertexIndex = document.getElementById("showBusStationIdAndVertexIndex");
	}

	showBusStationIdAndVertexIndex.innerHTML = 'current bus station index: ' + arrayOfBusLines.argumentsScan.index + '<br/>' +
		'current vertex index: ' + arrayOfBusLines.argumentsScan.i;

	document.body.appendChild(showBusStationIdAndVertexIndex);

}

if (typeof (loaded.makeVirtualsBusStation) != 'undefined'){
	loaded.makeVirtualsBusStation.push('mainMakeVirtualsBusStationOld.js');
}
if (typeof(loaded.linkBusStationToTroncales) != 'undefined') {
	loaded.linkBusStationToTroncales.push('mainMakeVirtualsBusStationOld.js');
}

					/*		//TO TEST
				 new PolygonCircle({
				 map: map,
				 clickable: false,
				 fillColor: '#FF0000',
				 fillOpacity: 0.4,
				 strokeColor: '#FF0000',
				 strokeOpacity: 0.5,
				 strokeWeight: 2,
				 zIndex: 100000,
				 radius: radius,
				 center: barycenter.convertToLatLng()
				 });

				for (var ll = 0; ll < previousPreviousListOfBusLinksAroundBarycenter.length; ll ++){
				 new PolygonCircle({
				 map: map,
				 clickable: false,
				 fillColor: '#00FF00',
				 fillOpacity: 0.4,
				 strokeColor: '#FF0000',
				 strokeOpacity: 0.5,
				 strokeWeight: 2,
				 zIndex: 100000,
				 radius: radius,
				 center: previousPreviousListOfBusLinksAroundBarycenter[ll].coord.convertToLatLng()
				 });

				}




return false;

							*/

						   //TO DEBUG
				/*

			new PolygonCircle({
				 map: map,
				 clickable: false,
				 fillColor: '#eeeeee',
				 fillOpacity: 0.2,
				 strokeColor: '#FF0000',
				 strokeOpacity: 0.5,
				 strokeWeight: 2,
				 zIndex: 100000,
				 radius: radius,
				 center: previousListOfBusLinks[previousListOfBusLinks.length-1].coord.convertToLatLng()
				 });


					new PolygonCircle({
				 map: map,
				 clickable: false,
				 fillColor: '#eeeeee',
				 fillOpacity: 0.8,
				 strokeColor: '#000000',
				 strokeOpacity: 0.5,
				 strokeWeight: 2,
				 zIndex: 100000,
				 radius: radius,
				 center: listOfBusLinks[listOfBusLinks.length-1].coord.convertToLatLng()
				 });
*/
				//END TO DEBUG


										//DEBUG
						//
/*
						if ( typeof(comptDebug) == 'undefined')
							var comptDebug = 0;
						else
							comptDebug++;

						if (comptDebug > 3)
							var test;

*/
						//
						//END DEBUG

				//TO DEBUG
/*
			new PolygonCircle({
				 map: map,
				 clickable: false,
				 fillColor: '#eeeeee',
				 fillOpacity: 0.2,
				 strokeColor: '#000000',
				 strokeOpacity: 0.5,
				 strokeWeight: 2,
				 zIndex: 100000,
				 radius: radius,
				 center: previousListOfBusLinks[previousListOfBusLinks.length-1].coord.convertToLatLng()
				 });


					new PolygonCircle({
				 map: map,
				 clickable: false,
				 fillColor: '#eeeeee',
				 fillOpacity: 0.8,
				 strokeColor: '#FF0000',
				 strokeOpacity: 0.5,
				 strokeWeight: 2,
				 zIndex: 100000,
				 radius: radius,
				 center: listOfBusLinks[listOfBusLinks.length-1].coord.convertToLatLng()
				 });

				*///END TO DEBUG
				