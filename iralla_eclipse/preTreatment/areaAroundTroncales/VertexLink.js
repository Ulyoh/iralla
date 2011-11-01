/**
 * @author Yoh
 */

 function VertexLink(index, point, previous, next){
 	this.index = index;
 	this.latLng = point.convertToLatLng();
	this.point = point;
	this.previous = previous;
	this.next = next;
	this.type = "VertexLink";
 }

 
if (typeof(loaded.redCreation) != 'undefined'){
 	loaded.redCreation.push('AreaSurroundedPolyline.js');
}
if (typeof(loaded.makeVirtualsBusStation) != 'undefined') {
	loaded.makeVirtualsBusStation.push('AreaSurroundedPolyline.js');
}
 