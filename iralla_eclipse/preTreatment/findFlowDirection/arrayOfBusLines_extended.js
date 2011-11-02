/**
 * @author Yoh
 */
arrayOfBusLines.enableAddArrow = function(){
	//deselect the previous function:
	//virtual press the deselect button:
	if (document.getElementById('button_deselect') !== null)
		document.getElementById('button_deselect').click();
	
	for(var i = 0; i < this.length; i++){
		this[i].enableAddArrow();
	}

	//add a button to deselect "AddArrow" function:
	if (document.getElementById('button_deselect') === null) {
		newField = newLineOfTablePreTreatment();
		var button_deselect = document.createElement('button');
		button_deselect.setAttribute('id', 'button_deselect');
		button_deselect.innerHTML = 'deselect';
		newField.appendChild(button_deselect);
	}
	document.getElementById('button_deselect').setAttribute('onclick', 
			"{arrayOfBusLines.disableAddArrow(); " +
			"removeNodeById('button_deselect');}" +
			"removeEmptyLinesOfTable(document.getElementById('tablePreTreatment'));"
			);
	
};

arrayOfBusLines.disableAddArrow = function(){
	for(var i = 0; i < this.length; i++){
		if ((typeof(this[i].idOfListenerOfAddArrow) != 'undefined') && (this[i].idOfListenerOfAddArrow >= 0 ))
			this[i].disableAddArrow();
	}
};

arrayOfBusLines.enableAddBoundary = function(){
	//deselect the previous function:
	//virtual press the deselect button:
	if (document.getElementById('button_deselect') !== null)
		document.getElementById('button_deselect').click();
	
	for(var i = 0; i < this.length; i++){
		this[i].enableAddBoundary();
	}
	
	//add a button to deselect "AddBoundary" function:
	if (document.getElementById('button_deselect') === null) {
		newField = newLineOfTablePreTreatment();
		var button_deselect = document.createElement('button');
		button_deselect.setAttribute('id', 'button_deselect');
		button_deselect.innerHTML = 'deselect';
		newField.appendChild(button_deselect);
	}
	document.getElementById('button_deselect').setAttribute('onclick', 
			"{arrayOfBusLines.disableAddBoundary(); " +
			"removeNodeById('button_deselect');}" +
			"removeEmptyLinesOfTable(document.getElementById('tablePreTreatment'));"
			);
		
};

arrayOfBusLines.disableAddBoundary = function(){
	for(var i = 0; i < this.length; i++){
		if ((typeof(this[i].idOfListenerOfAddBoundary) != 'undefined') && (this[i].idOfListenerOfAddBoundary >= 0 ))
			this[i].disableAddBoundary();
	}
};

arrayOfBusLines.enableRemoveArrows = function(){
	//deselect the previous function:
	//virtual press the deselect button:
	if (document.getElementById('button_deselect') !== null)
		document.getElementById('button_deselect').click();
	
	for(var i = 0; i < this.length; i++){
		this[i].enableRemoveArrows();
	}

	//add a button to deselect "RemoveArrows" function:
	if (document.getElementById('button_deselect') === null) {
		newField = newLineOfTablePreTreatment();
		var button_deselect = document.createElement('button');
		button_deselect.setAttribute('id', 'button_deselect');
		button_deselect.innerHTML = 'deselect';
		newField.appendChild(button_deselect);
	}
	document.getElementById('button_deselect').setAttribute('onclick', 
			"{arrayOfBusLines.disableRemoveArrows(); " +
			"removeNodeById('button_deselect');}" +
			"removeEmptyLinesOfTable(document.getElementById('tablePreTreatment'));"
			);
	
};

arrayOfBusLines.disableRemoveArrows = function(){
	for(var i = 0; i < this.length; i++){
		if ((typeof(this[i].idOfListenerOfRemoveArrows) != 'undefined') && (this[i].idOfListenerOfRemoveArrows >= 0 ))
			this[i].disableRemoveArrows();
	}
};

arrayOfBusLines.enableFindFlowAuto = function(){
	//deselect the previous function:
	//virtual press the deselect button:
	if (document.getElementById('button_deselect') !== null)
		document.getElementById('button_deselect').click();
	
	for(var i = 0; i < this.length; i++){
		this[i].enableFindFlowAuto();
	}

	//add a button to deselect "FindFlowAuto" function:
	if (document.getElementById('button_deselect') === null) {
		newField = newLineOfTablePreTreatment();
		var button_deselect = document.createElement('button');
		button_deselect.setAttribute('id', 'button_deselect');
		button_deselect.innerHTML = 'deselect';
		newField.appendChild(button_deselect);
	}
	document.getElementById('button_deselect').setAttribute('onclick',
			"{arrayOfBusLines.disableFindFlowAuto(); " +
			"removeNodeById('button_deselect');}" +
			"removeEmptyLinesOfTable(document.getElementById('tablePreTreatment'));"
			);
};

