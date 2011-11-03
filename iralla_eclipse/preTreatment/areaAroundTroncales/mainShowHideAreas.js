
function mainShowHideAreas(){
	var onMap = troncalAreas[0].getMap();
	for( var i = 0; i < troncalAreas.length; i++){
		if(onMap == map){
			troncalAreas[i].setMap(null);
		}
		else{
			troncalAreas[i].setMap(map);
		}
	}
	
	/*
	//show the part of the busline inside the main area:
	for (i = 0; i < SubMap._busStationArray.length; i++){
		SubMap._busStationArray[i]
		
		
	}
	*/
	
}



loaded.ShowHideAreas.push('mainShowHideAreas.js');


