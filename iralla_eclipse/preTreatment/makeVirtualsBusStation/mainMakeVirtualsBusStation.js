
function mainMakeVirtualsBusStation(){
	
	var tablePreTreatment = document.getElementById('tablePreTreatment');
	var node = document.getElementById("button_MakeVirtualsBusStation");
	if (node.state != "activate") {
		preTreatment.current = 'makeVirtualsBusStation';
		node.innerHTML = node.innerHTML + " : ACTIVATED";
		node.state = "activate";
		
		//hide the buttons not in use:
		for (var i = 1; i < tablePreTreatment.childNodes.length; i++) {
			if ( i != 4){
				tablePreTreatment.childNodes[i].style.display = "none";
			}
		}

		//add a button to launch the old version
		if (document.getElementById('button_old_version') === null) {
			var newField = newLineOfTablePreTreatment();
			var button_old_version = document.createElement('button');
			button_old_version.setAttribute('id', 'button_old_version');
			button_old_version.innerHTML = 'old version';
			newField.appendChild(button_old_version);
		}
		document.getElementById('button_old_version').setAttribute('onclick', 'makeVirtualsBusStationOldVersion()');
		
		//add a button to launch the old version with busline and vertex index predeterminated
		if (document.getElementById('div_old_version_index') === null) {
			var div_old_version_index = document.createElement('div');
			div_old_version_index.setAttribute('id', 'div_old_version_index');
			var input_index = document.createElement('input');
			input_index.setAttribute('id', 'input_index');
			input_index.setAttribute('type', 'text');
			input_index.setAttribute('value', 'index');
			div_old_version_index.appendChild(input_index);
			var input_i = document.createElement('input');
			input_i.setAttribute('id', 'input_i');
			input_i.setAttribute('type', 'text');
			input_i.setAttribute('value', 'i');
			div_old_version_index.appendChild(input_i);
			var input_button_old_version = document.createElement('input');
			input_button_old_version.setAttribute('id', 'input_button_old_version');
			input_button_old_version.setAttribute('type', 'submit');
			input_button_old_version.setAttribute('value', 'Submit');
			div_old_version_index.appendChild(input_button_old_version);
			newField.appendChild(div_old_version_index);
		}
		document.getElementById('input_button_old_version').setAttribute('onclick', 'makeVirtualBusStationFrom()');
				
				
		//add a button to create all the areas
		if (document.getElementById('button_create_areas') === null) {
			var newField = newLineOfTablePreTreatment();
			var button_create_areas = document.createElement('button');
			button_create_areas.setAttribute('id', 'button_create_areas');
			button_create_areas.innerHTML = 'create areas';
			newField.appendChild(button_create_areas);
		}
		document.getElementById('button_create_areas').setAttribute('onclick', 'SubMap._busLinesArray.createAreasAroundBusLines()');
		
		//add a button to look for the links
		if (document.getElementById('button_look_for_links') === null) {
			var newField = newLineOfTablePreTreatment();
			var button_look_for_links = document.createElement('button');
			button_look_for_links.setAttribute('id', 'button_look_for_links');
			button_look_for_links.innerHTML = 'look for links';
			newField.appendChild(button_look_for_links);
		}
		document.getElementById('button_look_for_links').setAttribute('onclick', 'SubMap._busLinesArray.lookForLinks()');
		
		
		//add a button to groupe the links
		if (document.getElementById('button_grouping_links') === null) {
			var newField = newLineOfTablePreTreatment();
			var button_grouping_links = document.createElement('button');
			button_grouping_links.setAttribute('id', 'button_grouping_links');
			button_grouping_links.innerHTML = 'grouping links';
			newField.appendChild(button_grouping_links);
		}
		document.getElementById('button_grouping_links').setAttribute('onclick', 'SubMap._busLinesArray.groupingLinks()');
		
		
		//listener to select a virtual bus Station
		for (i = 0; i < SubMap._busStationsArray.length; i++) {
			if(SubMap._busStationsArray[i].type == "virtual"){
				SubMap._busStationsArray[i].listenerId = SubMap._busStationsArray[i].addFunctionsToListener('click', SubMap._busStationsArray[i].setAddingConnections, [SubMap._busStationsArray[i]]);
			}
		}
		
		//listener to select a bus line:
		if ((SubMap._busLinesArray.length > 0) && (SubMap._busLinesArray[0] != "")){
			for (i = 0; i < SubMap._busLinesArray.length; i++) {
				if ((SubMap._busLinesArray[i].type == "other") || (SubMap._busLinesArray[i].type == "feeder")) {
					SubMap._busLinesArray[i].listenerId = SubMap._busLinesArray[i].addFunctionsToListener('click', SubMap._busLinesArray[i].setAddingConnections, [SubMap._busLinesArray[i]]);
				}
			}
		}

		//add a button to create all the links of virtual bus stations:
		var newField = newLineOfTablePreTreatment();
		var buttonCreateAllConnection = document.createElement('button');
		buttonCreateAllConnection.setAttribute('id', 'button_create_all_virtuals_connections');
		buttonCreateAllConnection.innerHTML = 'create all connectiones of virtual bus stations';
		buttonCreateAllConnection.setAttribute('onclick', 'Connection.createAllOnTheMapInit({type:"virtual"});');
		buttonCreateAllConnection.style.width = '198px';
		newField.appendChild(buttonCreateAllConnection);
		
		//add a button to add a virtual busStation:
		if (typeof(document.getElementById('button_add_virtual_busStation') == 'undefined')) {
			newField = newLineOfTablePreTreatment();
			var button_add_busStation = document.createElement('button');
			button_add_busStation.setAttribute('id', 'button_add_virtual_busStation');
			button_add_busStation.innerHTML = 'add a virtual bus station';
			button_add_busStation.style.width = '198px';
			newField.appendChild(button_add_busStation);
		}
		document.getElementById('button_add_virtual_busStation').setAttribute('onclick', 'gmap.Marker.addNewBusStationByClick("virtual")');	
		
	
		//add a button to save the virtual bus station
		if (typeof(document.getElementById('button_save_virtual_busStation') == 'undefined')) {
			newField = newLineOfTablePreTreatment();
			var button_add_busStation = document.createElement('button');
			button_add_busStation.setAttribute('id', 'button_save_virtual_busStation');
			button_add_busStation.innerHTML = 'save virtual bus station';
			button_add_busStation.style.width = '198px';
			newField.appendChild(button_add_busStation);
		}
		document.getElementById('button_save_virtual_busStation').setAttribute('onclick', 'mainSavingModifications("virtual");');	
	
	}
	else{
		node.innerHTML = 'make virutal bus stations';
		
		//remove the button to launch the old version
		removeNodeById('button_old_version');
		
		//remove the button to launch the old version with busline and vertex index predeterminated
		removeNodeById('div_old_version_index');
		
		//remove the button to create all the areas
		removeNodeById('button_create_areas');
		
		//remove the button to look for the links
		removeNodeById('button_look_for_links');
		
		//remove the button to groupe the links
		removeNodeById('button_grouping_links');
		
////////////////////FOR VIRUTAL BUS STATION/////////////////////////

		for (i = 0; i < SubMap._busStationsArray.length; i++) {
			if (SubMap._busStationsArray[i].type == "virtual"){
				SubMap._busStationsArray[i].removeFunctionsToListeners(SubMap._busStationsArray[i].listenerId, 'click');
			}
		}
		
		if ((typeof(SubMap._busLinesArray) != "undefined") && (SubMap._busLinesArray[0] != "")) {
			for (i = 0; i < SubMap._busLinesArray.length; i++) {
				if ((SubMap._busLinesArray[i].type == "other") || (SubMap._busLinesArray[i].type == "feeder")){
					SubMap._busLinesArray[i].removeFunctionsToListeners(SubMap._busLinesArray[i].listenerId, 'click');
				}
			}
		}
		
		if (typeof(BusStation.previousModified) != 'undefined') {
			
			for(i = 0; i < SubMap._busLinesArray.length; i++){
				//remove the listeners to addConnection from an other busStation:
				if (typeof(SubMap._busLinesArray[i].idOfListenerOfAddConnection) != 'undefined'){
					SubMap._busLinesArray[i].removeFunctionsToListeners(SubMap._busLinesArray[i].idOfListenerOfAddConnection,'click');
					SubMap._busLinesArray[i].idOfListenerOfAddConnection = undefined;
				}
			}
			
			BusStation.previousModified.setDraggable(false);
			
			//set the size of the icon to the good value:
			var size = SubMap._busStationsArray.sizeForAZoomValue[map.getZoom() - 1];
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
		removeNodeById("button_create_all_virtuals_connections");
		
		//remove the button to increase the radius of PolygonCircle: 
		removeNodeById("button_increase_polygonCircle");
		
		//remove the button to decrease the radius of PolygonCircle:
		removeNodeById("button_decrease_polygonCircle");
		
		//remove the button to reset the connections: 
		removeNodeById("button_reset_connections");
		
		//remove the button to add a bus station: 
		removeNodeById("button_add_virtual_busStation");
		
		//remove the button to deselect a bus station: 
		removeNodeById("button_deselect");
		
//buttons of BUS LINE
		//remove the button to deselect a bus station: 
		removeNodeById("button_create_connections");
		
		
		//remove the button to save a virtual bus station:
		removeNodeById("button_save_virtual_busStation");
		
	/*	//deselect the previous function:
		//virtual press the deselect button:
		if (document.getElementById('button_deselect') !== null){
			document.getElementById('button_deselect').click();
		}
				
		//remove the button 'deselect'
		removeNodeById('button_deselect');
	*/	
		
		//remove the fields of the table that are empty:
		removeEmptyLinesOfTable(tablePreTreatment);
		/*var trs = tablePreTreatment.getElementsByTagName('tr');
		
		for ( i = 0; i < trs.length; i++){
			if (trs[i].childNodes[0].childNodes.length === 0){
				trs[i].parentNode.removeChild(trs[i]);
				i--;
			}
		}*/
		
		//add the buttons hidden:
		for ( i = 0; i < tablePreTreatment.childNodes.length; i++){
			tablePreTreatment.childNodes[i].style.display = "table-row";
		}
		node.state = "desactivate";
		preTreatment.current = null;
	}
}

loaded.makeVirtualsBusStation.push('mainMakeVirtualsBusStation.js');

