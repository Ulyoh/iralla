/**
 * @author yoh2
 */

function showFindRouteMenu(){
	var idsToShow = [];
	var idsToHide = [];
	
	//forbidden to write text to find second road:
	getEltById("to_find_road_2").disabled = "disabled";
			
	//reset roads fields texts:
	getEltById("to_find_road_1").value = '';
	getEltById("to_find_road_2").value = '';
	
	//remove value in list:
	var roads_list_selected_1 = getEltById("roads_list_selected_1");
	var roads_list_selected_2 = getEltById("roads_list_selected_2");
	
	if (roads_list_selected_1 !== null){
		removeNodeById("roads_list_selected_1");
	}
	
	if (roads_list_selected_2 !== null){
		removeNodeById("roads_list_selected_2");
	}
	
	//reset row_suggestion_list_road_1 and 2:
	
	switch(map.stepLookForMenu){
		
		case 'departure':
		
			map.findRouteState = 'lookForFirstRoad';
			
			idsToHide = [
				'look_for_menu',
				'direction',
				'to_road_title',
				'modify_roads_button_1',
				'valid_roads_button_1',
				'modify_roads_button_2',
				'valid_roads_button_2',
				'row_suggestion_list_road_1',
				'row_suggestion_list_road_2',
				'instructions_to_select_marker',
				'cross_road_not_found',
				'row_instructions_marker',
				'row_valid_cancel_marker'
			];
			
			idsToShow = [
				'from_road_title',
				'table_to_find_road_1',
				'row_nombre_road_1',
				//'row_list_road_1',
				'row_input_road_1',
				'table_to_find_road_2',
				'row_nombre_road_2',
				//'row_list_road_2',
				'row_input_road_2',
				'directly_point_at_the_place',
				'itinerario'
			];
			
		break;
		
		
		case 'arrival':
		
			idsToHide = [
				'look_for_menu',
				'direction',
				'from_road_title',
				'modify_roads_button_1',
				'valid_roads_button_1',
				'modify_roads_button_2',
				'valid_roads_button_2',
				'row_suggestion_list_road_1',
				'row_suggestion_list_road_2',
				'instructions_to_select_marker',
				'cross_road_not_found',
				'row_instructions_marker',
				'row_valid_cancel_marker'
			];
			
			idsToShow = [
				'to_road_title',
				'table_to_find_road_1',
				'row_nombre_road_1',
				//'row_list_road_1',
				'row_input_road_1',
				'table_to_find_road_2',
				'row_nombre_road_2',
				//'row_list_road_2',
				'row_input_road_2',
				'directly_point_at_the_place',
				'itinerario'
			];
					
		break;
		
		
		
		
	}
	showBlocksById(idsToShow);
	hideNodesById(idsToHide);
	
}

function findRoads(input_node, event){

	if (input_node.value.length > 3) {
		request({
			phpFileCalled: 'geo_found_way_name.php',
			argumentsToPhpFile: 'q=' + input_node.value,
			callback: showRoadsNames,
			THIS: input_node,
			asynchrone: true
		});
	}
	else{
		//remove all the childs of suggestionNode
		var suggestionListNode = input_node.parentNode.parentNode.parentNode.getElementsByTagName("table")[0];
		while (suggestionListNode.firstChild) {
			suggestionListNode.removeChild(suggestionListNode.firstChild);
		}
		//hide the valids button:
		hideNodeById("valid_roads_button_1");
		hideNodeById("valid_roads_button_2");
	}
}


