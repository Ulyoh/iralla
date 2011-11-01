/**
 * @author Yoh
 */

function Point(x,y){
	this.type = 'Point';
	
	if ( findingArea === true) {
		this.x = x;
		this.y = y;
	}else{
		this.x = round(x);
		this.y = round(y);	
	}
	
	
	this.distanceOf = function(point){
		return round(Math.sqrt(( Math.pow(this.x - point.x, 2) + Math.pow(this.y - point.y, 2) ) ));
	};
	
	this.makeLatLng = function(){
		return new gmap.LatLng(this.y, this.x);
	};
	
	this.addVector = function(vector){
		return new Point(round(this.x + vector.x,10), round(this.y + vector.y,10));
	};
	
	this.subVector = function(vector){
		return new Point(round(this.x -( vector.x),10), round(this.y - ( vector.y ),10));
	};
	
	this.convertToLatLng = function(){
		return new gmap.LatLng(this.y, this.x);
	};
}

Point.latLngToPoint = function(latLng){
	return new Point(latLng.lng(),latLng.lat());
};

//to verify the file is loaded
loaded.tools.push('Point');