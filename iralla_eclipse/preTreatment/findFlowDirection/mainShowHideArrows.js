/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function mainShowHideArrows(){
	var toShow;
	if ((typeof(SubMap._busLinesArray.arrowVisible) == 'undefined') || (SubMap._busLinesArray.arrowVisible == false)){
		toShow = true;
		SubMap._busLinesArray.arrowVisible = true;
	}
	else{
		toShow = false;
		SubMap._busLinesArray.arrowVisible = false;
	}

	for (var i =0; i < SubMap._busLinesArray.length; i++){
		if (typeof(SubMap._busLinesArray[i].sections) != 'undefined'){
			for(var j =0; j < SubMap._busLinesArray[i].sections.length; j++){
				if (typeof(SubMap._busLinesArray[i].sections[j].arrayOfArrows) != 'undefined'){
					for(var k =0; k < SubMap._busLinesArray[i].sections[j].arrayOfArrows.length; k++){
						if ( toShow == true ){
							SubMap._busLinesArray[i].sections[j].arrayOfArrows[k].setMap(map);
						}
						else{
							SubMap._busLinesArray[i].sections[j].arrayOfArrows[k].setMap(null);
						}

					}
				}
			}
		}
	}

}
loaded.showHideArrows.push('mainShowHideArrows.js');