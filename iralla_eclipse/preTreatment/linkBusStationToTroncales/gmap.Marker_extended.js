/**
 * @author Yoh
 */

gmap.Marker.prototype.setAddingConnections = function(){

	var newField;

	if (typeof(BusStation.previousModified) != 'undefined') {
		//fix the previous bus station modified:
		BusStation.previousModified.setDraggable(false);
		
		//set the size of the icon to the good value:
		var size = SubMap._busStationArray.sizeForAZoomValue[map.getZoom()-1];
		/*var iconStation = new gmap.MarkerImage(
							"data/busStop.png", 
							null, 
							null, 
							null, 
							new gmap.Size(size, size));
								
		BusStation.previousModified.setIcon(iconStation);*/
		
		size = new gmap.Size(size, size);
		BusStation.previousModified.icon.size = size;
		
		//change the color of the polygon of the previous selected bus station:
		/*if(BusStation.previousModified.polygonCircle !== null)
			BusStation.previousModified.polygonCircle.setOptions({strokeColor: '#3311EE'});
		*/
		if(BusStation.previousModified.circle !== null){
			BusStation.previousModified.circle.setOptions({strokeColor: '#3311EE'});
		}
		
	}
	
	//save the current bus station being modified:
	BusStation.previousModified = this;
	
	//hide all the connections
	Connection.setAllConnectionsVisible(false);
	
	//show the connections of this bus station:
	this.setConnectionsVisible(true);
	
	//make it draggable
	if (this.type != 'boundary'){
		this.setDraggable(true);
	}
	
	//change the color of the polygon:
	//this.polygonCircle.setOptions({strokeColor: '#33EE11'});
	this.circle.setOptions({strokeColor: '#33EE11'});
	

	if ((typeof(SubMap._busStationArray) != 'undefined') && (SubMap._busStationArray[0] != "")) {
		//make all the polylines clickable
		//remove the listeners to select a polyline
		for (var i = 0; i < SubMap._busStationArray.length; i++) {
			if((typeof(SubMap._busStationArray[i].listeners)!= 'undefined') && (typeof(SubMap._busStationArray[i].listeners['click'])!= 'undefined')){
				SubMap._busStationArray[i].removeFunctionsToListeners(SubMap._busStationArray[i].listenerId, 'click');
			}
		}
		
		
		//add a connection when click a polyline
		for (i = 0; i < SubMap._busStationArray.length; i++) {
			//remove the listeners to addConnection from an other busStation:
			if (typeof(SubMap._busStationArray[i].idOfListenerOfAddConnection) != 'undefined') {
				SubMap._busStationArray[i].removeFunctionsToListeners(SubMap._busStationArray[i].idOfListenerOfAddConnection, 'click');
				SubMap._busStationArray[i].idOfListenerOfAddConnection = undefined;
			}
			SubMap._busStationArray[i].idOfListenerOfAddConnection = SubMap._busStationArray[i].addFunctionsToListener('click', Connection, ["", this, SubMap._busStationArray[i], "eVeNt:MouseEvent.latLng"]);
		}
	}
	//found the index of the bus station in arrayofbusstation
	for( i = 0; i < SubMap._busStationArray.length; i++){
		if (SubMap._busStationArray[i] == this)
			break;
	}
	
	//add a button to remove the selected bus station
	if (document.getElementById('button_remove_BusStation') === null) {
		newField = newLineOfTablePreTreatment();
		var button_remove_BusStation = document.createElement('button');
		button_remove_BusStation.setAttribute('id', 'button_remove_BusStation');
		button_remove_BusStation.innerHTML = 'remove';
		newField.appendChild(button_remove_BusStation);
		
	}
	document.getElementById('button_remove_BusStation').setAttribute('onclick', 'SubMap._busStationArray[' + i + "].remove('linkBusStationToTroncales');");
	
	//add a button to increase the radius of the polygonCircle
	if (document.getElementById('button_increase_polygonCircle') === null) {
		newField = newLineOfTablePreTreatment();
		var button_increase_polygonCircle = document.createElement('button');
		button_increase_polygonCircle.setAttribute('id', 'button_increase_polygonCircle');
		button_increase_polygonCircle.innerHTML = 'increase radius';
		newField.appendChild(button_increase_polygonCircle);
	}
	//document.getElementById('button_increase_polygonCircle').setAttribute('onclick', 'SubMap._busStationArray[' + i + '].polygonCircle.increaseRadius();');
	document.getElementById('button_increase_polygonCircle').setAttribute('onclick', 'SubMap._busStationArray[' + i + '].circle.increaseRadius();');
	
	//add a button to decrease the radius of the polygonCircle
	if (document.getElementById('button_decrease_polygonCircle') === null) {
		newField = newLineOfTablePreTreatment();
		var button_decrease_polygonCircle = document.createElement('button');
		button_decrease_polygonCircle.setAttribute('id', 'button_decrease_polygonCircle');
		button_decrease_polygonCircle.innerHTML = 'decrease radius';
		newField.appendChild(button_decrease_polygonCircle);
	}
	//document.getElementById('button_decrease_polygonCircle').setAttribute('onclick', 'SubMap._busStationArray[' + i + '].polygonCircle.decreaseRadius();');
	document.getElementById('button_decrease_polygonCircle').setAttribute('onclick', 'SubMap._busStationArray[' + i + '].circle.decreaseRadius();');
	
	//add a button to reset all the connections of the busStation selected:
	if (document.getElementById('button_reset_connections') === null) {
		newField = newLineOfTablePreTreatment();
		var button_reset_connections = document.createElement('button');
		button_reset_connections.setAttribute('id', 'button_reset_connections');
		button_reset_connections.innerHTML = 'reset connections';
		newField.appendChild(button_reset_connections);
	}
	document.getElementById('button_reset_connections').setAttribute('onclick', 'SubMap._busStationArray[' + i + '].createConnectionsWithAllBusLines();');
	
	//add a button to unselect the selected busStation:
	if (document.getElementById('button_deselect') === null) {
		newField = newLineOfTablePreTreatment();
		var button_deselect = document.createElement('button');
		button_deselect.setAttribute('id', 'button_deselect');
		button_deselect.innerHTML = 'deselect';
		newField.appendChild(button_deselect);
	}
	document.getElementById('button_deselect').setAttribute('onclick', 'SubMap._busStationArray[' + i + '].deselect();');

	//make the name of the station changeable
	if (!document.getElementById('div_set_name')){
		var busStationName = document.createElement('div');
		busStationName.setAttribute('id', 'div_set_name');
		getInfosPreBoxNode().appendChild(busStationName);
	}
	document.getElementById('div_set_name').innerHTML = "name of the selected bus station :<br/> <textarea rows='2' cols='40' id='busStationName'  onKeyUp=SubMap._busStationArray[" + i + "].setNewNameToShow() /> " + 
	this.name + "</textarea>";


	
	var texte = '';
	//show all the connections:
	if (typeof(this.arrayOfConnectionsMarkers) != 'undefined'){
		for ( var j = 0; j < this.arrayOfConnectionsMarkers.length; j++){
			texte += this.arrayOfConnectionsMarkers[j].busLine.lineName + '<br/>';
		}	
	}
	if (document.getElementById('div_connections') === null){
		var div_connections = document.createElement('div');
	}
	else{
		div_connections = document.getElementById('div_connections');
		div_connections.setAttribute('id', 'div_connections');
		div_connections.innerHTML = texte;
		getInfosPreBoxNode().appendChild(div_connections);
	}
		
};


