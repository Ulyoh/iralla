
function calculateRoad(){
	var startPosition = map.departureMarker.getPosition();
	var endPosition = map.arrivalMarker.getPosition();
	
	var start = {
		lat: startPosition.lat(),
		lng: startPosition.lng()
	};
	var end = {
		lat: endPosition.lat(),
		lng: endPosition.lng()
	};
	
	var route = {
		start: start,
		end: end
	};
	
	if( (typeof(map.calculateRoadState) != 'undefined') && (map.calculateRoadState !== null) && (map.calculateRoadState.readyState != 0)){
		map.calculateRoadState.abort();
	}
	
	//send to the servor:
	map.calculateRoadState = request({
		phpFileCalled: mysite + 'finding_route_3.php',
		argumentsToPhpFile: 'q=' + JSON.stringify(route),
		type: "",
		callback: showRoad,
		asynchrone: true
	});
}

function showRoad(datas){
	if(datas === ""){
		
		//TODO show a no result found window
		
		
		return;
	}
	if(typeof(map.ids_list_of_bus_lines) != 'undefined'){
		SubMap._busLinesArray.removePolylinesFromIds(map.ids_list_of_bus_lines); 
	}
	
	datas = JSON.parse(datas);
	
	//TODEBUG:
	//lastDatas = datas;
	
	
	var bs2Bss = datas.bs2bss;
	var path;
	var i;
	var length;
	map.ids_list_of_bus_lines = [];
		
	var opts = {
		clickable: false,
		map: map,
		strokeColor: '#000000',
		strokeOpacity: 0.5,
		strokeWeight: 6,
		zIndex: 99
	};
	
	//remove the previous calcul:
	if(typeof(map.circles) == 'undefined'){
		map.circles = [];
	}
	else{
		for( i = 0; i < map.circles.length; i++) {
			map.circles[i].setMap(null);
		}
		map.circles.length = 0;
	}
	
	var polylinesToRemove;
	//map.calculateRoute
	if (typeof(map.calculateRoute) != 'undefined') {
		for( i = 0; i < map.calculateRoute.length; i++) {
			map.calculateRoute[i].setMap(null);
			map.calculateRoute[i] = null;
		}
		map.calculateRoute.length = 0;
	}
	/*if (typeof(map.calculateRoute) != 'undefined') {
		while (map.calculateRoute.length > 0) {
			polylinesToRemove = map.calculateRoute.shift().setMap(null);
		}
	}*/
	else{
		map.calculateRoute = [];
	}
	
	//show the bus stations:
	var busStations = datas.bus_stations;	
	

	
	opts.strokeColor = "#000000";
	
	var center;
	var previousPath;
	var nextPath;
	var newCircle;
	//for each bus station of the road:
/*	for (i = 1; i < busStations.length-1; i++){
		//make a circle around:
		if (busStations[i] !== null){
			center = new gmap.LatLng(parseFloat(busStations[i].lat), parseFloat(busStations[i].lng));
			
			newCircle = new gmap.Circle({
				center: center,
				clickable: false,
				fillColor: "#00FF00",
				fillOpacity: 0.5,
				map: map,
				radius: 100,
				strokeColor: "#000000",
				strokeOpacity: 0.5,
				strokeWeight: 2,
				zIndex: 100
			});
			map.circles.push(newCircle);
		}
		else{
			
			//show a red circle at the stop of the previous road
			if( i > 0 ){
				previousPath = bs2Bss[i-1].path;
				if (previousPath.length > 0){
					center = new gmap.LatLng(
						parseFloat(previousPath[previousPath.length-1].lat), 
						parseFloat(previousPath[previousPath.length-1].lng)
						);
									
					newCircle = new gmap.Circle({
						center: center,
						clickable: false,
						fillColor: "#FF0000",
						fillOpacity: 0.5,
						map: map,
						radius: 50,
						strokeColor: "#000000",
						strokeOpacity: 0.5,
						strokeWeight: 2,
						zIndex: 100
					});
					map.circles.push(newCircle);
				}
			}
			//show a green circle at the beginning of the next road
			if (i < busStations.length - 1) {
				nextPath = bs2Bss[i].path;
				if (nextPath.length > 0) {
					center = new gmap.LatLng(
						parseFloat(nextPath[0].lat),
						parseFloat(nextPath[0].lng)
						);
					
					newCircle = new gmap.Circle({
						center: center,
						clickable: false,
						fillColor: "#00FF00",
						fillOpacity: 0.5,
						map: map,
						radius: 50,
						strokeColor: "#000000",
						strokeOpacity: 0.5,
						strokeWeight: 2,
						zIndex: 100
					});
					map.circles.push(newCircle);
				}
			}
		}
		
		
		
		
	}
	*/
	if (typeof(bs2Bss) != 'undefined'){
		for(i = 0; i < bs2Bss.length; i++ ){
			path = [];
			/*if (typeof(bs2Bss[i][0]) != 'undefined'){
				for(var k = 0; k < bs2Bss[i][0].path.length; k++){
					path.push(new gmap.LatLng(
								parseFloat(bs2Bss[i][0].path[k].lat), 
								parseFloat(bs2Bss[i][0].path[k].lng))
					);
				}
				opts.path = path;
				map.calculateRoute.push(new gmap.Polyline(opts));
			}*/
			bs2Bss[i].id = 10000+i;
			map.ids_list_of_bus_lines.push(bs2Bss[i].id);
		}
		
		map.addBusLinesFromDb(bs2Bss);
	}
	
	show_details(datas);
}

