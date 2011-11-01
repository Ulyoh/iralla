/**
 * @author Yoh
 */

mainDataBaseCreation = function(){
	var ne = new gmap.LatLng(-1.99, -79.70);
	var sw = new gmap.LatLng(-2.29, -80.15);
	
	var bounds = new gmap.LatLngBounds(sw, ne);
	
	//sur la largeur : 42 km diviser par 50 m = 840
	//sur la hauteur : 36 km diviser par 50 m = 725
	
	Database(map, bounds, 840, 600);
	
	//send datas to the server:
	SendDatasToDataBase(0, arrayOfBusLines.length - 1);
};

loaded.dataBaseCreation.push('mainDataBaseCreation');