function showRoadsNames(roadsList){
	
	//getEltById('')
	var suggestionListNode = this.parentNode.parentNode.parentNode.getElementsByTagName("table")[0];
	
	//remove all the childs of tbody of suggestionNode
	if(suggestionListNode.firstChild !== null){
		while (suggestionListNode.firstChild) {
			removeNode(suggestionListNode.firstChild);
		}
	}
	
	if ((typeof(roadsList) != 'undefined') && (roadsList !== '') && (roadsList != '[]') && (roadsList.length > 0)){
		roadsList = JSON.parse(roadsList);
		//suggestionListNode.parentNode.parentNode.style.display = 'none';

		if((this.getAttribute('id') == 'to_find_road_1') && (roadsList.length > 0)){
			getEltById('table_to_find_road_2').style.display = 'none';
		}
		
		suggestionListNode.nextId = 0;
		
		var thead = document.createElement("thead");
		suggestionListNode.appendChild(thead);
		
		var tbody = document.createElement("tbody");
		suggestionListNode.appendChild(tbody);
		
		var tfoot = document.createElement("tfoot");
		suggestionListNode.appendChild(tfoot);
		
		//set the selected qty to 0:
		tbody.selectedQty = 0;
		
		for (var i = 0; i < roadsList.length; i++) {
			//create the list:
			var newLine = document.createElement("tr");
			var newCell = document.createElement("td");
			newLine.appendChild(newCell);
			var newNode = document.createElement("button");
			newNode.setAttribute("type", "button");
			newNode.setAttribute('id', 'nodeShow' + suggestionListNode.nextId);
			suggestionListNode.nextId++;
			newNode.innerHTML = roadsList[i].name;
			newNode.roadsInfos = roadsList[i];
			newNode.selectUnselectRoad = selectUnselectRoad;
			newNode.setAttribute('class', 'road_choice');
			newCell.appendChild(newNode);
			newLine.title = 'clic para seleccionnar';
			tbody.appendChild(newLine);
			
			//on click on the node:
			newNode.setAttribute('onclick', 'selectUnselectRoad(this);');
			//newNode.onclick = "selectUnselectRoad(this)";
		}
					
		if (suggestionListNode.firstChild !== null) {
			suggestionListNode.parentNode.parentNode.style.display = 'block';
		}
		else{
			//hide the valid button:
			//hideNodeById("valid_roads");
		}
	}
	else{
		//hide the valid button:
		//hideNodeById("valid_roads");
	}
	
}

function selectUnselectRoad(that){

	if((typeof(that.selected) == 'undefined') || that.selected === false){
		that.style.backgroundColor="#00FF66";
		that.selected = true;
		that.title = 'clic para deseleccionnar';
		that.parentNode.parentNode.parentNode.selectedQty++;
		//show the valid button:
		if(map.findRouteState == 'lookForFirstRoad'){
			showBlocksById(["valid_roads_button_1", "text_valid_roads_button_1"]);
		}
		else if (map.findRouteState == 'lookForSecondRoad'){
			showBlocksById(["valid_roads_button_2", "text_valid_roads_button_2"]);
		}
	}
	else{
		that.style.backgroundColor="#FFFFFF";
		that.selected = false;
		that.title = 'clic para seleccionnar';
		that.parentNode.parentNode.parentNode.selectedQty--;
		if(that.parentNode.parentNode.parentNode.selectedQty <= 0){
			//hide the valids button:
			hideNodeById("valid_roads_button_1");
			hideNodeById("valid_roads_button_2");
		}
	}
}

