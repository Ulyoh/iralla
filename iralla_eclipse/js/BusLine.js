/**
 * @author Yoh
 */

var distance_to_remove_bs_from_list = 80;

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
	
		/*this.showMyInfo = function(latLng){
			var myInfo = document.getElementById('myInfo');
			clearTimeout(myInfo.idTimeOutMyInfo);
			
			var position = new gmap.Point();
			position = map.convertLatLngToPixelCoord(latLng);
			
			//adding the line at the top of the list
			
			myInfo.position = '';
			myInfo.position = new gmap.LatLng(latLng.lat(), latLng.lng());

			myInfo.innerHTML == "";
			if(typeof(myInfo.buslines_table) == 'undefined'){
				myInfo.buslines_table = document.createElement("div");
				createTableInElt(myInfo.buslines_table);
			}
			
			if(typeof(myInfo.showingList) == 'undefined'){
				myInfo.showingList = [];
				appendAsFirstChild(myInfo, myInfo.buslines_table);
			}	
			var flag_add = true;
			for(var j = 0; j < myInfo.showingList.length; j++){
				if(myInfo.showingList[j] != null && myInfo.showingList[j].busline.name == this.name ){
					flag_add = false;
					myInfo.showingList[j].position=new gmap.LatLng(latLng.lat(), latLng.lng());
				}
			}
			var button = newButton({class:'button_buslines_list'});
			button.innerHTML = this.name;
			button.busline = this,
			button.setAttribute('onclick', "selectBusline(this.busline)");
			button.setAttribute('onmouseover', "showBuslineOverlay(this.busline)");
			button.setAttribute('onmouseout', "hideBuslineOverlay(this.busline)");
			
			if(flag_add == true){
				more = {
					lineClass: "table_busline",
					childs: [button]
					};
				var newCell = addLineWithOneCellInTable(myInfo.buslines_table.childNodes[1], more);
	
				myInfo.showingList.push({
					busline:this, 
					position:new gmap.LatLng(latLng.lat(), latLng.lng()),
					tableLine: newCell.line
				});	
			}
			//remove all further than 50m of the current point, removed it 
			//from the list
			for(var i = 0; i < myInfo.showingList.length; i++){
				if(gmap.geometry.spherical.computeDistanceBetween(latLng, myInfo.showingList[i].position) > distance_to_remove_bs_from_list){
					removeNode(myInfo.showingList[i].tableLine);
					myInfo.showingList[i].busline.setOptions({zIndex:1000});
					myInfo.showingList.splice(i,1);
				};
			}
			
			var position  = new gmap.Point();
			position = map.convertLatLngToPixelCoord(latLng);
			var xx = position.x + 5;
			var yy = position.y - 5;
			myInfo.style.left = xx + "px";
			myInfo.style.top = yy + "px";
			
			this.setOptions({zIndex:999});
		};*/
		map.toBeShown = [];
		map.shownBusLines = [];
		this.storeBuslinesToBeShown = function(latLng){

			if(isInsideArray(this, map.toBeShown) === false){
				map.toBeShown.push(this);
				this.cursorPositionToBeShownList  = latLng;
				this.setOptions({zIndex:900});
				showBuslineOverlay(this);
			}
			//remove all further than 50m of the current point, removed it 
			//from the list
			for(var i = 0; i < map.toBeShown.length; i++){
				if(gmap.geometry.spherical.computeDistanceBetween(latLng, map.toBeShown[i].cursorPositionToBeShownList) > 50){
					if(this.selected == true){
						map.toBeShown[i].setOptions({zIndex:950});
					}
					else{
						map.toBeShown[i].setOptions({zIndex:1000});
					}
					hideBuslineOverlay(map.toBeShown[i]);
					map.toBeShown.splice(i,1);
				};
			}
		};
			
		this.listenerClick = gmap.event.addListener(this, 'click', function(){
			if(typeof map.show_buslines_table == 'undefined'){
				var table = createTableInElt(getEltById('show_buslines_list'));
				map.show_buslines_table = table.tbody;
				map.show_buslines_table.setAttribute('id', 'table_show_buslines_list');
				map.buslines_shown = [];
			}
			for(var i = 0; i < map.toBeShown.length; i++){
				//if(isInsideArray(this, map.shownBusLines) == false){
					//create a new line:
					createLineForShowingListTable(map.show_buslines_table, map.toBeShown[i]);
					map.shownBusLines.push(map.toBeShown[i]);
				//}
			}
			
		});
		
		this.idOfListenerOfShowMyInfo = this.addFunctionsToListener('mouseover', this.storeBuslinesToBeShown, [this, "eVeNt:MouseEvent.latLng"]);
		
	/*
		this.listenerMouseOut = gmap.event.addListener(this, 'mouseout', function(){
			//map.busLineOverlay.timeout = setTimeout(function(){map.busLineOverlay.setMap(null);},500);
			
			map.myInfoUnableForPolyline = true;
			var myInfo = document.getElementById("myInfo");
			clearTimeout(myInfo.idTimeOutMyInfo);
			myInfo.idTimeOutMyInfo = setTimeout(function(){document.getElementById("myInfo").style.display = "none";},800);
			
			myInfo.position
			
			
			
		});*/
	};
       
    return busLine;
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
	
	var options = {
			path: busline.getPath(),
			map: busline.getMap(),
			strokeColor: '#000000',
			strokeOpacity: 1,
			strokeWeight: SubMap._busLinesArray.sizeForAZoomValue[map.getZoom()] + 5,
			zIndex: 800
		};
	if (typeof(busline.buslineOverlay) == 'undefined') {
		busline.buslineOverlay = new gmap.Polyline();
	}
	busline.buslineOverlay.setOptions(options);
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
