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
	  	this.listenerMouseOver = google.maps.event.addListener(this, 'mouseover', function(MouseEvent){
			
			//add a polyline with the same path but larger:
			BusLineOverlay(this);
	  	});
		/*
		google.maps.event.addListener(this, 'click', function(MouseEvent){
		
			var position = new gmap.Point();
			position = map.convertLatLngToPixelCoord(MouseEvent.latLng);
			var myInfo = document.getElementById('myInfo');
			myInfo.innerHTML = map.busLineOverlay.busLine.name;
			myInfo.style.display = "block";
			var xx = position.x + 5;
			var yy = position.y - 5;
			myInfo.style.left = xx + "px";
			myInfo.style.top = yy + "px";
		});*/
		
		this.showMyInfo = function(latLng){
		
			var position = new gmap.Point();
			position = map.convertLatLngToPixelCoord(latLng);
			var myInfo = document.getElementById('myInfo');
			myInfo.innerHTML = this.name;
			myInfo.style.display = "block";
			var xx = position.x + 5;
			var yy = position.y - 5;
			myInfo.style.left = xx + "px";
			myInfo.style.top = yy + "px";
		}
		
		
		this.idOfListenerOfShowMyInfo = this.addFunctionsToListener('click', this.showMyInfo, [this, "eVeNt:MouseEvent.latLng"]);
		
		
		this.listenerMouseOut = google.maps.event.addListener(this, 'mouseout', function(){
			map.busLineOverlay.timeout = setTimeout(function(){map.busLineOverlay.setMap(null);},500);
			
			map.myInfoUnableForPolyline = true;
			var myInfo = document.getElementById("myInfo");
			clearTimeout(myInfo.idTimeOutMyInfo);
			myInfo.idTimeOutMyInfo = setTimeout(function(){document.getElementById("myInfo").style.display = "none";},2000);
		});
	};
       
    return busLine;
}

function BusLineOverlay(busLine){

	var options = {
		path: busLine.getPath(),
		map: busLine.getMap(),
		strokeColor: '#FFFFFF',
		strokeOpacity: 0.5,
		strokeWeight: arrayOfBusLines.sizeForAZoomValue[map.getZoom()] + 5,
		zIndex: 2000
	}
	
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



			/*	
	 if(typeof(myInfo.listener) == 'undefined'){
	 myInfo.listener =  google.maps.event.addListener(myInfo, 'mouseout', function(){
	 myInfo.style.display = "none";
	 //	google.maps.event.removeListener(listenerHideBubbleHandle);
	 });
	 }
	 //clearTimeout(myInfo.idTimeOutMyInfo);
	 //clearTimeout(map.busLineOverlay.timeout);
	 if (typeof(myInfo.listener) != 'undefined') {
	 google.maps.event.removeListener(myInfo.listener);
	 }
	 myInfo.listener = google.maps.event.addListener(this, 'mouseout', function(){
	 
	 
	 clearTimeout(map.busLineOverlay.timeout);
	 map.busLineOverlay.timeout = setTimeout(function(){map.busLineOverlay.setMap('null');}, 1000);
	 });
	 }*/

