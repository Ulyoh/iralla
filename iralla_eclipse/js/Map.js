/**
 * @author Yoh
 */
gmap.Map.prototype.addFunctionsToListener = addFunctionsToListener;		//see commonsFunctions.js
gmap.Map.prototype.removeFunctionsToListeners = removeFunctionsToListeners;		//see commonsFunctions.js
 
/**
 * 		method : convertLatLngToPixelCoord
 * 
 * to extend Map
 * 
 * @param {Object} latLng
 * @return {Object} point
 */

gmap.Map.prototype.convertLatLngToPixelCoord = function(latLng){
	var NE = this.getBounds().getNorthEast();
	var SW = this.getBounds().getSouthWest();
	
	var lat = latLng.lat();
	var lng = latLng.lng();
	
	//delta value from the NW point:
	lat = NE.lat() - lat;
	lng = lng - SW.lng();
	
	divNode = this.getDiv();
	
	var x = lng * divNode.offsetWidth /( NE.lng() - SW.lng() ) + divNode.offsetLeft;
	var y = lat * divNode.offsetHeight / ( NE.lat() - SW.lat() ) + divNode.offsetTop;
	
	return new gmap.Point(x,y);
};

/**
 * 		method : showBlockToPos
 * 		NOT USED FOR NOW
 * to extend Map
 * 
 * @param {Object} blockId
 * @param {Object} position
 */
gmap.Map.prototype.showBlockToPos = function(blockId, position){
	var coord = this.convertLatLngToPixelCoord(position );
	var blockNode = document.getElementById(blockId);
	if ((coord.x >=0) && (coord.y >= 0)){
		blockNode.style.left = coord.x + "px";
		blockNode.style.top= coord.y + "px";
		blockNode.style.display= "block";
	}
	else{
		blockNode.style.display= "none";
	}
};