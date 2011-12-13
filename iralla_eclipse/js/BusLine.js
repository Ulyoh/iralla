/**
 * @author Yoh
 */


/*
 * idea to get better "rendu" when showing overlay on buslines.
 * 
 * actual pb: not always showing all the buslines as the user should wants
 * ie: seems that some overlay does not works but it seem to be because of
 * resolution on the screen and / or conflict with the events of mouseover
 * and mouse out
 * 
 * so: the possibly solution:
 * for each bus lines shown on the map create an invisible overlay with a
 * z index lower than the buslines.
 * 
 * put a mouseover event for each bus lines
 * 
 * when mouseover :
	 * random number local
	 * if random number global = null 
		 * random number global = random number local
		 * textify number
		 * setimeout("if(random number global == "+ textify number + ") mafonction();",1);
	
	 * else
	 	* set repeatflag mafunction() 
	 	 * return
 *
 * mafonction()
 	 * get mouse position 
	 * 	pass throw all the overlay of the map to test if detected
	 *  they are showing and saved in map.overlayShown
	 *  removed the previous shown ones not detected
	 *  
	 *  setimeout("if(repeatflag == true){repeatflag = false; mafonction();}",1);
	 * if repeatflag to true do it again with actul mouse position
 *
 * 
 * 
 * 
	 * put a mouseover on every other things as map and bus stations
	 * 
	 * 
	 * 
	 * 
	 * OTHER REFLEXION fire event on polyline when mouse en bus station
	 * 
	 * 
	 * 
	 * 
	 * showing when one bus line fired by mouseover
	 * fire all the overlay all found are shown
	 * 
	 * actualise if mouvement more than 2 pixels =>mousemouve on each part
	 * save last moment of calculation at each mousemouve event remove old 
	 * settimeout if exist and do another one
	 * 
	 * remove all when mouse over map
	 * 
	 *OTHER POSSIBILITY:
	 *after the first show, calculate every second the new showing view
	 *
	 *
	 *OTHER ONE:
	 *
	 *when mouseover of a busline
	 *
	 *up all the zindex of all the busline overlay upper than the busline said 1100
	 *when mouseover one overlay :
	 *		show the overlay
	 *		put its zindex to 1050
	 *
 */

 function BusLine(opts){
    var busLine = new gmap.Polyline();

//non-static variables:
	//public:
	busLine.id = opts.id;
	busLine.name = opts.name;
	//busLine.layerId = opts.layerId;
	busLine.type = opts.type;
	//busLine.inUse = opts.inUse; //"mainLine", "feeder" or "other"
    //"protected":
		//none
    //private:
		//none
	
//constructor:
	
//non-static methods:

		
    //private:
	busLine.addListenerOnBusLine = function(){
	
			//IDEA TODO: (if necesary)
		/*
		 * creer un overlay transparent de chaque route pour le traitement des 
		 * events (non necessaire)
		 * 
		 * 
		 * if mouseover busline: 
		 * 		zIndex = 900
		 * 		affiche "under overlay of the busline" (showBuslineOverlay(this);)
		 * 		busline.timeMouseOver = date.getTime();
		 * 		garder la busline ds map.toBeShown, if not exists
		 * 
		 * 
		 * on timeout ou mouseMove:
		 * 		couper coller les elts de map.toBeShown ds toShowList 
		 * 		pour chaque busline de toShowList
		 * 		remonter le zIndex (engendrera un mouseover) a 1000 ou 950
		 * 		pour chaque busline de toShowList
		 * 			if zIndex = 1000
		 * 				hide the "under overlay of the busline" (unShowBuslineOverlay(this);)
		 * 				remove it from map.toBeShown
		 * 
		 */

		map.toBeShown = [];
		map.shownBusLines = [];
		map.isOnABusLine = 0;
		//map.currentzIndex = 800;
		this.storeBuslinesToBeShown = function(latLng){
			map.rectForMouseOver.setOptions({zIndex:1});
			this.setOptions({zIndex: 900});
			showBuslineOverlay(this);
			if(typeof this.selected == 'undefined'){
				this.selected = false;
			}
			if(isInArray(this, map.toBeShown) == false){
				map.toBeShown.push(this);
			}
			if(typeof map.eventRemoveFromToBeShownRunning == 'undefined'){
				//map.removeFromToBeShownRunning = gmap.event.addListener(map, 'mouseover', removeFromToBeShown);
				gmap.event.addListener(map.rectForMouseOver, 'mouseover', removeFromToBeShown);
			}
			//map.isOnABusLine++;
		};
			
		/*gmap.event.addListener(this, 'mouseout', function(){
			map.isOnABusLine--;
			if(map.isOnABusLine == 0){
				removeFromToBeShown();
			}
			});
		*/
		
		this.listenerClick = gmap.event.addListener(this, 'click', showBusLinesInTable);
		
		this.idOfListenerOfShowMyInfo = this.addFunctionsToListener('mouseover', this.storeBuslinesToBeShown, [this, "eVeNt:MouseEvent.latLng"]);
		
	
		
	};
	
/*	//create overlay of the busline but not set on the map yet
	var options = {
			path: busline.getPath(),
			strokeColor: '#000000',
			strokeOpacity: 1,
			strokeWeight: SubMap._busLinesArray.sizeForAZoomValue[map.getZoom()] + 5,
			zIndex: 800
		};
	busline.buslineOverlay = new gmap.Polyline();
	busline.buslineOverlay.setOptions(options);
	busline.buslineOverlay.listenerClick = gmap.event.addListener(busline.buslineOverlay, 'click', showBusLinesInTable);
	busline.buslineOverlay.listenerClick = gmap.event.addListener(busline.buslineOverlay, 'mouseOver', storeBuslinesToBeShown);
	buslineOverlay.busline = busline;*/
    return busLine;
}

 //TODO : found why it is necessary to use relaunchIt
 function relaunchIt(){
	 if(typeof map.lastRandom == 'undefined'){
		 map.lastRandom = 0;
	 }
	 if(map.lastRandom == map.currentRandom){
		 setTimeout("removeFromToBeShown();",1);
	 }
	 map.lastRandom =  map.currentRandom;
	 setTimeout("relaunchIt()", 5000);
	
 }
 /*
	 * 		couper coller les elts de map.toBeShown ds toShowList 
	 * 		pour chaque busline de toShowList
	 * 		remonter le zIndex a 1000 ou 950 (engendrera un mouseover)
	 * 		pour chaque busline de toShowList
	 * 			if zIndex = 1000
	 * 				hide the "under overlay of the busline" (unShowBuslineOverlay(this);)
	 * 				remove it from map.toBeShown*/
