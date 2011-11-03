/**
 * @author Yoh
 */
function mainUpABusLine(){
	if (document.getElementById("button_FindFlowDirection").state == "activate"){
		mainFindFlowDirection();
		document.getElementById("button_FindFlowDirection").state = "mustBeReActivate";
	}
		
	for( var i = 0; i < SubMap._busStationArray.length; i++){
		SubMap._busStationArray[i].idOfListenerOfUp = SubMap._busStationArray[i].addFunctionsToListener('click',upTheBusLine,[SubMap._busStationArray[i]]);
	}
	
	if (document.getElementById("button_FindFlowDirection").state == "mustBeReActivate"){
		mainFindFlowDirection();
	}
}

function upTheBusLine(){
	for (var i = 0; i < SubMap._busStationArray.length; i++) {
		SubMap._busStationArray[i].setOptions({zIndex:1});
		SubMap._busStationArray[i].removeFunctionsToListeners(SubMap._busStationArray[i].idOfListenerOfUp,'click');
	}
	
	this.setOptions({zIndex:10});
}


loaded.upABusLine.push("mainUpABusLineCreation.js");