gmap.Marker.prototype.createConnectionsWithAllBusLines = function(){
	//remove all old connections:
	this.removeAllConnections();
	
	for(var j = 0; j < SubMap._busStationArray.length; j++){
		//if making connections with mainLines and feeder:
		if((preTreatment.current == 'linkBusStationToTroncales') &&
		((this.type == 'normal') || (this.type == 'boundary')) &&
		((SubMap._busStationArray[j].type == "mainLine") || (SubMap._busStationArray[j].type == "feeder"))){
			
			this.createConnectionsWithOneBusLine(SubMap._busStationArray[j]);
			
		}
		//if making connections with feeders and others:
		else if((preTreatment.current == 'makeVirtualsBusStation') && 
		(this.type == 'virtual') &&
		((SubMap._busStationArray[j].type == "feeder") || (SubMap._busStationArray[j].type == "other"))){
			this.createConnectionsWithOneBusLine(SubMap._busStationArray[j]);
		}
		
		/*
		//for each troncal and alimentadora:
		if(((typeof(option) == 'undefined') || (typeof(options.type) == 'undefined') || (options.type == "normal")) &&
		((SubMap._busStationArray[j].type == "mainLine") || (SubMap._busStationArray[j].type == "feeder"))){
			this.createConnectionsWithOneBusLine(SubMap._busStationArray[j]);
		}
		//for each rutas:
		if(((typeof(option) == 'undefined') || (typeof(options.type) == 'undefined') || (options.type == "other")) &&
		(SubMap._busStationArray[j].type == "virtual")){
			
		}*/
	}
	
	if (typeof(BusStation.previousModified) != 'undefined') {
		BusStation.previousModified.setAddingConnections();
	}
	
	//test id the current bus station got at least one link:
	if( (typeof(this.arrayOfConnectionsMarkers) == 'undefined') || (this.arrayOfConnectionsMarkers.length <= 0)){
		addInfoInNewDiv();
		getAddInfoDiv().innerHTML = 'bus station with id :' + this.id + ' does not have any links created <br />';
		getAddInfoDiv().scrollTop = getAddInfoDiv().scrollHeight;
		addInfoInNewDiv();
	}
	
};

