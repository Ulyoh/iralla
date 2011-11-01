

function showArrowsOnMap() {
	
	var arrowsListOrderByLayer = map.arrowsListFromBdd;
	var path;
	var latAndLng;
	var arrowsList;
	for(key in arrowsListOrderByLayer){
		arrowsList = arrowsListOrderByLayer[key];
		for(var j = 0; j < arrowsList.length; j++) {
			if(typeof(			map.arrowsListFromBdd[key][j].polyline) == "undefined") {
				//create the path:
				//arrowsList[j].path change to an array:
				var arrayOfStringPoints = arrowsList[j].path.split(',');
				path = [];
				for( k = 0; k < arrayOfStringPoints.length; k++){
					latAndLng = arrayOfStringPoints[k].split(' ');
					path.push(new gmap.LatLng(latAndLng[0], latAndLng[1]));
				}
				
				//create the polyline to make the arrow:
				opts = {
					map: map,
					path: path,
					strokeColor: "#" + arrowsList[j].color,
					strokeOpacity: 0.5,
					strokeWeight: 1,
					zIndex: 2000
				};
				map.arrowsListFromBdd[key][j].polyline = new gmap.Polyline(opts);
			}
			else{
				map.arrowsListFromBdd[key][j].polyline.setMap(map);
			}
		}
	}
}

function hideArrowsOnMap(){
	var arrowsListOrderByLayer = map.arrowsListFromBdd;
	var arrowsList;
	for(key in arrowsListOrderByLayer){
		arrowsList = arrowsListOrderByLayer[key];
		for(var j = 0; j < arrowsList.length; j++) {
			if(typeof( map.arrowsListFromBdd[key][j].polyline) != "undefined"){
				map.arrowsListFromBdd[key][j].polyline.setMap(null);
			}
		}
	}
}

loaded.findFlowDirection.push("showArrowsOnMap.js");