arrayOfBusLines.disableFindFlowAuto = function(){
	for(var i = 0; i < this.length; i++){
		if ((typeof(this[i].idOfListenerOfFindFlowAuto) != 'undefined') && (this[i].idOfListenerOfFindFlowAuto >= 0 )){
			this[i].disableFindFlowAuto();
		}
	}
};


arrayOfBusLines.enableFindFlowFromXmlArrows = function(){
	//deselect the previous function:
	//virtual press the deselect button:
	if (document.getElementById('button_deselect') !== null){
		document.getElementById('button_deselect').click();
	}
	
	for(var i = 0; i < this.length; i++){
		this[i].enableFindFlowFromXmlArrows();
	}

	//add a button to deselect 'find flow from xml arrows' function:
	if (document.getElementById('button_deselect') === null) {
		newField = newLineOfTablePreTreatment();
		var button_deselect = document.createElement('button');
		button_deselect.setAttribute('id', 'button_deselect');
		button_deselect.innerHTML = 'deselect';
		newField.appendChild(button_deselect);
	}
	document.getElementById('button_deselect').setAttribute('onclick', 
			"{arrayOfBusLines.disableFindFlowFromXmlArrows();" +
			"removeNodeById('button_deselect');}" +
			"removeEmptyLinesOfTable(document.getElementById('tablePreTreatment'));"
			);
};

arrayOfBusLines.disableFindFlowFromXmlArrows = function(){
	for(var i = 0; i < this.length; i++){
		if ((typeof(this[i].idOfListenerOfFindFlowFromXmlArrows) != 'undefined') && (this[i].idOfListenerOfFindFlowFromXmlArrows >= 0 )){
			this[i].disableFindFlowFromXmlArrows();
		}
	}
};

arrayOfBusLines.enableReverseFlow = function(){
	//deselect the previous function:
	//virtual press the deselect button:
	if (document.getElementById('button_deselect') !== null){
		document.getElementById('button_deselect').click();
	}
	
	for(var i = 0; i < this.length; i++){
		this[i].enableReverseFlow();
	}

	//add a button to deselect 'find flow from xml arrows' function:
	if (document.getElementById('button_deselect') === null) {
		newField = newLineOfTablePreTreatment();
		var button_deselect = document.createElement('button');
		button_deselect.setAttribute('id', 'button_deselect');
		button_deselect.innerHTML = 'deselect';
		newField.appendChild(button_deselect);
	}
	document.getElementById('button_deselect').setAttribute('onclick', 
			"{arrayOfBusLines.disableReverseFlow();" +
			"removeNodeById('button_deselect');}" +
			"removeEmptyLinesOfTable(document.getElementById('tablePreTreatment'));"
			);
};

arrayOfBusLines.disableReverseFlow = function(){
	for(var i = 0; i < this.length; i++){
		if ((typeof(this[i].idOfListenerOfReverseFlow) != 'undefined') && (this[i].idOfListenerOfReverseFlow >= 0 )){
			this[i].disableReverseFlow();
		}
	}
};

arrayOfBusLines.enableAddBidirectionalArrows = function(){
	//deselect the previous function:
	//virtual press the deselect button:
	if (document.getElementById('button_deselect') !== null){
		document.getElementById('button_deselect').click();
	}
	
	for(var i = 0; i < this.length; i++){
		this[i].enableAddBidirectionalArrows();
	}

	//add a button to deselect "AddBidirectionalArrows" function:
	if (document.getElementById('button_deselect') === null) {
		newField = newLineOfTablePreTreatment();
		var button_deselect = document.createElement('button');
		button_deselect.setAttribute('id', 'button_deselect');
		button_deselect.innerHTML = 'deselect';
		newField.appendChild(button_deselect);
	}
	document.getElementById('button_deselect').setAttribute('onclick', "{" +
			"arrayOfBusLines.disableAddBidirectionalArrows(); " +
			"removeNodeById('button_deselect');}" +
			"removeEmptyLinesOfTable(document.getElementById('tablePreTreatment'));"
			);
};

arrayOfBusLines.disableAddBidirectionalArrows = function(){
	for(var i = 0; i < this.length; i++){
		if ((typeof(this[i].idOfListenerOfAddBidirectionalArrows) != 'undefined') && (this[i].idOfListenerOfAddBidirectionalArrows >= 0 )){
			this[i].disableAddBidirectionalArrows();
		}
	}
};