function validar(that){
	//hide the valids buttons:
	hideNodeById("valid_roads_button_1");
	hideNodeById("valid_roads_button_2");
	
	var roadsListSelected;
	var newLine;
	var table;
	var rows;
	var i;
	
	if(that == getEltById('valid_roads_button_1')){
		//hide the current text input:
		hideNodeById('row_input_road_1');
		//hide the suggestion list:
		hideNodeById('row_suggestion_list_road_1');
		
		//show a list of the roads selected for the first road:
		roadsListSelected = document.createElement('ul');
		roadsListSelected.setAttribute("id", "roads_list_selected_1");
		table = that.parentNode.parentNode.parentNode.getElementsByTagName("table")[0];
		rows = table.getElementsByTagName("tr");
		for( i = 0; i < rows.length; i++){
			if(rows[i].firstChild.firstChild.selected === true){
				newLine = document.createElement('li');
				newLine.innerHTML = rows[i].firstChild.firstChild.innerHTML;
				newLine.roadsInfos = rows[i].firstChild.firstChild.roadsInfos;
				roadsListSelected.appendChild(newLine);
			}
		}
		getEltById("row_list_road_1").firstChild.appendChild(roadsListSelected);
		
		 //limit the size of the list:
		//roadsListSelected.style.maxHeight = "50px";
		
		//show the modify button:
		showBlockById("modify_roads_button_1");
		
		//show the modify button of second roads if already selected:
		if(getEltById("roads_list_selected_2") != null){
			showBlockById("modify_roads_button_2");	
		}
		
		//show field to find the second road:
		showBlockById("table_to_find_road_2");
		
		//allow to write text to find second road:
		getEltById("to_find_road_2").disabled = null;
		
		//hide the valid button:
		hideNodeById("valid_roads_button_1");
		
		//change state:
		map.findRouteState = 'lookForSecondRoad';

	}
	else if(that == getEltById('valid_roads_button_2')){
		//hide the current text input:
		hideNodeById('row_input_road_2');
		//hide the suggestion list:
		hideNodeById('row_suggestion_list_road_2');
		
		//show a list of the roads selected for the second road:
		roadsListSelected = document.createElement('ul');
		roadsListSelected.setAttribute("id", "roads_list_selected_2");
		table = that.parentNode.parentNode.parentNode.getElementsByTagName("table")[0];
		rows = table.getElementsByTagName("tr");
		for( i = 0; i < rows.length; i++){
			if(rows[i].firstChild.firstChild.selected === true){
				newLine = document.createElement('li');
				newLine.innerHTML = rows[i].firstChild.firstChild.innerHTML;
				newLine.roadsInfos = rows[i].firstChild.firstChild.roadsInfos;
				roadsListSelected.appendChild(newLine);
			}
		}
		getEltById("row_list_road_2").firstChild.appendChild(roadsListSelected);
		
		//limit the size of the table:
		//table.style.maxHeight = "50px"; 
		
		//show the modifies button:
		showBlockById("modify_roads_button_1");
		showBlockById("modify_roads_button_2");
		
		//hide the valid button:
		hideNodeById("valid_roads_button_2");
		
		//show field to find the first road:
		showBlockById("table_to_find_road_1");
		
		//change state:
		map.findRouteState == '';	
		
	}
	
	//if the two roads are selected:
	
	if (((getEltById("roads_list_selected_1") != null)) && (getEltById("roads_list_selected_2") != null)) {
		//get the selected ways:
		var roads1 = [];
		var roads2 = [];
		//of the first street:
		var list = getEltById("roads_list_selected_1");
		
		for (var i = 0; i < list.childNodes.length; i++) {
			roads1.push(list.childNodes[i].roadsInfos.id);
		}
		
		//of the second street:
		list = getEltById("roads_list_selected_2");
		
		for (i = 0; i < list.childNodes.length; i++) {
			roads2.push(list.childNodes[i].roadsInfos.id);
		}
		
		var selectedRoads = {
			roads1: roads1,
			roads2: roads2
		};
		
		//show the two fields to find the roads:
		showBlockById("table_to_find_road_1");
		showBlockById("table_to_find_road_2");
		
		//look for the departure/arrival point:
		request({
			phpFileCalled: 'geo_found_croosroads.php',
			argumentsToPhpFile: 'q=' + JSON.stringify(selectedRoads),
			callback: showIntersectionList,
			//argumentsCallback: map.departureOrArrival,
			asynchrone: true
		});
	}
}

function modify_roads_1(that){
	//change state:
	map.findRouteState = 'lookForFirstRoad';
		
	//remove markers:
	removeCurrentsMarkers({selectedMarker:true});
	
	//remove the road list:
	removeNodeById("roads_list_selected_1");
	
	//show the current text input:
	getEltById('row_input_road_1').style.display = "block";
	
	//hide the indications, the modifies button and the other cross road
	//the other table:
	hideNodesById([
		"modify_roads_button_1",
		"modify_roads_button_2",
		"cross_road_not_found",
		"instructions_to_select_marker",
		"table_to_find_road_2"
	]);
	
	//reset the value:
	//getEltById("to_find_road_1").value = "";
}

function modify_roads_2(that){
	//change state:
	map.findRouteState = 'lookForSecondRoad';
		
	//remove markers:
	removeCurrentsMarkers({selectedMarker:true});
	
	//remove the road list:
	removeNodeById("roads_list_selected_2");
	
	//show the current text input:
	getEltById('row_input_road_2').style.display = "block";
	
	//hide the indications, the modifies button and the other cross road
	//the other table:
	hideNodesById([
		"modify_roads_button_2",
		"modify_roads_button_1",
		"cross_road_not_found",
		"instructions_to_select_marker",
		"table_to_find_road_1"
	]);
	
	//reset the value:
	//getEltById("to_find_road_1").value = "";
}

function showIntersectionList(received, which){
	var i = 0;
	
	//remove list marker
	if(typeof(map.sList) != 'undefined'){
		for (i = 0; i < map.markersList.length; i++) {
			map.markersList[i].setMap(null);
			gmap.event.clearInstanceListeners(marker);
		}
	}
	
	//remove the marker if exists of departure/arrival:
	if((map.stepLookForMenu == "departure") && 
	((typeof(map.departureMarker)) && (map.departureMarker != null))){
		map.departureMarker.setMap(null);
	}
	else if ((map.stepLookForMenu == "arrival") && 
	((typeof(map.arrivalMarker)) && (map.arrivalMarker != null))){
		map.arrivalMarker.setMap(null);
	}
	
	var crossRoadsFound = JSON.parse(received);
	map.markersList = [];
	var latLng;
	var marker;
	if (crossRoadsFound != null) {
		for (i = 0; i < crossRoadsFound.length; i++) {
			latLng = new gmap.LatLng(crossRoadsFound[i].lat, crossRoadsFound[i].lng);
			marker = new gmap.Marker({
				map: map,
				position: latLng,
				raiseOnDrag: false
			});
			gmap.event.addListener(marker, 'click', marker.select_cross_road_marker);
			
			map.markersList.push(marker);
		}
		showBlockById("instructions_to_select_marker");
	}
	else {
		showBlockById("cross_road_not_found");
	}
}