function removeFromToBeShown(){
	map.rectForMouseOver.setMap(null);
	var toShowQuestion = [];
	while( map.toBeShown.length > 0){
		toShowQuestion.push(map.toBeShown.shift());
	}
	for(var i = 0; i < toShowQuestion.length; i++){
		hideBuslineOverlay(toShowQuestion[i]);
		if(toShowQuestion[i].selected == true){
			toShowQuestion[i].setOptions({zIndex:950});
		}
		else{
			toShowQuestion[i].setOptions({zIndex:1000});
		}
	}
	
	/*for(var i = 0; i < toShowQuestion.length; i++){
		if(toShowQuestion[i].zIndex > 900){
			hideBuslineOverlay(toShowQuestion[i]);
		}
	}*/
	//TODO handle the map.currentzIndex this is not sufisante:
	/*if (map.currentzIndex > 900){
		map.currentzIndex = 0;
	}*/
	
	//setTimeout("removeFromToBeShown()", 5000);
}
 
function showBusLinesInTable(){
	if(typeof map.show_buslines_table == 'undefined'){
		var table = createTableInElt(getEltById('show_buslines_list'));
		map.show_buslines_table = table.tbody;
		map.show_buslines_table.setAttribute('id', 'table_show_buslines_list');
		map.buslines_shown = [];
	}
	//remove the ones which are not in  map.toBeShown and not selected
	for(var i = 0; i < map.shownBusLines.length ;i++){
		if(map.shownBusLines[i].selected == true){
			continue;
		}
		if( isInArray(map.shownBusLines[i], map.toBeShown) == false ){
			removeNode(map.shownBusLines[i].tableLine);
			map.shownBusLines.splice(i, 1);
		}
	}
	
	
	for(var i = 0; i < map.toBeShown.length; i++){
		//create a new line:
		createLineForShowingListTable(map.show_buslines_table, map.toBeShown[i]);
		map.shownBusLines.push(map.toBeShown[i]);
	}
}

