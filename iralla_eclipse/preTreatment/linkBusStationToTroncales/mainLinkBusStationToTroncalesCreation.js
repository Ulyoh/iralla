/**
 * @author Yoh
 */
// put all the bus station with a low z-index:

function mainLinkBusStationToTroncales(){
	
	//create listeners to add/remove link between bus station and bus lines
	var node = document.getElementById("button_LinkBusStationToTroncales");

	var tablePreTreatment = document.getElementById('tablePreTreatment');

	if (node.state != "activate") {
		preTreatment.current = 'linkBusStationToTroncales';
		node.innerHTML = node.innerHTML + " : ACTIVATED";
		node.state = "activate";
		
		//hide the buttons not in use:
		for ( var i = 1; i < tablePreTreatment.childNodes.length; i++){
			if (i != 3) {
				tablePreTreatment.childNodes[i].style.display = "none";
			}
		}
		
		//listener to select a bus Station
		for (i = 0; i < SubMap._busStationArray.length; i++) {
			if ((SubMap._busStationArray[i].type == "normal") || (SubMap._busStationArray[i].type == "boundary")){
				SubMap._busStationArray[i].listenerId = SubMap._busStationArray[i].addFunctionsToListener('click', SubMap._busStationArray[i].setAddingConnections, [SubMap._busStationArray[i]]);
			}
		}
		
		//listener to select a bus line:
		if ((SubMap._busStationArray.length > 0) && (SubMap._busStationArray[0] != "")){
			for (i = 0; i < SubMap._busStationArray.length; i++) {
				if ((SubMap._busStationArray[i].type == "mainLine") || (SubMap._busStationArray[i].type == "feeder")) {
					SubMap._busStationArray[i].listenerId = SubMap._busStationArray[i].addFunctionsToListener('click', SubMap._busStationArray[i].setAddingConnections, [SubMap._busStationArray[i]]);
				}
			}
		}

		//add a button to create all the links:
		var newField = newLineOfTablePreTreatment();
		var buttonCreateAllConnection = document.createElement('button');
		buttonCreateAllConnection.setAttribute('id', 'button_create_all_connections');
		buttonCreateAllConnection.innerHTML = 'create all connectiones';
		buttonCreateAllConnection.setAttribute('onclick', 'Connection.createAllOnTheMapInit();');
		buttonCreateAllConnection.style.width = '198px';
		newField.appendChild(buttonCreateAllConnection);
		
		//add a button to add a busStation:
		if (typeof(document.getElementById('button_add_busStation') == 'undefined')) {
			newField = newLineOfTablePreTreatment();
			var button_add_busStation = document.createElement('button');
			button_add_busStation.setAttribute('id', 'button_add_busStation');
			button_add_busStation.innerHTML = 'add a bus station';
			button_add_busStation.style.width = '198px';
			newField.appendChild(button_add_busStation);
		}
		
	document.getElementById('button_add_busStation').setAttribute('onclick', 'gmap.Marker.addNewBusStationByClick("normal")');

	}
	else {
		node.innerHTML = "create link: bus lines / troncales";
		
		for (i = 0; i < SubMap._busStationArray.length; i++) {
			if ((SubMap._busStationArray[i].type == "normal") || (SubMap._busStationArray[i].type == "boundary")){
				SubMap._busStationArray[i].removeFunctionsToListeners(SubMap._busStationArray[i].listenerId, 'click');
			}
		}
		
		if ((typeof(SubMap._busStationArray) != "undefined") && (SubMap._busStationArray[0] != "")) {
			for (i = 0; i < SubMap._busStationArray.length; i++) {
				if ((SubMap._busStationArray[i].type == "mainLine") || (SubMap._busStationArray[i].type == "feeder")) {
					SubMap._busStationArray[i].removeFunctionsToListeners(SubMap._busStationArray[i].listenerId, 'click');
				}
			}
		}
		
		if (typeof(BusStation.previousModified) != 'undefined') {
			
			for(i = 0; i < SubMap._busStationArray.length; i++){
				//remove the listeners to addConnection from an other busStation:
				if (typeof(SubMap._busStationArray[i].idOfListenerOfAddConnection) != 'undefined'){
					SubMap._busStationArray[i].removeFunctionsToListeners(SubMap._busStationArray[i].idOfListenerOfAddConnection,'click');
					SubMap._busStationArray[i].idOfListenerOfAddConnection = undefined;
				}
			}
			
			BusStation.previousModified.setDraggable(false);
			
			//set the size of the icon to the good value:
			var size = SubMap._busStationArray.sizeForAZoomValue[map.getZoom() - 1];
			var iconPath = BusStation.previousModified.iconPath;
			var iconStation = new gmap.MarkerImage(iconPath, null, null, null, new gmap.Size(size, size));
			BusStation.previousModified.setIcon(iconStation);
		}	
		map.listenersOfBusStations = 'undefined';
		Connection.setAllConnectionsVisible(false);

		//delete the textArea to set name of bus station or bus line:
		/*if (document.getElementById('div_set_name') != null)
			document.getElementById('div_set_name').parentNode.removeChild(document.getElementById('div_set_name'));
			*/
		removeNodeById("div_set_name");
			
		//delete the div to show the connections:
		removeNodeById("div_connections");
		
		//remove the button to delete the selected bus station:
		removeNodeById("button_remove_BusStation");
		
//buttons of BUS STATION
		//remove the button to create all the connections
		removeNodeById("button_create_all_connections");
		
		//remove the button to increase the radius of PolygonCircle: 
		removeNodeById("button_increase_polygonCircle");
		
		//remove the button to decrease the radius of PolygonCircle:
		removeNodeById("button_decrease_polygonCircle");
		
		//remove the button to reset the connections: 
		removeNodeById("button_reset_connections");
		
		//remove the button to add a bus station: 
		removeNodeById("button_add_busStation");
		
		//remove the button to deselect a bus station: 
		removeNodeById("button_deselect");
		
//buttons of BUS LINE
		//remove the button to deselect a bus station: 
		removeNodeById("button_create_connections");
		
		//add the buttons hidden:
		for ( i = 0; i < tablePreTreatment.childNodes.length; i++){
			tablePreTreatment.childNodes[i].style.display = "table-row";
		}
		
		//remove the fields of the table that are empty:
		removeEmptyLinesOfTable(tablePreTreatment);
		
		node.state = "desactivate";
		preTreatment.current = null;
	}
}

loaded.linkBusStationToTroncales.push("mainLinkBusStationToTroncales.js");