function show_details(datas){
	var bs2Bss = datas.bs2bss;
	var busStations = datas.bus_stations;
	var infos = getEltById("infos");
	infos.innerHTML = "";
	
	var tableParts = createTableInElt(infos);
	var tbody = tableParts.tbody;
	
	var text = {
		itinerary:"itinerario:",
		fromTheBusStation:"a la estacion de bus:",
		takeNextBus:"cambiar de bus",
		takeTheLine:"tomar la linea:",
		goToTheFirstBusLine:"caminar hasta la primera linea:",
		goToTheFirstBusStation:"caminar hasta la primera estacion de bus:",
		goToTheNextLine:"caminar hasta la linea siguiente:",
		goToTheNextBusStation:"caminar hasta la estacion de bus siguiente:",
		goToTheFinal:"caminar hasta su punto de lleguada",
		lineTitle: 'clic para zoomear'
	};
	
	tableParts.thead.innerHTML = text.itinerary;
	
	var textToShow;
	
	//if otras:
	if (busStations[1] !== null) {
		textToShow = text.goToTheFirstBusStation + '<br/> <span class=name_of>' + busStations[1].name + " </span><br/>";
	}
	//else if troncal or alimentadora
	else {
		textToShow = text.goToTheFirstBusLine;
	}
	
	addLineWithOneCellInTable(tbody, {
		lineTitle: text.lineTitle,
		lineId: 'info' + 0,
		lineClass: 'results_infos',
		innerHTML: textToShow
	});
	
	//show details:
	for (var i = 1; i < busStations.length - 1; i++) {
		textToShow = '';
		
		//show for the bus stations
		if (i > 1) {
			if (busStations[i] !== null) {
				textToShow = text.fromTheBusStation + '<br/> <span class=name_of>' + busStations[i].name + " </span><br/>";
			}
			else if (bs2Bss[i].path.length !== 0) {
				textToShow = text.takeNextBus + '<br/>';
				addLineWithOneCellInTable(tbody, {
					lineTitle: text.lineTitle,
					lineId: 'info' + i,
					lineClass: 'results_infos',
					innerHTML: textToShow
				});
				textToShow = '';
			}
		}

		//show for the bus lines:
		if (i < busStations.length - 2) {
			if (bs2Bss[i].name !== null) {
				if ((i == 1) && (busStations[1] === null)) {
					textToShow = '<span class=name_of>' + bs2Bss[i].name + ' </span><br/>';
				}
				else {
					textToShow += text.takeTheLine + '<br/><span class=name_of>' + bs2Bss[i].name + ' </span><br/>';
				}
			}
			else if (busStations[i + 1] === null) {
				textToShow += text.goToTheNextLine + '<br/>';
			}
			else {
				textToShow += text.goToTheNextBusStation + '<br/>';
			}
			
			//preparation of the map to show the alrededro of the bus station:
			if (textToShow !== '') {
				addLineWithOneCellInTable(tbody, {
					lineTitle: text.lineTitle,
					lineId: 'info' + i,
					lineClass: 'results_infos',
					innerHTML: textToShow
				});
			}
		}
	}					
	
	textToShow = '';
	
	if (busStations[i] !== null) {
		textToShow = text.fromTheBusStation + '<br/> <span class=name_of>' + busStations[i].name + " </span><br/>";
		textToShow += text.goToTheFinal + '<br/>';
	}
	else {
		textToShow = text.goToTheFinal + '<br/>';
	}
			
	addLineWithOneCellInTable(tbody, {
		lineTitle: text.lineTitle,
		lineId: 'info' + 0,
		lineClass: 'results_infos',
		innerHTML: textToShow
	});
	
	
			/*				
	 //preparation of the map to show the alrededro of the bus station:
	 google.maps.event.addListener(map, 'idle', function() {
	 ...
	 });*/
	
	
	showBlockById("show_infos");
}


function find_nearest_roads(){
	
	var position = map.nearToMarker.getPosition();
	
	var position = {
		lat: position.lat(),
		lng: position.lng()
	};
	
	if( (typeof(map.calculateNearTo) != 'undefined') && (map.calculateNearTo !== null) && (map.calculateNearTo.readyState != 0)){
		map.calculateNearTo.abort();
	}
	
	//send to the servor:
	map.calculateNearTo = request({
		phpFileCalled: mysite + 'find_nearest_roads.php',
		argumentsToPhpFile: 'q=' + JSON.stringify(position),
		type: "",
		callback: showNearestRoads,
		asynchrone: true
	});
}

function showNearestRoads(roadsList){
	roadsList = JSON.parse(roadsList);
	var cleanLinesNode = document.getElementById('button_clean_lines');
	cleanLinesNode.style.display = 'table-row';

	SubMap._busLinesArray.removePolylinesFromIds(cleanLinesNode.linesIdAdded); 
	cleanLinesNode.linesIdAdded = [];	
	
	for( var i = 0; i < roadsList.length; i++){
		road = roadsList[i];
		var busLine = SubMap._busLinesArray.getItemById(road.id);
		if (busLine === false) {
			var array = [];
			array.push(road);
			map.addBusLinesFromDb(array);
			busLine = SubMap._busLinesArray.getItemById(road.id);
		}
		else {
			busLine.setMap(map);
			busLine.overlayForEvent.setMap(map);
		}
		SubMap._busLinesArray.setOptionsToAll({
			strokeColor: 'default'
		});
		cleanLinesNode.linesIdAdded.push(busLine.id);
	}
	
	
	
	
}