function createLineForShowingListTable(table, busline){
	//create add button
	var addButton = document.createElement('input');
	addButton.className = 'add_button_selected_busline';
	addButton.type = 'image';
	addButton.src = "data/add.png";
	addButton.setAttribute('onclick',"addBuslineToSelected(this)");
	addButton.busline = busline;
	busline.addButton = addButton;

	//create unshow button
	var unShowButton = document.createElement('input');
	unShowButton.className = 'less_button_selected_busline';
	unShowButton.type = 'image';
	unShowButton.src = "data/eye.png";
	unShowButton.setAttribute('onclick',"unShowSelectedBusline(this)");
	unShowButton.busline = busline;
	unShowButton.style.display = "none";
	busline.unShowButton = unShowButton;
	

	//create show button
	var showButton = document.createElement('input');
	showButton.className = 'less_button_selected_busline';
	showButton.type = 'image';
	showButton.src = "data/hided_eye.png";
	showButton.setAttribute('onclick',"showSelectedBusline(this)");
	showButton.busline = busline;
	showButton.style.display = "none";
	busline.showButton = showButton;
		
	//create a div with the road name:
	var span_road_name = document.createElement('span');
	span_road_name.className = 'span_road_name';
	span_road_name.innerHTML = busline.name;
	span_road_name.busline = busline;
	span_road_name.classCell = 'td_name_showing_buslines';

	//create cross
	var cross = document.createElement('input');
	cross.className = 'cross_button_busline';
	cross.type = 'image';
	cross.src = "data/cross.png";
	cross.setAttribute('onclick',"removeBusline(this)");
	cross.busline = busline;
	busline.cross = cross;
	cross.classCell = 'td_showing_buslines';
	
	//div_buttons
	var div_buttons = document.createElement('div');
	div_buttons.appendChild(addButton);
	div_buttons.appendChild(unShowButton);
	div_buttons.appendChild(showButton);
	div_buttons.classCell = 'td_showing_buslines';
	
	//add to the selected list:
	var lineAndCell = addLineInTable(table, {childsInCells:[div_buttons,span_road_name,cross]});
	var tableLine = lineAndCell.line;
	cross.tableLine = lineAndCell.line;
	tableLine.busline = busline;
	tableLine.setAttribute('mouseover', 'showBuslineOverlay()');
	tableLine.setAttribute('mouseout', 'hideBuslineOverlay()');
	busline.tableLine = lineAndCell.line;
	
	//busline.keepStatus = false;
	busline.selected = false;
}
function addBuslineToSelected(button){
	button.busline.selected = true;
	button.busline.addButton.style.display  = "none";
	button.busline.unShowButton.style.display = "block";
}

function unShowSelectedBusline(button){
	button.busline.unShowButton.style.display = "none";
	button.busline.showButton.style.display = "block";
	button.busline.setMap(null);
}

function showSelectedBusline(button){
	button.busline.unShowButton.style.display = "block";
	button.busline.showButton.style.display = "none";
	button.busline.setMap(map);
}

