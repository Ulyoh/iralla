/**
 * @author yoh
 */

var mysite = 'http://www.cortocamino.com/guayaquil/';
var xDebugOn = true;

var gmap = google.maps;

//var listOfFilesToLoad = [/*'troncales.xml', 'rutas.xml',*/ 'rutas_reresimplified.xml']; //'rutas.xml'

function initialize() {
	rutasLoaded = false;
	var defaultCenter = new google.maps.LatLng(-2.17,-79.9);

    var myOptions = {
			zoom: 12,
			defaultCenter: defaultCenter,
    	    center: defaultCenter,
    	    disableDefaultUI: true,
    	    mapTypeId: gmap.MapTypeId.ROADMAP,
			navigationControl: true,
			navigationControlOptions: {
				position: google.maps.ControlPosition.LEFT_CENTER,
				style: google.maps.NavigationControlStyle.SMALL
			},
			//to debug
			zoomMin: 0, //changed to 0 instead of 11
			//end to debug
			zoomMax: 24,
			zoomLimitation: "on"
    };
    
    map = new SubMap(document.getElementById("map_canvas"), myOptions);
	
	//center the map for the zoomMin
	
	
	///////////////////////////////////////
	//removed to debug:
	
	/*centerMapOnZoomMin = function(){
		if (map.getZoom() <= map.zoomMin){
			var t=setTimeout(function(){map.panTo(map.defaultCenter);},100);	
		}
	};
	map.addFunctionsToListener("zoom_changed", centerMapOnZoomMin, [map]);
	*/
	//end removed to debug
	
	
	/*
	var icon = new gmap.MarkerImage("data/validBoxSalida16.png");	
	var opts = {
			icon:icon,
			map: map,
			visible: false,
			draggable: true,
			name : 'begin'
	};
	map.markerBegin = new SubMarker(opts);
	opts.icon = new gmap.MarkerImage("data/validBoxLlegada16.png");
	opts.name = 'end';
	map.markerEnd = new SubMarker(opts);
	map.listenerIdWhenClickMap = map.addFunctionsToListener('click', map.putMarkerFromSubMarkersArray, [map,"eVeNt:MouseEvent.latLng"]);
*/
	//document.getElementById('calculate').setAttribute('onclick', 'calculateRoad()');
/*	document.getElementById('calculate').onclick = function(){
		calculateRoad()
	};*/

	//SubMap.callToShowBusStationsOnMap();
	
	if(mainBusStationsList.length > 0){
		map.addBusStationsFromDb(mainBusStationsList);
	}
	
	mainBusStationsList = undefined;
/*	var color = mainBusLinesList[0].color;
	var path = JSON.parse(mainBusLinesList[0].path);
	var path2 = [];
	var latLng;
	
	while(path.length > 0){
		latLng = path.shift();
		path2.push(gmap.LatLng(latLng.lat,latLng.lng));
	}
	
	var polylinetest = gmap.Polyline({
		map:map,
		strokeColor: "#" + color,
		strokeWeight: 5,
		strokeOpacity: 0.5,
		path: path2
	});**/
	
	//mainBusLinesList.color
	//mainBusLinesList.path
	if (mainBusLinesList.length > 0) {
		map.addBusLinesFromDb(mainBusLinesList);
	}
	mainBusLinesList = undefined;
	
	setupCleanLines();
	document.getElementById('suggestionListNode').nextId = 0;
	
	//set style:
	//hide the indications:
	hideNodeById("cross_road_not_found");
	hideNodeById("instructions_to_select_marker");
	
	/*	var myInfo = document.getElementById("myInfo");
		myInfo.idTimeOutMyInfo = google.maps.event.addListener(myInfo, 'mouseout', function(){
			var myInfo = document.getElementById("myInfo");
			clearTimeout(myInfo.idTimeOutMyInfo);
			myInfo.idTimeOutMyInfo = setTimeout(function(){document.getElementById("myInfo").style.display = "none";}, 2000);
			map.myInfoUnableForPolyline = true;
			
		});*/
	
}
/*
function loadRutas(){
	if (typeof(listOfFilesToLoad) != "undefined"){
		for (var i = 0; i < listOfFilesToLoad.length; i++ )
			map.addPolyinesAndBusStationFromFile(loadXMLDoc(listOfFilesToLoad[i]));
	
		rutasLoaded = true;
	}
}
*/
