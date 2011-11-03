/**
 * @author Yoh
 */

 gmap.Polyline.prototype.setAddingConnections = function(){
	
	//remove listener to select bus stations:
	for (var i = 0; i < arrayOfBusStations.length; i++) {
		arrayOfBusStations[i].removeFunctionsToListeners(arrayOfBusStations[i].listenerId, 'click');
	}
	
	//hide all the connections
	Connection.setAllConnectionsVisible(false);
	
	//show the connections of this bus station:
	this.setConnectionsVisible(true);
		
	//add a connection when click a busStation
	for( i = 0; i < arrayOfBusStations.length; i++){
		//remove the listeners to addConnection from an other busStation:
		if (typeof(arrayOfBusStations[i].idOfListenerOfAddConnection) != 'undefined'){
			arrayOfBusStations[i].removeFunctionsToListeners(arrayOfBusStations[i].idOfListenerOfAddConnection,'click');
			arrayOfBusStations[i].idOfListenerOfAddConnection = undefined;
		}
		arrayOfBusStations[i].idOfListenerOfAddConnection = arrayOfBusStations[i].addFunctionsToListener('click',arrayOfBusStations[i].createConnectionsWithOneBusLine,[arrayOfBusStations[i], this]);
		
	}

	//find the index of the busline:
	//found the index of the bus station in arrayofbusline
	for( i = 0; i < arrayOfBusLines.length; i++){
		if (arrayOfBusLines[i] == this)
			break;
	}
	
 	//add a button to create the connections with all bus Stations
	if (document.getElementById('button_create_connections') == null) {
		var newField = newLineOfTablePreTreatment();
		var button_create_connections = document.createElement('button');
		button_create_connections.setAttribute('id', 'button_create_connections');
		button_create_connections.innerHTML = 'make connections';
		newField.appendChild(button_create_connections);
	}
	document.getElementById('button_create_connections').setAttribute('onclick', 'arrayOfBusLines[' + i + '].createConnectionsWithAllBusStations();');

	//add a button to unselect the selected busLine:
	if (document.getElementById('button_deselect') == null) {
		var newField = newLineOfTablePreTreatment();
		var button_deselect = document.createElement('button');
		button_deselect.setAttribute('id', 'button_deselect');
		button_deselect.innerHTML = 'deselect';
		newField.appendChild(button_deselect);
	}
	document.getElementById('button_deselect').setAttribute('onclick', 'arrayOfBusLines[' + i + '].deselect();');

 	//make the name of the line changeable
	if (!document.getElementById('div_set_name')){
		var busLineName = document.createElement('div');
		busLineName.setAttribute('id', 'div_set_name');
		getInfosPreBoxNode().appendChild(busLineName);
	}
	document.getElementById('div_set_name').innerHTML = "name of the selected bus line :<br/> <textarea rows='2' cols='40' id='busLineName'  onKeyUp=arrayOfBusLines[" + i + "].setNewNameToShow() />" + 
	this.name + "</textarea>";
	
	var texte = '';
	//show all the connections:
	if (typeof(this.arrayOfConnectionsMarkers) != 'undefined'){
		for ( var j = 0; j < this.arrayOfConnectionsMarkers.length; j++){
			texte += this.arrayOfConnectionsMarkers[j].busStation.nameToShow + '<br/>';
		}	
	}
	if (document.getElementById('div_connections') == null){
		var div_connections = document.createElement('div');
	}
	else{
		div_connections = document.getElementById('div_connections');
		div_connections.setAttribute('id', 'div_connections');
		div_connections.innerHTML = texte;
		getInfosPreBoxNode().appendChild(div_connections);
	}
		
 };
 
 gmap.Polyline.prototype.setNewNameToShow = function(){
	this.name = document.getElementById('busLineName').value;
};

 gmap.Polyline.prototype.createConnectionsWithAllBusStations = function(){
  	//create all the connections of the bus line:
	for( var i = 0; i < arrayOfBusStations.length; i++){
		arrayOfBusStations[i].createConnectionsWithOneBusLine(this);
	}
 };
 
gmap.Polyline.prototype.setConnectionsVisible = function(bool){
	if (typeof(this.arrayOfConnectionsMarkers) != 'undefined'){
		for(var j = 0; j < this.arrayOfConnectionsMarkers.length; j++){
			this.arrayOfConnectionsMarkers[j].setVisible(bool);
		}
	}
};

gmap.Polyline.prototype.deselect = function(){
	mainLinkBusStationToTroncales();
	mainLinkBusStationToTroncales();
};
 

 
loaded.linkBusStationToTroncales.push("gmap.Polyline_extended.js");

 			