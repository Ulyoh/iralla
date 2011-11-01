/**
 * @author Yoh
 */
function PolygonCircle(opts){

	var polygonCircle = new gmap.Polygon({
		map: opts.map,
		clickable: opts.clickable,
		fillColor: opts.fillColor,
		fillOpacity: opts.fillOpacity,
		strokeColor: opts.strokeColor,
		strokeOpacity: opts.strokeOpacity,
		strokeWeight: opts.strokeWeight,
		zIndex: opts.zIndex
	});
	
	polygonCircle._r = opts.radius;
	polygonCircle._center = new gmap.LatLng();
	polygonCircle._center = opts.center;
	polygonCircle.idListenerMove = -1;
	var defintionOfPolygon = 50;
	var polygonCoord = new Array();
	var draggable = false;
	polygonCircle.working = 0;
	
	//calculation of the coordinates of the points of the polygon
	function calculOfCoord(center, radius){
		var arrayOfCoord = new Array();
		for( var i = 0; i < defintionOfPolygon; i++){
			var teta = i * 2 * Math.PI / defintionOfPolygon;
			var lng = center.lng() + radius * Math.cos(teta);
			var lat = center.lat() + radius * Math.sin(teta);
			arrayOfCoord[i] = new gmap.LatLng(lat,lng);
		}
		return arrayOfCoord;
	}
	
	//set the center:
	polygonCircle.setCenter = function(newCenter){
		polygonCircle._center = newCenter;
		polygonCircle.setPath(calculOfCoord(newCenter, polygonCircle._r));
		this.working = 0;
	};
	
	//set the radius:
	polygonCircle.setRadius = function(newRadius){
		polygonCircle._r = newRadius;
		polygonCircle.setPath(calculOfCoord(polygonCircle._center, polygonCircle._r));
	};
	
	//increase radius:
	polygonCircle.increaseRadius = function(){
		polygonCircle.setRadius(1.05 * polygonCircle._r);
	};
	
	//decrease radius
	polygonCircle.decreaseRadius = function(){
		polygonCircle.setRadius(polygonCircle._r / 1.05);
	};
	
	//make it draggable
	polygonCircle.setDraggable = function(bool){
		if ((bool == true) && (draggable == false)) {
			polygonCircle.setOptions({
				clickable: true
			});
			
			polygonCircle.idListenerInitMove = this.addFunctionsToListener('mousedown', this.initMove, [this, "eVeNt:MouseEvent.latLng"]);
		}
		else if ((bool == false) && (draggable == true)){
			polygonCircle.removeFunctionsToListeners(polygonCircle.idListenerInitMove, 'mouseup');
		}

	};
	
	//set initMove:
	polygonCircle.initMove = function(value){
		map.setOptions({
			draggable: false
		});
		map.polygonMoving = this;
		this.idListenerStopMoveCanceled = -1;
		this.initMouse = value;
		this.oldPosition = this._center ;
		this.idListenerMove = this.addFunctionsToListener('mousemove',this.move,[this, "eVeNt:MouseEvent.latLng"]);
		document.getElementsByTagName("body")[0].setAttribute('onmouseup', 'map.polygonMoving.stopMove();');
	};
	
	polygonCircle.move = function(mousePosition){
		if (this.working == 0){
			this.working = 1;
			this.setCenter(new gmap.LatLng(this.oldPosition.lat() + mousePosition.lat() - this.initMouse.lat(), this.oldPosition.lng() + mousePosition.lng() - this.initMouse.lng()));
		}			
	};
	
	//set stopMove
	polygonCircle.stopMove = function(){
		if (this.idListenerMove > -1)
			this.removeFunctionsToListeners(this.idListenerMove, 'mousemove');
		this.idListenerMove = -1;
		document.getElementsByTagName('body')[0].removeAttribute('onmouseup');
		map.setOptions({
			draggable: true
		});
	};

	//constructor:
	if ((polygonCircle._r != 0) && (typeof(polygonCircle._center) != "undefined")){
		polygonCircle.setPath(calculOfCoord(polygonCircle._center, polygonCircle._r));
	}
	if((typeof(opts.draggable) != 'undefined') && (opts.draggable == true))
	{
		polygonCircle.setDraggable(true);
		draggable = true;
	}
	
	return polygonCircle;
}


gmap.Polygon.prototype.addFunctionsToListener = addFunctionsToListener;
gmap.Polygon.prototype.removeFunctionsToListeners = removeFunctionsToListeners;


