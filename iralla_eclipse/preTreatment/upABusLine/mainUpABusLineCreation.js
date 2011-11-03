/**
 * @author Yoh
 */
function mainUpABusLine(){
	if (document.getElementById("button_FindFlowDirection").state == "activate"){
		mainFindFlowDirection();
		document.getElementById("button_FindFlowDirection").state = "mustBeReActivate";
	}
		
	for( var i = 0; i < SubMap._busLinesArray.length; i++){
		SubMap._busLinesArray[i].idOfListenerOfUp = SubMap._busLinesArray[i].addFunctionsToListener('click',upTheBusLine,[SubMap._busLinesArray[i]]);
	}
	
	if (document.getElementById("button_FindFlowDirection").state == "mustBeReActivate"){
		mainFindFlowDirection();
	}
}

function upTheBusLine(){
	for (var i = 0; i < SubMap._busLinesArray.length; i++) {
		SubMap._busLinesArray[i].setOptions({zIndex:1});
		SubMap._busLinesArray[i].removeFunctionsToListeners(SubMap._busLinesArray[i].idOfListenerOfUp,'click');
	}
	
	this.setOptions({zIndex:10});
}


loaded.upABusLine.push("mainUpABusLineCreation.js");