gmap.Marker.prototype.select_cross_road_marker = function(){
	
	//reset roads fields texts:
	getEltById("to_find_road_1").value = '';
	getEltById("to_find_road_2").value = '';
	
	this.setDraggable(true);
	
	//remove all others marker:
	if (typeof(map.markersList) != 'undefined') {
		for (i = 0; i < map.markersList.length; i++) {
			if (map.markersList[i] != this) {
				map.markersList[i].setMap(null);
				gmap.event.clearInstanceListeners(this);
			}
		}
	}
	map.markersList = [];
	
	if (map.stepLookForMenu == 'departure') {		
		//if map.departureMarker already exist:
		//if(typeof(map.departureMarker) != 'undefined'){
		//	map.departureMarker.setMap(null);
		//}
		
		map.departureMarker = this;
		
		//remove listener of the departure marker:
		gmap.event.clearInstanceListeners(this);
		
		map.stepLookForMenu = 'arrival';
	}
	else if (map.stepLookForMenu == 'arrival') {
		
		map.arrivalMarker = this;
		
		//remove listener of the departure marker:
		gmap.event.clearInstanceListeners(this);
	}
	
	//if two marker selected:
	if (((typeof(map.departureMarker) != 'undefined') && (map.departureMarker != null)) &&
	((typeof(map.arrivalMarker) != 'undefined') && (map.arrivalMarker != null))) {
		
		calculateRoad();
	}
		
	//reinit the look for:
	modify_roads_2(getEltById("modify_roads_button_2"));
	modify_roads_1(getEltById("modify_roads_button_1"));
		
	//show menu:
	showFindRouteMenu();
	
	//hide the instructions_to_select_marker
	hideNodeById("instructions_to_select_marker");
};

function initToPlaceTheMarker(){
	map.currentMarker = null;
	
	var idsToHide = [
		'look_for_menu',
		'to_road_title',
		'direction',
		'table_to_find_road_1',
		//'row_nombre_road_1',
		//'row_list_road_1',
		//'row_input_road_1',
		'table_to_find_road_2',
		//'row_nombre_road_2',
		//'row_list_road_2',
		//'row_input_road_2',
		'to_road_title',
		'modify_roads_button_1',
		'text_valid_roads_button_1',
		//'row_suggestion_list_road_1,
		//'row_suggestion_list_road_2,
		'instructions_to_select_marker',
		'cross_road_not_found',
		//'cross_button',
		'directly_point_at_the_place',
		'valid_marker'
	];
	
	var idsToShow = [
		'from_road_title',
		'row_valid_cancel_marker',
		'row_instructions_marker',
		'itinerario'
	];
	
	hideNodesById(idsToHide);
	showBlocksById(idsToShow);
	
	//if the marker considereted already on the map:
	if(((map.stepLookForMenu  == "departure") && 
	((typeof(map.departureMarker) != 'undefined') && (map.departureMarker != null)))||
	((map.stepLookForMenu == "arrival") && 
	((typeof(map.arrivalMarker) != 'undefined') && (map.arrivalMarker != null)))){
		showBlockById("valid_marker");
	}
	
	//listener to put the marker on the map:
	//google.maps.event.addListener(map, 'mouseover', function(MouseEvent){
	//setTimeout(function(){gmap.event.addListener(map, 'click', addMarkerOnTheMap(MouseEvent))}, 1);
	map.listenerIdWhenClickMap = map.addFunctionsToListener('click', addMarkerOnTheMap, [map,"eVeNt:MouseEvent.latLng"]);
	
}

