/**
 * @author Yoh
 */

 
gmap.Map.prototype.addNewBusStation = function(latLng, type){
	//the marker image:
	var size =  arrayOfBusStations.sizeForAZoomValue[map.getZoom()-1];
	var iconStation;
	
	if ((typeof(type) == 'undefined') || (type == 'normal')){
		type = 'normal';
		iconPath = "data/busStop.png";
	}
	else if (type == 'virtual'){
		iconPath = "data/virtualBusStop.png";
	}
	else if (type == 'boundary'){
		iconPath = "data/boundary.png";
	}

	iconStation = new gmap.MarkerImage(
		iconPath,
		null,
		null,
		null,
		new gmap.Size(size, size)
	);

	var opts = {
		map: map,
		visible: true,
		position: latLng,
		icon: iconStation
	};
		
	var newBusStation = new BusStation(opts);
	
	newBusStation.type = type;
	newBusStation.id = map.newIdForBusStation;
	newBusStation.iconPath = iconPath;
	
	map.newIdForBusStation++;
	
	arrayOfBusStations.push(newBusStation);
	
	if (type != 'boundary'){
		newBusStation.listenerId = newBusStation.addFunctionsToListener('click', newBusStation.setAddingConnections, [newBusStation]);
	}
	
	if (typeof(BusStation.nbrOfBusStationCreated) == 'undefined' ){
		BusStation.nbrOfBusStationCreated = 0;
	}
	//set local id:
	newBusStation.javascriptId = -(++BusStation.nbrOfBusStationCreated);
	
	//listener to show name of the bus station:
	newBusStation.addListenerOnBusStation();
	
	//create circle area to set the connections:
	newBusStation.circle = new gmap.Circle({
		map: map,
		clickable: false,
		draggable: true,
		fillColor: '#FFFF00',
		fillOpacity: 0.4,
		strokeColor: '#000000',
		strokeOpacity: 0.5,
		strokeWeight: 2,
		zIndex: 10000,
		radius: 20,
		center: newBusStation.getPosition(),
		busStation: newBusStation
	});
			
	if ((typeof(map.listenerIdWhenClickMap) != 'undefined') && (map.listenerIdWhenClickMap > -1)){
		map.removeFunctionsToListeners(map.listenerIdWhenClickMap, 'click');
		map.listenerIdWhenClickMap = -1;
		
		//allowed the access to the functions:
		if ((typeof(type) == 'undefined') || (type == 'normal') || (type == "boundary")) {
			mainLinkBusStationToTroncales();
		}
		else if (type == "virtual") {
			mainMakeVirtualsBusStation();
		}
		
		newBusStation.setAddingConnections();
	}
	
	return newBusStation;
};

loaded.linkBusStationToTroncales.push("gmap.Map_extended.js");