arrayOfBusLines.findFlowOfAllBusLines = function(index){
	if (( typeof(map.allArrowsFromFileShown) == 'undefined' ) || ( map.allArrowsFromFileShown === false )){
		showArrowsOnMap();
		map.allArrowsFromFileShown = true;
	}

	var infos = getInfosPreBoxNode();
	
	//show the progression:
	if (document.getElementById('progression') === null) {
		var progression = document.createElement('p');
		progression.setAttribute('id', 'progression');
		infos.appendChild(progression);
	}
	var lastIndex = this.length - 1;
	document.getElementById('progression').innerHTML = index + ' / ' + lastIndex + 'bus lines processed <br\> bus lines for which the flow could not be determinated:';

	//in case the flow could not be determinated:
	if (this[index].findFlow(false) === false){
		//show the name of the bus line:
		var newLine = document.createElement('div');
		newLine.setAttribute('id', 'div' + this.index);
		newLine.innerHTML = index + ') ' + this[index].name + 'click here to see the bus line';
		document.getElementById('infos').appendChild(newLine);
		//make it clikcable to show only this bus line in the map:
		newLine.setAttribute('onclick', 'arrayOfBusLines.showOnlyOneBusLine(' + index + ');');
		//give the possibility to remove it from the arrayOfBusLines:
		
	}
	
	index++;
	if(index < this.length){
		setTimeout(function(){arrayOfBusLines.findFlowOfAllBusLines( ' + index + ' )},100);
	}
};


arrayOfBusLines.showOnlyOneBusLine = function(index){
	for ( var i = 0; i <  this.length; i++){
		if ( i != index){
			this[i].setMap(null);
		}
		else{
			this[i].setOptions({
				map: map,
				strokeOpacity: 1
			});
		}
	}
	
	//create a button to show all the buslines:
	if (document.getElementById('button_show_all_bus_lines') === null) {
		newField = newLineOfTablePreTreatment();
		var button_show_all_bus_lines = document.createElement('button');
		button_show_all_bus_lines.setAttribute('id', 'button_show_all_bus_lines');
		button_show_all_bus_lines.innerHTML = 'showAllBusLines';
		newField.appendChild(button_show_all_bus_lines);
		button_show_all_bus_lines.setAttribute('onclick',
				"arrayOfBusLines.showAllBusLine();" +
				"removeNodeById(this);" +
				"removeEmptyLinesOfTable(document.getElementById('tablePreTreatment'));"
				);
	}
	
};

arrayOfBusLines.showAllBusLine = function(index){
	for (var i = 0; i < this.length; i++) {
		this[i].setOptions({
			map: map,
			strokeOpacity: 0.5
		});
	}
};

arrayOfBusLines.saveFlowsInDatabase = function(){

	//create a JSON object to send to the database:
	var datasToSend = [];
	var oneBusLineFlow;

	//for each bus line:
	for (var i = 0; i < arrayOfBusLines.length; i++){

		//if sections, ie: flows have been determinated
		if (typeof(arrayOfBusLines[i].sections) != 'undefined'){
			//record the id:
			oneBusLineFlow.id = arrayOfBusLines[i].id;
			//init to record the flows:
			oneBusLineFlow.flows = '';

			//for each section:
			for(var j = 0; j < arrayOfBusLines[i].sections.length; j++){
				//if arrows in the section:
				if (typeof(arrayOfBusLines[i].sections[j].arrayOfArrows) != 'undefined'){
					//record the flow:
					oneBusLineFlow.flows += ' ' + arrayOfBusLines[i].sections[j].arrayOfArrows[0].flow;
				}
			}

			oneBusLineFlow.boundaries = '';

			if (typeof(arrayOfBoundaries) != 'undefined'){
				for( j = 0; j < arrayOfBusLines[i].arrayOfBoundaries.length; j++){

					oneBusLineFlow.boundaries += ',' + arrayOfBusLines[i].arrayOfBoundaries[j].center.lat() + ' ' +
						arrayOfBusLines[i].arrayOfBoundaries[j].center.lng();
				}
				oneBusLineFlow.boundaries = oneBusLineFlow.boundaries.removeFirstLetter();
			}
			datasToSend.push(oneBusLineFlow);
		}
	}


	//send datas:
	


}

arrayOfBusLines.showFlows = function(){
	for(var i = 0; i < arrayOfBusLines.length; i++){
		arrayOfBusLines[i].showFlow();
	}
}

arrayOfBusLines.hideFlows = function(){
	for(var i = 0; i < arrayOfBusLines.length; i++){
		arrayOfBusLines[i].hideFlow();
	}
}

loaded.findFlowDirection.push('arrayOfBusLines_extended.js');