function removeBusline(button){
	var busline = button.busline;
	//handling remove from selected and from not selected
	for(var i = 0; i < map.shownBusLines.length; i++){
		if(map.shownBusLines[i] == busline){
			busline.selected = false;
			busline.tableLine.style.display = "none";
			busline.tableLine = undefined;
			busline.selected = null;
			busline.unShowButton = null;
			busline.showButton = null;
			busline.addButton = null;
			busline.cross = null;
			map.shownBusLines.splice(i,1);
			if((typeof busline.type != 'undefined') && 
					((busline.type == "mainLine") || (busline.type == "feeder") )){
				return;
			}
			busline.setMap(null);
		};
	}
}
/*
function showUnshowBusline(){
	//TODO correler with info
	if(this.shown ==true){
		this.shown ==false;
		this.setMap(null);
	}else{
		this.shown ==true;
		this.setMap(map);
	}
}*/

function removeBuslineFromSelected(){
	//on the screen
	//if part of info do nothing:
	var myInfo = document.getElementById('myInfo');
	/*flag = false;
	for(var j = 0; j < myInfo.showingList.length; j++){
		if(myInfo.showingList[j] != null && myInfo.showingList[j].busline.name == this.name ){
			flag = true;
		}
	}*/
	
	if(isInsideArray(this, myInfo.showingList) == false){
		this.busline.setMap(null);
	}
	this.busline.selected = false;
	removeNode(this.line);
}
 
function showBuslineOverlay(busline){
	

	if (typeof(busline.buslineOverlay) == 'undefined') {
		var options = {
				path: busline.getPath(),
				map: busline.getMap(),
				strokeColor: '#000000',
				strokeOpacity: 0.2,
				strokeWeight: SubMap._busLinesArray.sizeForAZoomValue[map.getZoom()] + 5,
				zIndex: 800
			};
		busline.buslineOverlay = new gmap.Polyline();
		busline.buslineOverlay.setOptions(options);
		busline.buslineOverlay.listenerClick = gmap.event.addListener(busline.buslineOverlay, 'click', showBusLinesInTable);
	}
	else{
		busline.buslineOverlay.setMap(map);
	}
}

function hideBuslineOverlay(busline){
	busline.buslineOverlay.setMap(null);

}

function BusLineOverlay(busLine){

	var options = {
		path: busLine.getPath(),
		map: busLine.getMap(),
		strokeColor: '#FFFFFF',
		strokeOpacity: 0.5,
		strokeWeight: SubMap._busLinesArray.sizeForAZoomValue[map.getZoom()] + 5,
		zIndex: 2000
	};
	
	if (typeof(map.busLineOverlay) == 'undefined') {
		map.busLineOverlay = new gmap.Polyline(options);
		
		map.busLineOverlay.listenerClick = google.maps.event.addListener(map.busLineOverlay, 'click', function(MouseEvent){
		
			var position = new gmap.Point();
			position = map.convertLatLngToPixelCoord(MouseEvent.latLng);
			var myInfo = document.getElementById('myInfo');
			myInfo.innerHTML = map.busLineOverlay.busLine.name;
			myInfo.style.display = "block";
			var xx = position.x + 5;
			var yy = position.y - 5;
			myInfo.style.left = xx + "px";
			myInfo.style.top = yy + "px";
		});
		
		map.busLineOverlay.listenerMouseOut = google.maps.event.addListener(map.busLineOverlay, 'mouseout', function(MouseEvent){
			map.busLineOverlay.timeout = setTimeout(function(){map.busLineOverlay.setMap(null);},500);			
			
			map.myInfoUnableForPolyline = true;
			var myInfo = document.getElementById("myInfo");
			clearTimeout(myInfo.idTimeOutMyInfo);
			myInfo.idTimeOutMyInfo = setTimeout(function(){document.getElementById("myInfo").style.display = "none";},2000);
		});
		
		map.busLineOverlay.listenerMouseOver = google.maps.event.addListener(map.busLineOverlay, 'mouseover', function(MouseEvent){
			clearTimeout(map.busLineOverlay.timeout);
		});
	}
	else {
		clearTimeout(map.busLineOverlay.timeout);
		clearTimeout(getEltById("myInfo") .idTimeOutMyInfo);
		map.busLineOverlay.setOptions(options);
	}
	map.busLineOverlay.busLine = busLine;
}
