gmap.Circle.prototype.addFunctionsToListener = addFunctionsToListener;		//see commonsFunctions.js
gmap.Circle.prototype.removeFunctionsToListeners = removeFunctionsToListeners;

//increase radius:
gmap.Circle.prototype.increaseRadius = function(){
	var previousRadius = this.getRadius();
	var newRadius = Math.floor(previousRadius  * 1.05);
	if(previousRadius == newRadius){
		newRadius++;
	}
	this.setRadius(newRadius);
};

//decrease radius
gmap.Circle.prototype.decreaseRadius = function(){
	this.setRadius(Math.floor(this.getRadius() / 1.05));
};

//make it draggable
gmap.Circle.prototype.setDraggable = function(bool){
	if ((bool == true) && ( (typeof(this.draggable) == 'undefined') || (this.draggable == false))) {
		this.setOptions({
			clickable: true
		});
		
		this.idListenerInitMove = this.addFunctionsToListener('mousedown', this.initMove, [this, "eVeNt:MouseEvent.latLng"]);
		this.draggable = false;
	}
	else if ((bool == false) && (this.draggable == true)){
		this.removeFunctionsToListeners(this.idListenerInitMove, 'mouseup');
		this.draggable = true;
	}
};

//set initMove:
gmap.Circle.prototype.initMove = function(value){
	map.setOptions({
		draggable: false
	});
	map.circleMoving = this;
	this.idListenerStopMoveCanceled = -1;
	this.initMouse = value;
	this.oldPosition = this.center ;
	this.idListenerMove = this.addFunctionsToListener('mousemove',this.move,[this, "eVeNt:MouseEvent.latLng"]);
	document.getElementsByTagName("body")[0].setAttribute('onmouseup', 'map.circleMoving.stopMove();');
};

gmap.Circle.prototype.move = function(mousePosition){
	this.setCenter(new gmap.LatLng(this.oldPosition.lat() + mousePosition.lat() - this.initMouse.lat(), this.oldPosition.lng() + mousePosition.lng() - this.initMouse.lng()));	
};

//set stopMove
gmap.Circle.prototype.stopMove = function(){
	if (this.idListenerMove > -1)
		this.removeFunctionsToListeners(this.idListenerMove, 'mousemove');
	this.idListenerMove = -1;
	document.getElementsByTagName('body')[0].removeAttribute('onmouseup');
	map.setOptions({
		draggable: true
	});
};


//find if circle intersect with a segment or line:
gmap.Circle.prototype.intersectWith = function(lineOrSegment){
		if (lineOrSegment.type == "Line") {
			var line = lineOrSegment;
		}
		else {
			var line = lineOrSegment.supportLine;
			var segment = lineOrSegment;
		}
		
		var center = this.getCenter();
		var radius = this.getRadius();
		
		//found the line throw the center of the circle and perpendicular at the line:
		var orthogonal = line.CreateOrthogonalByThePoint(center.convertToPoint());
			
		//intersection point between line and orthogonal:
		var intersectionPoint = line.intersection(orthogonal);
		
		//distance from the center of the circle to the line:
		var distance = google.maps.geometry.spherical.computeDistanceBetween(intersectionPoint.convertToLatLng(), center);
		
		if (distance > radius){
			return false;
		}
		else {
			if (lineOrSegment.type == "Line") {
				return intersectionPoint;
			}
			else {
				var pt1 = segment.getPt1();
				var pt2 = segment.getPt2();
				
				//if the intersection point is inside the segment:
				if ((((pt1.x <= intersectionPoint.x) && (intersectionPoint.x <= pt2.x) ||
				(pt2.x <= intersectionPoint.x) && (intersectionPoint.x <= pt1.x))) &&
				(((pt1.y <= intersectionPoint.y) && (intersectionPoint.y <= pt2.y) ||
				(pt2.y <= intersectionPoint.y) && (intersectionPoint.y <= pt1.y)))) {
					return intersectionPoint;
				}
				//if one extremity is at a distance < radius:
				else if (google.maps.geometry.spherical.computeDistanceBetween(center, pt1.convertToLatLng()) < radius){
					return 'pt1 of segment';
				}
				else if (google.maps.geometry.spherical.computeDistanceBetween(center, pt2.convertToLatLng()) < radius){
					return 'pt2 of segment';
				}
				else {
					return false;
				}
			}
		}
	};	
 //to verify the file is loaded
loaded.tools.push('gmap.Circle_extended.js');