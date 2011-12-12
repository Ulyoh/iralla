/**
 * @author Yoh
 */
SubMap._busLinesArray = new ArrayOfBusLines();
SubMap._busStationsArray = new  ArrayOfBusStation();

function SubMap(canvas, opts){

	var subMap = new gmap.Map(canvas, opts);
	
	//non-static variables:	
	//public:
	subMap.latLngBoundLimitation = new gmap.LatLngBounds();
	subMap.zoomMin;
	subMap.zoomMax;
	subMap.zoomLimitation;
	//subMap.subMarkersArray = new Array();
	//subMap.sizeDependingOnZoom				can be created if passed by opts
	//subMap.sizeForAZoomValue			can be created if passed by opts
	//subMap.defaultCenter
	//"protected":
	//_nbrDeListener = 0;
	
	//private:
	var _boundLimitationOn = false;
	var _zoomLimitation = "false";
	var _idListenerOfZoomLimitation = -1;
	
	//non-static methods:
	//public:
	subMap.addFunctionsToListener = addFunctionsToListener;
	subMap.removeFunctionsToListeners = removeFunctionsToListeners;
	
	//Methods:
	//public
	/*subMap.putMarkerFromSubMarkersArray = function(latLng){
		//find which marker to put on the map :
		var i = 0;
		var markerShowed = false;
		var nbrOfSubMarkersVisible = 0;
		var marker = new gmap.Marker();
		
		if (this.subMarkersArray != null) {
			for (i = 0; i < this.subMarkersArray.length; i++) {
				marker = this.subMarkersArray[i];
				if (marker.visible == true) {
					//count the number of markers visibles:
					nbrOfSubMarkersVisible++; //TODO optimized with a memory of submarkers visible
				}
				else if (markerShowed == false) {
					marker.showWithCross(latLng);
					markerShowed = true;
				}
			}
			
			//when the last marker drop on the map if used with a listener :
			if (nbrOfSubMarkersVisible == this.subMarkersArray.length - 1) {
				if (this.listenerIdWhenClickMap != null) {
					//document.getElementById('calculate').style.display = "block";
					calculateRoad();
					//the listener is removed when all the marker are on the map:
					this.removeFunctionsToListeners(this.listenerIdWhenClickMap, 'click');
					this.listenerIdWhenClickMap = null;
					
					//add listeners when markers moved:
					this.subMarkersArray[0].listenerShowResult = this.subMarkersArray[0].addFunctionsToListener('dragend', calculateRoad);
					this.subMarkersArray[1].listenerShowResult = this.subMarkersArray[1].addFunctionsToListener('dragend', calculateRoad);
					
				}
			}
		}
	};*/
	
	subMap.enableZoomLimitation = function(bool){
	
		if ((bool === true) && (_idListenerOfZoomLimitation == -1)) {
			function limitZoom(){
				if (this.getZoom() < this.zoomMin){
					this.setZoom(this.zoomMin);
				}
				else if (this.getZoom() > this.zoomMax){
					this.setZoom(this.zoomMax);
				}
			}
			_idListenerOfZoomLimitation = subMap.addFunctionsToListener('zoom_changed', limitZoom, [subMap]);
			
			subMap.zoomLimitation = "on";
			_zoomLimitation = "on";
		}
		else if ((bool === false) && (_idListenerOfZoomLimitation >= 0)) {
			subMap.removeFunctionsToListeners(_idListenerOfZoomLimitation, 'zoom_changed');
			subMap.zoomLimitation = "off";
			_zoomLimitation = "off";
			_idListenerOfZoomLimitation = -1;
		}
		
	};
	
	//TODO:
	/*subMap.enableBoundsLimitation = function(on){
	 
	 //TODO
	 
	 };*/
	subMap.removeAllBusLines = function(){	
		//remove all previous bus lines:
		if ((typeof(SubMap._busLinesArray) != 'undefined') && (SubMap._busLinesArray[0] !== "")) {
			while (SubMap._busLinesArray.length > 0) {
				SubMap._busLinesArray.shift().setMap(null);
			}
		}
	};
	
	subMap.addBusLinesFromDb = function(DbList){

		var busLineBuffer;
		var j = 0;
		var nameList = [];
		var latAndLng;

		subMap.setSizeOfBusLinesDependingOnZoomLevel({
			11: 7,
			12: 8,
			13: 9,
			14: 10,
			15: 11,
			16: 14,
			17: 16
		});
		
		for (var i = 0; i < DbList.length; i++) {
			/*	
			 //DbList[i].path change to an array:
			 var arrayOfStringPoints = DbList[i].path.split(',');
			 
			 var path = [];
			 for( j = 0; j < arrayOfStringPoints.length; j++){
			 latAndLng = arrayOfStringPoints[j].split(' ');
			 path.push(new gmap.LatLng(latAndLng[0], latAndLng[1]));
			 }*/
			if(typeof(DbList[i].path) == 'string'){
				DbList[i].path = JSON.parse(DbList[i].path);
			}
			
			var path = [];
			
			for (j = 0; j < DbList[i].path.length; j++) {
				if((typeof(DbList[i].path[j].lat) != 'undefined') && (typeof(DbList[i].path[j].lng) != 'undefined')){
					path.push(new gmap.LatLng(DbList[i].path[j].lat, DbList[i].path[j].lng));
				}
			}
			
			
			//set the polyline:
			var opts = {
				id: DbList[i].id,
				name: DbList[i].name,
				//layerId: DbList[i].layerId, //set in preTreatment file
				type: DbList[i].type
				//inUse: DbList[i].inUse //set in preTreatment file
			};
			
			busLineBuffer = new BusLine(opts);
			
			var color;
			if(typeof(DbList[i].color) !='undefined'){
				color = '#' + DbList[i].color;
			}
			else{
				color = '#2A60DA';
			}
			
			busLineBuffer.defaultColor = color;
			
			busLineBuffer.setOptions({
				map: this,
				strokeOpacity: 1,
				strokeWeight: subMap.sizeDependingOnZoom[subMap.getZoom()],
				strokeColor: color,
				path: path,
				zIndex: 1000
			});
			
			busLineBuffer.DbList = DbList[i];
			
			busLineBuffer.addListenerOnBusLine();
			
			if (typeof(callFunction) != 'undefined') {
				callFunction(DbList[i], busLineBuffer);
			}
			
			//save reference to SubMap._busLinesArray
			if (SubMap._busLinesArray[0] === ""){
				SubMap._busLinesArray[0] = busLineBuffer;
			}
			else{
				SubMap._busLinesArray.push(busLineBuffer);
			}
		}
		

		
		subMap.enableSizeDependingOnZoom(true);
	};
	
	
	subMap.addBusStationsFromDb = function(DbList){
	
		var options;
		var iconPath;
		
		for (var i = 0; i < DbList.length; i++) {
			//define the icon:
			iconPath = null;
			
			if ((DbList[i].type === '') || (DbList[i].type == 'normal')) {
				iconPath = "data/busStop.png";
			}
			else if (DbList[i].type == 'virtual') {
				iconPath = "data/virtualBusStop.png";
			}
			
			var iconStation = new gmap.MarkerImage(iconPath, null, null, null, new gmap.Size(1, 1));
			
			options = {
				map: this,
				draggable: false,
				visible: true,
				name: DbList[i].name,
				position: new gmap.LatLng(DbList[i].lat, DbList[i].lng),
				icon:iconStation,
				zIndex:100
			};
			
			var busStation = new BusStation(options);
			
			busStation.name = DbList[i].name;
			busStation.id = DbList[i].id;
			busStation.type = DbList[i].type;
			busStation.iconPath = iconPath;
			
			//save reference to SubMap._busLinesArray
			if (SubMap._busStationsArray[0] === "") {
				SubMap._busStationsArray[0] = busStation;
			}
			else {
				SubMap._busStationsArray.push(busStation);
			}
			
			// create event to show the station on mouseover the marker
			busStation.addListenerOnBusStation();
			
			//if an additional function is past in argument:
			if (typeof(additionalFunction) != 'undefined') {
				additionalFunction(DbList[i], busStation);
			}
		}
		
		if (SubMap._busStationsArray.length > 0) {
			subMap.setSizeOfBusStationsDependingOnZoomLevel({
				11: 5,
				12: 8,
				13: 12,
				14: 17,
				15: 23,
				16: 30,
				17: 38
			});
			
			subMap.enableBusStationsSizeDependingOnZoom(true);
		}
	
		SubMap._busStationsArray.busStationsSizingDependingOnZoom();
	};
	
	//busLines
	subMap.setSizeOfBusLinesDependingOnZoomLevel = function(sizesDependingOnZoomsLevels){
		subMap.sizeDependingOnZoom = sizesDependingOnZoomsLevels;
		SubMap._busLinesArray.setSizeOfPolylinesDependingOnZoomLevel(sizesDependingOnZoomsLevels);
	};
	
	subMap.enableSizeDependingOnZoom = function(bool){
		if (bool === true){
			subMap.sizeDependingOnZoom = "on";
		}
		else{
			subMap.sizeDependingOnZoom = "off";
		}
		SubMap._busLinesArray.enableSizeDependingOnZoom(bool);
	};
	
	//busStation
	subMap.setSizeOfBusStationsDependingOnZoomLevel = function(sizesDependingOnZoomsLevels){
		subMap.busStationSizeDependingOnZoom = sizesDependingOnZoomsLevels;
		SubMap._busStationsArray.setSizeOfBusStationsDependingOnZoomLevel(sizesDependingOnZoomsLevels);
	};
	
	subMap.enableBusStationsSizeDependingOnZoom = function(bool){
		if (bool === true){
			subMap.busStationsSizeDependingOnZoom = "on";
		}
		else{
			subMap.busStationsSizeDependingOnZoom = "off";
		}
		SubMap._busStationsArray.enableSizeDependingOnZoom(bool);
	};
	
	
	subMap.getBusLinesArray = function(index){
		return SubMap._busLinesArray[index];
	};
	
	subMap.getLengthOfBusLinesArray = function(){
		return SubMap._busLinesArray.length;
	};
	
	/*
	
	subMap.callToShowBusStationsOnMap = function(){
		//send to the servor:
		request({
			phpFileCalled: mysite + 'getBusStationsToShow.php',
			type: "",
			callback: SubMap.showBusStationsOnMap,
			argumentsCallback: this,
			asynchrone: true
		});
	}*/
	
	subMap.showBusStationsOnMap = function(busStationListFromDb){
		this.addBusStationsFromDb(JSON.parse(busStationListFromDb));
		SubMap._busStationsArray.busStationsSizingDependingOnZoom();
	};
	
	subMap.showBusLinesOnMap = function(busLinesListFromDb){
		var linesListFromBdd = JSON.parse(busLinesListFromDb);

		//parseAnswer.arrowsList[i].path change to an array:
		for ( var i = 0; i < linesListFromBdd.length; i++){
			var arrayOfStringPoints = linesListFromBdd[i].path.split(',');
		
			var path = [];
			var latAndLng;
			for( var j = 0; j < arrayOfStringPoints.length; j++){
				latAndLng = arrayOfStringPoints[j].split(' ');
				path.push(latAndLng);
			}
			linesListFromBdd[i].path = path;
		}
		
		map.addBusLinesFromDb(linesListFromBdd);
	};
	//private:
	
	//constructor:
	if (subMap.zoomLimitation == "on") {
		subMap.enableZoomLimitation(true);
	}
	else {
		subMap.enableZoomLimitation(false);
		subMap.zoomLimitation = "off";
	}
	
	if (subMap.sizeDependingOnZoom) 
		subMap.setSizeOfBusLinesDependingOnZoomLevel(subMap.sizesDependingOnZoomsLevels);
	
	if (subMap.sizeDependingOnZoom == 'on') 
		subMap.enableSizeDependingOnZoom(true);
	
	return subMap;	
}