function addMarkerOnTheMap(latLng){
	
	//remove the roads list
	removeNodeById("roads_list_selected_1");
	removeNodeById("roads_list_selected_2");
	
	//remove all others marker:
	if (typeof(map.markersList) != 'undefined') {
		for (i = 0; i < map.markersList.length; i++) {
			map.markersList[i].setMap(null);
		}
	}
	
	if (map.stepLookForMenu == "departure") {
		if ((typeof(map.departureMarker) != 'undefined') && (map.departureMarker != null)) {
			map.departureMarker.setMap(null);
			//remove listener:
			if(typeof(map.departureMarker.listenerDragEnd) != undefined){
				gmap.event.removeListener(map.departureMarker.listenerDragEnd);
			}
		}
		map.departureMarker = new gmap.Marker({
			map: this,
			position: latLng,
			draggable: true,
			raiseOnDrag: false,
			flat: true
		});
	}
	else if (map.stepLookForMenu == "arrival") {
		if ((typeof(map.arrivalMarker) != 'undefined') && (map.arrivalMarker != null)) {
			map.arrivalMarker.setPosition(null);
			//remove listener:
			if(typeof(map.departureMarker.listenerDragEnd) != undefined){
				gmap.event.removeListener(map.arrivalMarker.listenerDragEnd);
			}
		}
		
		map.arrivalMarker = new gmap.Marker({
			map: this,
			position: latLng,
			draggable: true,
			raiseOnDrag: false,
			flat: false
		});	
	}
	
	showBlockById("valid_marker");
}

function validMarker(){
	//remove the listener :
	map.removeFunctionsToListeners(map.listenerIdWhenClickMap, 'click');
			
	//reset roads fields texts:
	getEltById("to_find_road_1").value = '';
	getEltById("to_find_road_2").value = '';
		
	//remove draggable of the marker:
	if (map.stepLookForMenu  == "departure") {
		//map.departureMarker.setDraggable(false);
		map.stepLookForMenu = "arrival";
		showFindRouteMenu();
		
	}
	else if (map.stepLookForMenu == "arrival") {
		showBlocksById(["valid_roads_button_2", "text_valid_roads_button_2"]);
		hideNodeById('itinerario');
		//map.arrivalMarker.setDraggable(false);
	}
	
	//if two marker selected:
	if (((typeof(map.departureMarker) != 'undefined') && (map.departureMarker != null)) &&
	((typeof(map.arrivalMarker) != 'undefined') && (map.arrivalMarker != null))) {
				
		//add listeners to show new road if a marker is moved:
		if (typeof(map.departureMarker.listenerDragEnd) == 'undefined') {
			map.departureMarker.listenerDragEnd = gmap.event.addListener(map.departureMarker, 'dragend', calculateRoad);
		}
		if (typeof(map.arrivalMarker.listenerDragEnd) == 'undefined') {
			map.arrivalMarker.listenerDragEnd = gmap.event.addListener(map.arrivalMarker, 'dragend', calculateRoad);
		}	
		
		calculateRoad();
	}
	
	
}

function cancelMarker(){
	//remove the listener :
	map.removeFunctionsToListeners(map.listenerIdWhenClickMap, 'click');
	
	//show the cross road look for menu:
	showFindRouteMenu();
}



function removeCurrentsMarkers(except){
	var removeSuggestedMarkers = true;
	var removeCurrentMarker = true;
	var removeSelectedMarker = true;
	
	if(typeof(except) != 'undefined'){
		if ((typeof(except.suggestedMarkers)) && (except.suggestedMarkers == true)){
			removeSuggestedMarker = false;
		}
		if ((typeof(except.currentMarker)) && (except.currentMarker == true)){
			removeCurrentMarker = false;
		}
		if ((typeof(except.selectedMarker)) && (except.selectedMarker == true)){
			removeSelectedMarker = false;
		}
	}
	
	//remove all suggested markers
	if ((removeSuggestedMarkers == true) &&
	(typeof(map.markersList) != 'undefined')) {
		for (i = 0; i < map.markersList.length; i++) {
			map.markersList[i].setMap(null);
			gmap.event.clearInstanceListeners(this);
		}
	}
	
	//remove the current marker:
	if ((removeCurrentMarker == true) &&
	((typeof(map.currentMarker) != 'undefined') && (map.currentMarker != null))){
		map.currentMarker.setMap(null);
		map.currentMarker = null;
	}
	
	//remove the selected marker:
	if (removeSelectedMarker == true) {
		if ((map.stepLookForMenu == "departure") &&
		((typeof(map.departureMarker) != 'undefined') && (map.departureMarker != null))) {
			map.currentMarker = map.departureMarker;
		}
		else if ((map.stepLookForMenu == "arrival") &&
		((typeof(map.arrivalMarker) != 'undefined') && (map.arrivalMarker != null))) {
			map.currentMarker = map.arrivalMarker;
		}
	}
	
	
}
