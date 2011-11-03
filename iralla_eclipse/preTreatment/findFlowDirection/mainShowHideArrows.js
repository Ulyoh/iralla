/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function mainShowHideArrows(){
	var toShow;
	if ((typeof(SubMap._busStationArray.arrowVisible) == 'undefined') || (SubMap._busStationArray.arrowVisible == false)){
		toShow = true;
		SubMap._busStationArray.arrowVisible = true;
	}
	else{
		toShow = false;
		SubMap._busStationArray.arrowVisible = false;
	}

	for (var i =0; i < SubMap._busStationArray.length; i++){
		if (typeof(SubMap._busStationArray[i].sections) != 'undefined'){
			for(var j =0; j < SubMap._busStationArray[i].sections.length; j++){
				if (typeof(SubMap._busStationArray[i].sections[j].arrayOfArrows) != 'undefined'){
					for(var k =0; k < SubMap._busStationArray[i].sections[j].arrayOfArrows.length; k++){
						if ( toShow == true ){
							SubMap._busStationArray[i].sections[j].arrayOfArrows[k].setMap(map);
						}
						else{
							SubMap._busStationArray[i].sections[j].arrayOfArrows[k].setMap(null);
						}

					}
				}
			}
		}
	}

}
loaded.showHideArrows.push('mainShowHideArrows.js');