/*
gmap.Marker.prototype.createConnectionsWithAllBusLines = function(busStationIndex, firstIndex){

	if (( typeof(this.arrayOfConnectionsMarkers) != 'undefined' ) && ( this.arrayOfConnectionsMarkers.length > 0 )){
		//remove all old connections:
		this.removeAllConnections();
	}

	if ( typeof(firstIndex) == 'undefined' ){
		firstIndex = 0;
	}
	
	addInfoInNewDiv();

	this.createConnectionsWithAllBusLines_OneStep(firstIndex, busStationIndex);
};

gmap.Marker.prototype.createConnectionsWithAllBusLines_OneStep = function(index, busStationIndex){

	//for each troncal and alimentadora (recursivly):
	if((SubMap._busStationArray[index].type == "mainLine") || (SubMap._busStationArray[index].type == "feeder")){
		this.createConnectionsWithOneBusLine(SubMap._busStationArray[index]);
	}

	index++;
	getAddInfoDiv().innerHTML = index + '/' + SubMap._busStationArray.length + 'bus lines checked';

	if ( index < SubMap._busStationArray.length ){
		setTimeout(function(){SubMap._busStationArray[' + busStationIndex + '].createConnectionsWithAllBusLines_OneStep(' + index + ',' + busStationIndex + ')}, 100);
	}

}
*/

gmap.Marker.prototype.createConnectionsWithOneBusLine = function(busLine){
	var path;
	var segment;
	var polygon;
	var result;
	var resultIntersection = new Array();
	var distance;
	var interPoint;
	var point;
	var center;
	var isAnExtremity = false;
	var connection;
	var listOfConnections;
	var listOfConnectionsToCreate;
	
	//extract the path:
	path = busLine.getPath();
	//for each segment of the troncal or alimentadora:
	//search if cross the polygon:
//	resultIntersection = busLine.isPolygonCircleCrossed(this.polygonCircle);
	
	//list of the index of all bus lines except the one 
	var othersLinesIndex = [];
	for(var i = 0; i < SubMap._busStationArray.length; i++){
		if( SubMap._busStationArray[i] != busLine){
			othersLinesIndex.push(i);
		}
	}
	
	//list of connections between this and busLine:
	listOfConnectionsToCreate = findBusLinksAroundThisPoint({
		//point: Point.latLngToPoint(this.circle.getCenter()),
		//radius: this.circle.getRadius() * 0.000009,
		circle: this.circle,
		exception: othersLinesIndex
	});
	
	//create connections:
	for( i = 0; i < listOfConnectionsToCreate.length; i++){
		Connection(this, busLine, listOfConnectionsToCreate[i].coord.convertToLatLng(),listOfConnectionsToCreate[i].indexOfPreviousVertex , listOfConnectionsToCreate[i].distanceToThePreviousVertex )
	}

};
	


