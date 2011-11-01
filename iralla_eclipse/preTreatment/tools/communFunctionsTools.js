
function extractPointsOfVertexList(vertexList, latO, lngO){
	var pointsTab = [];
	for (var k = 0; k < vertexList.length; k++) {
		var lat = vertexList[k].getAttribute("lat");
		var lng = vertexList[k].getAttribute("lng");
		lat = lat.replace(",", ".");
		lng = lng.replace(",", ".");
		lat = parseFloat(lat) + latO;
		lng = parseFloat(lng) + lngO;
		var point = new gmaps.LatLng(lat, lng);
		pointsTab.push(point);
	}
	return pointsTab;
}

function haversinDistance(pt1, pt2) {
	function rad(x){
		return x * Math.PI / 180;
	}
  var R = 6371; // earth's mean radius in km
  var deltaLat  = rad(pt2.lat() - pt1.lat());
  var deltaLng = rad(pt2.lng() - pt1.lng());

  var a = Math.sin(deltaLat/2) * Math.sin(deltaLat/2) +
          Math.cos(rad(pt1.lat())) * Math.cos(rad(pt2.lat())) * 
		  Math.sin(deltaLng/2) * Math.sin(deltaLng/2);
  var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
  var d = R * c;

  return d.toFixed(3);
}



String.prototype.removeFirstLetter = function(){
	return this.substring(1, this.length);
};


 //to verify the file is loaded
loaded.tools.push('communFunctionsTools.js');



