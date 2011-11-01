/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function mainShowHideArrows(){
	var toShow;
	if ((typeof(arrayOfBusLines.arrowVisible) == 'undefined') || (arrayOfBusLines.arrowVisible == false)){
		toShow = true;
		arrayOfBusLines.arrowVisible = true;
	}
	else{
		toShow = false;
		arrayOfBusLines.arrowVisible = false;
	}

	for (var i =0; i < arrayOfBusLines.length; i++){
		if (typeof(arrayOfBusLines[i].sections) != 'undefined'){
			for(var j =0; j < arrayOfBusLines[i].sections.length; j++){
				if (typeof(arrayOfBusLines[i].sections[j].arrayOfArrows) != 'undefined'){
					for(var k =0; k < arrayOfBusLines[i].sections[j].arrayOfArrows.length; k++){
						if ( toShow == true ){
							arrayOfBusLines[i].sections[j].arrayOfArrows[k].setMap(map);
						}
						else{
							arrayOfBusLines[i].sections[j].arrayOfArrows[k].setMap(null);
						}

					}
				}
			}
		}
	}

}
loaded.showHideArrows.push('mainShowHideArrows.js');