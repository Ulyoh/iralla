function findRutas(event){
	var address = document.getElementById('address');
	if (event.keyCode == 13) {
		//TODO: bug : suggestionListNode.firstChild.showChoosenRuta() does not work
		/*var suggestionListNode = document.getElementById('suggestionListNode');
		while(suggestionListNode.childNodes.length > 0){
			suggestionListNode.firstChild.showChoosenRuta();
		}*/
	}
	else {
		if (address.value !== '') {
			request({
				phpFileCalled: 'found_ruta_s_name.php',
				argumentsToPhpFile: 'q=' + address.value,
				callback: showRutasName,
				asynchrone: true
			});
		}
		else {
			var suggestionListNode = document.getElementById('suggestionListNode');
			suggestionListNode.style.display = 'none';
			
			//remove all the childs of suggestionNode
			while (suggestionListNode.firstChild) {
				suggestionListNode.removeChild(suggestionListNode.firstChild);
			}
		}
	}
}


function showRutasName(rutasList){
	if ((typeof(rutasList) != 'undefined') && (rutasList !== '')){
		rutasList = JSON.parse(rutasList);
		
		var suggestionListNode = document.getElementById('suggestionListNode');
		suggestionListNode.style.display = 'none';
		
		//remove all the childs of suggestionNode
		while (suggestionListNode.firstChild) {
			suggestionListNode.removeChild(suggestionListNode.firstChild);
		}
		
		for (var i = 0; i < rutasList.length; i++) {
			//create the list:
			var newLine = document.createElement("tr");
			var newNode = document.createElement("button");
			newNode.setAttribute('id', 'nodeShow' + suggestionListNode.nextId);
			suggestionListNode.nextId++;
			newNode.innerHTML = rutasList[i].name;
			newNode.rutasInfos = rutasList[i];
			newNode.showChoosenRuta = showChoosenRuta;
			newNode.setAttribute('class', 'line_choice');
			newLine.appendChild(newNode);
			suggestionListNode.appendChild(newLine);
			
			//on click on the node:
			newNode.setAttribute('onclick', 'this.showChoosenRuta();');
		}
					
		if (suggestionListNode.firstChild !== null) {
			suggestionListNode.style.display = 'block';
		}
	}
}

function showChoosenRuta(){
	var busLine = arrayOfBusLines.getItemById(this.rutasInfos.id);
	if (busLine === false) {
		var array = [];
		array.push(this.rutasInfos);
		map.addBusLinesFromDb(array);
		busLine = arrayOfBusLines.getItemById(this.rutasInfos.id);
	}
	else {
		busLine.setMap(map);
	}
	arrayOfBusLines.setOptionsToAll({
		strokeColor: 'default'
	});
	
	//TODO: DOES NOT WORK 17/04/2011
	busLine.setOptions({
		strokeColor: '#0060FA'
	});
	///////////////////
	
	var cleanLinesNode = document.getElementById('button_clean_lines');
	cleanLinesNode.style.display = 'inline';
	cleanLinesNode.linesIdAdded.push(busLine.id);
	this.parentNode.style.display = 'none';
	if (this.parentNode.parentNode.childNodes.length <= 1) {
		this.parentNode.parentNode.style.display = 'none';
	}
	this.parentNode.parentNode.removeChild(this.parentNode);
	document.getElementById('address').value = "";
}

function setupCleanLines(){

	//add a button to remove the lines on the map
	var cleanLinesNode = document.createElement('button');
	cleanLinesNode.setAttribute('id', 'button_clean_lines');
	cleanLinesNode.innerHTML = 'borrar rutas';
	cleanLinesNode.setAttribute('onclick', 'arrayOfBusLines.removePolylinesFromIds(this.linesIdAdded); this.linesIdAdded = []; this.style.display = "none"');
	document.getElementById('direction').appendChild(cleanLinesNode);
	cleanLinesNode.linesIdAdded =[];
}


/*
 * 
 *			//remove all the roads already shown:
			var id = suggestionListNode.firstChild.rutasInfos.id;
			arrayOfBusLines.removeOnePolylineFromId(id);
 * 
 * 
 * 
 */