gmap.Marker.prototype.setNewNameToShow = function(){
	this.name = document.getElementById('busStationName').value;
};


gmap.Marker.prototype.setConnectionsVisible = function(bool){
	if (typeof(this.arrayOfConnectionsMarkers) != 'undefined'){
		for(var j = 0; j < this.arrayOfConnectionsMarkers.length; j++){
			this.arrayOfConnectionsMarkers[j].setVisible(bool);
		}
	}
};

gmap.Marker.prototype.remove = function(whichMenuOpen){
	if( this.type == 'boundary'){
 		this.busLine.removeBoundary(this);
	}
	//remove it from the list of arrayOfBusStation
	//this.polygonCircle.setOptions({map: null});
	this.circle.setOptions({map: null});
	//this.polygonCircle = null;
	this.circle = null;
	this.removeAllConnections({
		whichMenuOpen: whichMenuOpen
	});
	for( var i = 0; i < SubMap._busStationArray.length; i++){	//TODO hide arrayofbusstations make it accessible only in map
		if(SubMap._busStationArray[i] == this ){
			SubMap._busStationArray.splice(i, 1);
			break;
		}
	}
		
	//remove it from the map:
	this.setMap(null);
};

gmap.Marker.prototype.removeAllConnections = function(){
	if(preTreatment.current == 'linkBusStationToTroncales'){
		mainLinkBusStationToTroncales();
		var reactiveLinkBusStationToTroncales = true;
	}
	
	if (typeof(this.arrayOfConnectionsMarkers) != 'undefined') {
		while (this.arrayOfConnectionsMarkers.length > 0) {
			var busLineArray = this.arrayOfConnectionsMarkers[0].busLine.arrayOfConnectionsMarkers;
			for (var j = 0; j < busLineArray.length; j++) {
				if (busLineArray[j] == this) {
					busLineArray.splice(j, 1);
					break;
				}
			}
			this.arrayOfConnectionsMarkers[0].removeConnection();
		}
	}
	
	if(reactiveLinkBusStationToTroncales == true){
		mainLinkBusStationToTroncales();
	}
};

gmap.Marker.prototype.deselect = function(){
	
	//this.removeFunctionsToListeners(this.listenerId, 'click');
	
	if (preTreatment.current == 'linkBusStationToTroncales') {
		mainLinkBusStationToTroncales();
		mainLinkBusStationToTroncales();
	}
	else if (preTreatment.current == 'makeVirtualsBusStation'){
		mainMakeVirtualsBusStation();
		mainMakeVirtualsBusStation();
	}
	
	
	//BusStation.previousModified.polygonCircle.setOptions({strokeColor: '#3311EE'});
	BusStation.previousModified.circle.setOptions({strokeColor: '#3311EE'});
};

gmap.Marker.addNewBusStationByClick = function(which){
	//remove the access to the others button and functions
	if((typeof(which) == 'undefined') || (which == 'normal') || (which == "boundary")){
		mainLinkBusStationToTroncales();
	}
	else if (which == "virtual"){
		mainMakeVirtualsBusStation();
	}
	
	map.listenerIdWhenClickMap = map.addFunctionsToListener('click',map.addNewBusStation , [map,'eVeNt:MouseEvent.latLng', which]);
};
/*
gmap.Marker.prototype.setPositionAndPolygonCenter = function(latLng){
	this.setPosition(latLng);
	this.polygonCircle.setCenter(latLng);
	if ((typeof(map.listenerIdWhenClickMap) != 'undefined') && (map.listenerIdWhenClickMap > -1)){
		map.removeFunctionsToListeners(map.listenerIdWhenClickMap, 'click');
		map.listenerIdWhenClickMap = -1;
	}
}
*/
loaded.linkBusStationToTroncales.push("gmap.Marker_extended.js");
