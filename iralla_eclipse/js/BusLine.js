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
		this.listenerMouseOver = gmap.event.addListener(this, 'mouseover', function(MouseEvent){
			
			//add a polyline with the same path but larger:
			//BusLineOverlay(this);
		});
		
		
		/*
		 reflexion to create a tempo before showing the table od buslines:
		 mouseover busline =>
		 	execute the actual showMyInfo without showing the result
		 		(showMyInfo should be rename)
		 	if table not already shown
			 	if a tempo already in action and distance at the tempo from latlng > 100m 
			 		remove the current tempo
			 		
			 	if not any tempo in action
			 		execute a tempo of 500 ms
			 			
		 	
		 		
		*/
		
		this.showMyInfo = function(latLng){
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
				if(gmap.geometry.spherical.computeDistanceBetween(latLng, myInfo.showingList[i].position) > 50){
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
			
			myInfo.style.display = "block";
			this.setOptions({zIndex:999});
		};
		
		
		this.idOfListenerOfShowMyInfo = this.addFunctionsToListener('mouseover', this.showMyInfo, [this, "eVeNt:MouseEvent.latLng"]);
		
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

function selectBusline(busline){
	if((typeof busline.selected != 'undefined') && (busline.selected == true)){
		return
	}
	var myInfo = document.getElementById('myInfo');

	//create table:
	if(typeof(myInfo.buslines_table_selected) == 'undefined'){
		myInfo.buslines_table_selected = document.createElement('div');
		myInfo.buslines_table_selected.setAttribute('id','div_buslines_table_selected');
		tbody = createTableInElt(myInfo.buslines_table_selected).tbody;
		document.getElementsByTagName('body')[0].appendChild(myInfo.buslines_table_selected);
	}

	busline.selected = true;
	
	if(typeof myInfo.buslines_table_selected.list == 'undefined'){
		myInfo.buslines_table_selected.list = [];
	}
	myInfo.buslines_table_selected.list.push(busline);
	//change color
	//busline.setOptions({color: "#DDDDDD"}); does not work
	
	//create a button:
	var button = newButton({myClass:"buslines_table_selected_button"});
	button.innerHTML = busline.name;
	button.setAttribute('onclick', "showUnshowBusline");
	button.busline = busline;
	button.shown = true;
	
	var cross = document.createElement('input');
	cross.className = 'cross_button_selected_busline';
	cross.type = 'image';
	cross.src = "data/unvalid.png";
	cross.setAttribute('onclick',"removeBuslineFromSelected()");
	cross.busline = busline;
	
	//add to the selected list:
	var line = addLineWithOneCellInTable(tbody, {childs:[button,cross]}).line;
	
	cross.line = line;
	line.busline = busline;
	line.setAttribute('mouseover', 'showBuslineOverlay()');
	line.setAttribute('mouseout', 'hideBuslineOverlay()');
}

function showUnshowBusline(){
	//TODO correler with info
	if(this.shown ==true){
		this.shown ==false;
		this.setMap(null);
	}else{
		this.shown ==true;
		this.setMap(map);
	}
}

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
			strokeColor: '#FFFFFF',
			strokeOpacity: 0.5,
			strokeWeight: SubMap._busLinesArray.sizeForAZoomValue[map.getZoom()] + 5,
			zIndex: 2000
		};
	if (typeof(map.buslineOverlay) == 'undefined') {
		map.buslineOverlay = new gmap.Polyline();
	}
	map.buslineOverlay.currentBusline = busline;
	map.buslineOverlay.setOptions(options);
}

function hideBuslineOverlay(busline){
	if (busline == map.buslineOverlay.currentBusline){
		map.buslineOverlay.setMap(null);
	}
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
