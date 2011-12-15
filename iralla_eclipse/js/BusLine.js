/**
 * @author Yoh
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
		
		//create a polyline copy of the busline to handle the events:
		
		this.overlayForEvent = new gmap.Polyline({
			path: this.getPath(),
			clickable: true,
			map: map,
			strokeColor: "#FFFFFF",
			strokeOpacity: 0,
			strokeWeight: SubMap._busLinesArray.sizeForAZoomValue[map.getZoom()],
			zIndex: 1000
		});
		
		this.overlayForEvent.storeBuslinesToBeShown = function(latLng, that){
			map.rectForMouseOver.setMap(map);
			that.overlayForEvent.setOptions({zIndex: 900});
			showBuslineOverlay(that);
			if(typeof that.selected == 'undefined'){
				that.selected = false;
			}
			if(isInArray(that, map.toBeShown) == false){
				map.toBeShown.push(that);
			}
		};
		
		this.overlayForEvent.listenerClick = gmap.event.addListener(this.overlayForEvent, 'click', showBusLinesInTable);
		
		this.idOfListenerOfShowMyInfo = this.overlayForEvent.addFunctionsToListener('mouseover', this.overlayForEvent.storeBuslinesToBeShown, [this.overlayForEvent, "eVeNt:MouseEvent.latLng", this]);
		
		var overlayOptions = {
				path: this.getPath(),
				strokeColor: '#000000',
				strokeOpacity: 0.5,
				strokeWeight: Math.abs(SubMap._busLinesArray.sizeForAZoomValue[map.getZoom()]/2),
				zIndex: 800
				};				
		this.buslineOverlay = new gmap.Polyline();
		this.buslineOverlay.setOptions(overlayOptions);	
	};
	
	busLine.removeBusline = function(){
		for(var i = 0; i < map.shownBusLines.length; i++){
			this.selected = false;
			if(map.shownBusLines[i] == this){
				map.shownBusLines.splice(i,1);
				this.tableLine.style.display = "none";
				this.tableLine = undefined;
				this.unShowButton = null;
				this.showButton = null;
				this.addButton = null;
				this.trash = null;
			}
		}
		if((typeof this.type != 'undefined') && 
				((this.type == "mainLine") || (this.type == "feeder") )){
			return;
		}
		this.overlayForEvent.setMap(null);
		hideBuslineOverlay(this);
		this.setMap(null);
	};
    return busLine;
}
 
function removeFromToBeShown(){
	map.rectForMouseOver.setMap(null);
	var toShowQuestion = [];
	while( map.toBeShown.length > 0){
		toShowQuestion.push(map.toBeShown.shift());
	}
	for(var i = 0; i < toShowQuestion.length; i++){
		hideBuslineOverlay(toShowQuestion[i]);
		if(toShowQuestion[i].selected == true){
			toShowQuestion[i].overlayForEvent.setOptions({zIndex:950});
		}
		else{
			toShowQuestion[i].overlayForEvent.setOptions({zIndex:1000});
		}
	}
}
 
function showBusLinesInTable(){
	//remove the ones which are not in  map.toBeShown and not selected
	for(var i = map.shownBusLines.length-1; i >= 0 ;i--){
		if(map.shownBusLines[i].selected == true){
			continue;
		}
		if( isInArray(map.shownBusLines[i], map.toBeShown) == false ){
			removeNode(map.shownBusLines[i].tableLine);
			map.shownBusLines.splice(i, 1);
		}
	}
	
	//add the ones in map.toBeShown
	for(var i = 0; i < map.toBeShown.length; i++){
		if( isInArray(map.toBeShown[i], map.shownBusLines) == false ){
			//create a new line:
			createLineForShowingListTable(map.show_buslines_table, map.toBeShown[i]);
			map.shownBusLines.push(map.toBeShown[i]);
		}
	}
	
	//show the row to remove:
	var cleanLinesNode = document.getElementById('button_clean_lines');
	cleanLinesNode.style.display = 'table-row';
}

function createLineForShowingListTable(table, busline){
	//create add button
	var addButton = document.createElement('input');
	addButton.className = 'add_button_selected_busline';
	addButton.type = 'image';
	addButton.src = "data/add.png";
	addButton.setAttribute('onclick',"addBuslineToSelected(this)");
	addButton.busline = busline;
	addButton.setAttribute('onmouseover', 'showBuslineOverlay(this.busline)');
	addButton.setAttribute('onmouseout', 'hideBuslineOverlay(this.busline)');
	busline.addButton = addButton;

	//create unshow button
	var unShowButton = document.createElement('input');
	unShowButton.className = 'less_button_selected_busline';
	unShowButton.type = 'image';
	unShowButton.src = "data/eye.png";
	unShowButton.setAttribute('onclick',"unShowSelectedBusline(this)");
	unShowButton.busline = busline;
	unShowButton.style.display = "none";
	unShowButton.setAttribute('onmouseover', 'showBuslineOverlay(this.busline)');
	unShowButton.setAttribute('onmouseout', 'hideBuslineOverlay(this.busline)');
	busline.unShowButton = unShowButton;
	
	//create show button
	var showButton = document.createElement('input');
	showButton.className = 'less_button_selected_busline';
	showButton.type = 'image';
	showButton.src = "data/hided_eye.png";
	showButton.setAttribute('onclick',"showSelectedBusline(this)");
	showButton.busline = busline;
	showButton.style.display = "none";
	showButton.setAttribute('onmouseover', 'showBuslineOverlay(this.busline)');
	showButton.setAttribute('onmouseout', 'hideBuslineOverlay(this.busline)');
	busline.showButton = showButton;
		
	//create a div with the road name:
	var span_road_name = document.createElement('span');
	span_road_name.className = 'span_road_name';
	span_road_name.innerHTML = busline.name;
	span_road_name.classCell = 'td_name_showing_buslines';
	span_road_name.style.cursor = 'pointer';
	span_road_name.setAttribute('onmouseover', 'showBuslineOverlay(this.busline)');
	span_road_name.setAttribute('onmouseout', 'hideBuslineOverlay(this.busline)');
	span_road_name.busline = busline;

	//create trash
	var trash = document.createElement('input');
	trash.className = 'trash_button_busline';
	trash.type = 'image';
	trash.src = "data/trash.png";
	trash.setAttribute('onclick',"removeBuslineFromButton(this)");
	trash.classCell = 'td_showing_buslines';
	trash.setAttribute('onmouseover', 'showBuslineOverlay(this.busline)');
	trash.setAttribute('onmouseout', 'hideBuslineOverlay(this.busline)');
	trash.busline = busline;
	busline.trash = trash;
	
	//div_buttons
	var div_buttons = document.createElement('div');
	div_buttons.appendChild(addButton);
	div_buttons.appendChild(unShowButton);
	div_buttons.appendChild(showButton);
	div_buttons.classCell = 'td_showing_buslines';
	
	//add to the selected list:
	var lineAndCell = addLineInTable(table, {childsInCells:[div_buttons,span_road_name,trash]});
	var tableLine = lineAndCell.line;
	trash.tableLine = lineAndCell.line;
	tableLine.busline = busline;
	busline.tableLine = lineAndCell.line;
	
	//busline.keepStatus = false;
	busline.selected = false;
}
function addBuslineToSelected(button){
	button.busline.selected = true;
	button.busline.addButton.style.display  = "none";
	button.busline.unShowButton.style.display = "block";
}

function addAllBuslineToSelected(){
	
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

function removeBuslineFromButton(button){
	button.busline.removeBusline();
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
	busline.buslineOverlay.setOptions({map:map, strokeWeight: Math.abs(SubMap._busLinesArray.sizeForAZoomValue[map.getZoom()]/2)});
	busline.buslineOverlay.setMap(map);
}

function hideBuslineOverlay(busline){
	busline.buslineOverlay.setMap(null);
}

function setupCleanLines(table){
/*
	//create add button
	var addButton = document.createElement('input');
	addButton.className = 'add_button_selected_busline';
	addButton.type = 'image';
	addButton.src = "data/add.png";
	addButton.setAttribute('onclick',"addAllBuslinesToSelected(this)");
	addButton.busline = busline;
	busline.addButton = addButton;

	//create unshow button
	var unShowButton = document.createElement('input');
	unShowButton.className = 'less_button_selected_busline';
	unShowButton.type = 'image';
	unShowButton.src = "data/eye.png";
	unShowButton.setAttribute('onclick',"unShowAllBuslinesNotInList(this)");
	unShowButton.busline = busline;
	unShowButton.style.display = "none";
	busline.unShowButton = unShowButton;

	//create show button
	var showButton = document.createElement('input');
	showButton.className = 'less_button_selected_busline';
	showButton.type = 'image';
	showButton.src = "data/hided_eye.png";
	showButton.setAttribute('onclick',"showAllBuslinesNotInList(this)");
	showButton.busline = busline;
	showButton.style.display = "none";
	busline.showButton = showButton;*/

	//create trash left
	var trashLeft = document.createElement('input');
	trashLeft.className = 'trash_button_busline';
	trashLeft.type = 'image';
	trashLeft.src = "data/trash.png";
	trashLeft.setAttribute('onclick',"removeAllBusLinesNotSelected()");
	trashLeft.classCell = 'td_showing_buslines';
	
	//create a div with the name:
	var span_road_name = document.createElement('span');
	span_road_name.className = 'span_road_name';
	span_road_name.id = 'text_remove_roads_not_selected';
	span_road_name.innerHTML = 'borrar rutas no seleccionadas';
	span_road_name.classCell = 'td_name_showing_buslines';
	span_road_name.setAttribute('onclick',"removeAllBusLinesNotSelected()");
	span_road_name.style.cursor = 'pointer';

	//create trash right
	var trashRight = document.createElement('input');
	trashRight.className = 'trash_button_busline';
	trashRight.type = 'image';
	trashRight.src = "data/trash.png";
	trashRight.setAttribute('onclick',"removeAllBusLinesNotSelected()");
	trashRight.classCell = 'td_showing_buslines';
	
	//div_buttons
	var div_buttons = document.createElement('div');
	div_buttons.appendChild(trashLeft);
	/*div_buttons.appendChild(unShowButton);
	div_buttons.appendChild(showButton);*/
	div_buttons.classCell = 'td_showing_buslines';
	
	//add to the selected list:
	var lineAndCell = addLineInTable(table, {childsInCells:[div_buttons, span_road_name,trashRight]});
	var tableLine = lineAndCell.line;
	tableLine.id = 'button_clean_lines';
	tableLine.linesIdAdded = [];
	tableLine.setAttribute('mouseover', 'showBuslineOverlay()');
	tableLine.setAttribute('mouseout', 'hideBuslineOverlay()');
	tableLine.style.display = 'none';
}

function removeAllBusLinesNotSelected(){
	var button_clean_lines = getEltById('button_clean_lines');
	SubMap._busLinesArray.removePolylinesFromIds(button_clean_lines.linesIdAdded); 
	button_clean_lines.linesIdAdded = []; 
	button_clean_lines.style.display = "none";		
}
