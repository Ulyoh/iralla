/**
 * @author Yoh
 */
Connection.infos = {
	whichMenuOpen: null,
	index: 0
};

Connection.setAllConnectionsVisible = function(bool){
	for(var i = 0; i < SubMap._busStationArray.length; i++) {
		SubMap._busStationArray[i].setConnectionsVisible(bool);
	}
};

function Connection(busStation, busLine, latLng){
	
	var connection = new gmap.Marker({
							draggable: true,
							map: map,
							position: latLng
						});

//non-static methods:
    //public:
	connection.removeConnection = function(){
		//remove the connection from the busStation
		for (var i = 0; i < this.busStation.arrayOfConnectionsMarkers.length; i++) {
		
			if (this.busStation.arrayOfConnectionsMarkers[i] == this) {
				this.busStation.arrayOfConnectionsMarkers.splice(i, 1);
				break;
			}
		}
		//remove the connection from the busLine
		for (i = 0; i < this.busLine.arrayOfConnectionsMarkers.length; i++) {
		
			if (this.busLine.arrayOfConnectionsMarkers[i] == this) {
				this.busLine.arrayOfConnectionsMarkers.splice(i, 1);
				break;
			}
		}
		//to remove to add a new vertex in the bus line when a connection is created
	/*	//remove the vertex corresponding to the connection
		var path = this.busLine.getPath();
		for (i = 0; i < path.length; i++) {
			if ((path.getAt(i).lng() == this.getPosition().lng()) && (path.getAt(i).lat() == this.getPosition().lat())) {
				path.removeAt(i);
				break;
			}
		}*/
		this.setVisible(false);
	};
	
	//to remove to add a new vertex in the bus line when a connection is created
	/*connection.startMoveConnection = function(){
		//found the vertex which has to be moved:
		var positionOfConnection = Point.latLngToPoint(this.getPosition());
		var shortestDistance = Infinity;
		var distance;
		var indexToMove;
		var path = this.busLine.getPath();
		
		for(var i = 0; i < path.length; i++){
			distance = Point.latLngToPoint(path.getAt(i)).distanceOf(positionOfConnection);
			if( distance < shortestDistance){
				indexToMove = i;
				shortestDistance = distance;
			}
		}
		this.indexToMove = indexToMove;
	};

	connection.endMoveConnection = function(){
		var path = this.busLine.getPath();
		path.setAt(this.indexToMove, this.getPosition());
	};*/
	
	//constructor:
	//found where the connection is made on the polyline:
	var path = busLine.getPath();
	var shortestDistance = Infinity;
	var whereToAddNewPoint;

	//to remove to add a new vertex in the bus line when a connection is created
	//whereToAddNewPoint = busLine.findNearestProyectionOrthogonal(latLng);
	var connectionPosition = busLine.findNearestProyectionOrthogonal(latLng);
	
	//to remove to add a new vertex in the bus line when a connection is created
	/*if(whereToAddNewPoint.type == 'proyection'){
		path.insertAt(whereToAddNewPoint.index+1, latLng);
	}
	else if (whereToAddNewPoint.type != 'vertex'){
		alert( 'error to add connection');
	}*/
	
	/*for(var i = 0; i < path.getLength() - 1; i++){

		//create segment of the 2 points:
		var segment = new Segment(Point.latLngToPoint(path.getAt(i)), Point.latLngToPoint(path.getAt(i+1)));
		var supportLine = segment.getSupportLine();
		var latLngPoint = Point.latLngToPoint(latLng);
		
		var orthLine = supportLine.CreateOrthogonalByThePoint(latLngPoint);
			
		var intersectPoint = supportLine.intersection(orthLine);
		
		//is intersectPoint can be in the segment :
		/if ((((path.getAt(i).lat() < latLng.lat()) && (latLng.lat() < path.getAt(i + 1).lat())) ||
		((path.getAt(i + 1).lat() < latLng.lat()) && (latLng.lat() < path.getAt(i).lat()))) &&
		(((path.getAt(i).lng() < latLng.lng()) && (latLng.lng() < path.getAt(i + 1).lng())) ||
		((path.getAt(i + 1).lng() < latLng.lng()) && (latLng.lng() < path.getAt(i).lng())))) {/
		/	var distance = intersectPoint.distanceOf(latLngPoint);
		
			if (distance < shortestDistance){
				whereToAddNewPoint = i + 1;
				shortestDistance = distance;
			}
		//}
	}*/

	//path.insertAt(whereToAddNewPoint, latLng);
	
	connection.busLine = busLine;
	connection.busStation = busStation;
	if (typeof(busStation.arrayOfConnectionsMarkers) == "undefined"){
		busStation.arrayOfConnectionsMarkers = [];
	}
	busStation.arrayOfConnectionsMarkers.push(connection);
	if (typeof(busLine.arrayOfConnectionsMarkers) == "undefined"){
		busLine.arrayOfConnectionsMarkers = [];
	}
	busLine.arrayOfConnectionsMarkers.push(connection);

//to remove to add a new vertex in the bus line when a connection is created
	/*connection.addFunctionsToListener('dragstart',connection.startMoveConnection,[connection]);
	connection.addFunctionsToListener('dragend',connection.endMoveConnection,[connection]);
	*/
	connection.addFunctionsToListener('dblclick',connection.removeConnection,[connection]);
	connection.whereAdded = connectionPosition.coord;
	
	return connection;
	//end constructor
}

Connection.createAllOnTheMapInit =  function(){
	addInfoInNewDiv();
	//todo : to modify
	Connection.infos.index = 0;
	//end todo
	Connection.createAllOnTheMap();
};


Connection.createAllOnTheMap = function(){

	BusStation.previousModified = undefined;

	index = Connection.infos.index;

	var qty = index + 1;

	getAddInfoDiv().innerHTML = 'looking for connections: <br />' + qty  + '/' + SubMap._busStationArray.length + ' bus stations done <br />';

	var divInfos = getEltById('infos');
	divInfos.scrollTop = divInfos.scrollHeight;

	//for each bus station (recursively)
	SubMap._busStationArray[index].createConnectionsWithAllBusLines();

	index++;
	
	Connection.infos.index = index;
	
	if (index < SubMap._busStationArray.length ){
		setTimeout(function(){Connection.createAllOnTheMap()}, 500);
	}
	else{
		Connection.infos.index = 0;
	}

};	

loaded.linkBusStationToTroncales.push("Connection.js");


