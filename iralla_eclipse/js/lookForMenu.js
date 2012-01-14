/**
 * @author yoh2
 */
function showLookForMenu(){
	var look_for_menu = getEltById("look_for_menu");
	if (look_for_menu.style.display == "block") {
		look_for_menu.style.display = "none";
	}
	else{
		look_for_menu.style.display = "block";
	}
}

function show_look_for_roads(){
	getEltById("look_for_menu").style.display = "none";
	getEltById("buscar").style.display = "none";
	var directionNode = getEltById("direction");
	directionNode.style.display = "block";
}

function show_look_for_route(){
	
	map.stepLookForMenu = 'departure';
	map.findRouteState = 'lookForFirstRoad';
	showFindRouteMenu();
	
}

function show_look_for_roads_near_to(){
	map.stepLookForMenu = 'near to';
	map.findRouteState = 'lookForNearTo';
	showFindRouteMenu();
}

function cross_button_click(id_to_remove){
	//if in first cross road selection
	if(id_to_remove == 'direction'){
		hideNodeById('direction');
		showBlockById('buscar');
	}
	else if (map.stepLookForMenu == "departure") {
		hideNodeById('itinerario');
		showBlockById('buscar');
	}
	//if in second cross road selection
	else if (map.stepLookForMenu == "arrival"){
		map.stepLookForMenu = "departure";
		showFindRouteMenu();
	}
}




