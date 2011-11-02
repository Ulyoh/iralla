/**
 * @author Yoh
 */



/**
 * 		SuperMarker
 * 
 * subclass of Marker
 * 
 */

SubMarker._quantity = 0;

function SubMarker(opts){
	var subMarker = new gmap.Marker(opts);
//non-static variables:
	//public
		//none
	//"protected":
	subMarker._cross = new Cross(subMarker);
	subMarker._circle = new gmap.Circle({
		map: subMarker.map,
		clickable: false,
		fillColor: '#23D117',
		fillOpacity: 0.4,
		strokeColor: '#1DA313',
		strokeOpacity: 0.5,
		strokeWeight: 2,
		zIndex: 5000,
		radius: 20,
		center: subMarker.getPosition()
	});
	//private
	var idListenerHideCrossWhenDragMap;
	var idListenerShowCrossWhenDragMapEnd;
	var idListenerHandlingCrossVisibilityWhenZoom;
	var idListenerWhenMarkerStartToDrag;
	var idListenerWhenMarkerEndToDrag;
	var idListenerWhenCrossClicked;
	
//constructor:
	SubMarker._quantity++;	
	subMarker.map.subMarkersArray.push(subMarker);
	
//methods:
	//to show the marker:
	subMarker.show = function(latLng){
		this.setVisible(true);
		this.setPosition(latLng);
	};
	
	//to show the marker and the cross:
	subMarker.showWithCross = function(latLng){
		subMarker.setVisible(true);
		subMarker.setPosition(latLng);
		subMarker._showCross();
		subMarker._setListenersOnCross();
		subMarker._circle.setCenter(latLng);
	};

	//hide the marker and its cross:
	subMarker.hide = function(){
		subMarker.map.removeFunctionsToListeners(idListenerHideCrossWhenDragMap, 'dragstart');
		subMarker.map.removeFunctionsToListeners(idListenerShowCrossWhenDragMapEnd, 'dragend');
		subMarker.map.removeFunctionsToListeners(idListenerHandlingCrossVisibilityWhenZoom, 'zoom_changed');
		subMarker.removeFunctionsToListeners(idListenerWhenMarkerStartToDrag, 'dragstart');
		subMarker.removeFunctionsToListeners(idListenerWhenMarkerEndToDrag, 'dragend');
		subMarker._cross.removeFunctionsToListeners(idListenerWhenCrossClicked,'click');
		subMarker.setVisible(false);
		subMarker._cross.hide();
	};
	
	//show the cross where is the marker and set the listener related to:
	subMarker._showCross = function(){
		var latLng = subMarker.getPosition();
		subMarker._cross.showLatLng(subMarker.map, latLng);
	};
	
	subMarker._setListenersOnCross = function(){
		//Set the listener to hide the cross when dragging the map:
		idListenerHideCrossWhenDragMap = subMarker.map.addFunctionsToListener('dragstart', subMarker._cross.hide,[subMarker._cross]);
		
		//set the listener to show the cross when dragging finished:
		idListenerShowCrossWhenDragMapEnd = subMarker.map.addFunctionsToListener('dragend', subMarker._showCross, [subMarker] );   //this will be the map
		
		//set the listener to hide the cross tag when the map is zoomed:
		var listenerDropMarker = function(){
			this._cross.hide();
			var self = this;
			try {
				clearTimeout(subMarker.t);
			}
			catch(err)
			{}
			subMarker.t = setTimeout(subMarker._showCross,500);
		};
		idListenerHandlingCrossVisibilityWhenZoom = subMarker.map.addFunctionsToListener('zoom_changed', listenerDropMarker, [subMarker] );
		
		//set the listener to hide the cross when the marker is moved:
		idListenerWhenMarkerStartToDrag = subMarker.addFunctionsToListener('dragstart', subMarker._cross.hide,[subMarker._cross] );
		
		//set the listener to show the cross when marker end to move:
		idListenerWhenMarkerEndToDrag = subMarker.addFunctionsToListener('dragend', subMarker._showCross, [subMarker] );
		
		//set the listener when the cross is click to hide the subMarker
		var setCrossListener = function(){
			this.hide();
			theMap = this.map;
			//document.getElementById('calculate').style.display = "none";
			if (theMap.listenerIdWhenClickMap == null) {
				theMap.listenerIdWhenClickMap = theMap.addFunctionsToListener('click', theMap.putMarkerFromSubMarkersArray, [theMap, "eVeNt:MouseEvent.latLng"]);
			}
			//remove listener to launch calcul when a marker is moved:
			this.subMarkersArray[0].removeFunctionsToListeners(this.subMarkersArray[0].listenerShowResult, 'dragend');
			this.subMarkersArray[1].removeFunctionsToListeners(this.subMarkersArray[1].listenerShowResult, 'dragend');
		};
		idListenerWhenCrossClicked = subMarker._cross.addFunctionsToListener('click', setCrossListener, [subMarker] );
	};

	subMarker.destructor = function(subMarker){
		subMarker.hide();
		subMarker._cross.destructor();
		SubMarker._quantity--;
		for (var i = 0; i < subMarker.map.subMarkersArray.length; i++){
			if (subMarker.map.subMarkersArray[i] == subMarker){
				SubMarker.subMarkersArray.splice(i,1);
				break;
			}
		}
	};

	return subMarker;
}

	
	
	