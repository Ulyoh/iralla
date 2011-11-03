/**
 * @author Yoh
 */
function mainUpABusLine(){
	if (document.getElementById("button_FindFlowDirection").state == "activate"){
		mainFindFlowDirection();
		document.getElementById("button_FindFlowDirection").state = "mustBeReActivate";
	}
		
	for( var i = 0; i < arrayOfBusLines.length; i++){
		arrayOfBusLines[i].idOfListenerOfUp = arrayOfBusLines[i].addFunctionsToListener('click',upTheBusLine,[arrayOfBusLines[i]]);
	}
	
	if (document.getElementById("button_FindFlowDirection").state == "mustBeReActivate"){
		mainFindFlowDirection();
	}
}

function upTheBusLine(){
	for (var i = 0; i < arrayOfBusLines.length; i++) {
		arrayOfBusLines[i].setOptions({zIndex:1});
		arrayOfBusLines[i].removeFunctionsToListeners(arrayOfBusLines[i].idOfListenerOfUp,'click');
	}
	
	this.setOptions({zIndex:10});
}


loaded.upABusLine.push("mainUpABusLineCreation.js");
