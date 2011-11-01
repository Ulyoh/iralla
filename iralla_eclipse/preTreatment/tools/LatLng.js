/**
 * @author Yoh
 */
gmap.LatLng.prototype.convertToPoint = function(){
	return new Point(this.lng(),this.lat());
};

//to verify the file is loaded
loaded.tools.push('LatLng.js');
