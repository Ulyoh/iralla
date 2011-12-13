/**
 * @author Yoh
 */
var gmap = google.maps;
/*
function();

if(){
	
}*/
//add a fonction to a listener on the map, if the listener doesn t exist, it s created:
// itsArgs[0] = this of newFunction
//to pass an event to the fonction used : "eVeNt:nameOfEvent"
//example : "eVeNt:MouseEvent.latLng"
function addFunctionsToListener(event, newFunction, itsArgs){
	var self = this;
	if (!(this.listeners)){
		this.listeners = {};
	}
		
	if ((this.listeners._id === null)){
		this.listeners._id = 0;
	}
	
	//if there is no listeners created for this event:
	if (!(this.listeners[event])) {
		//this.listeners[event] = {};

		//set of the listener:
		this.listeners[event] = gmap.event.addListener(self, event, function(MouseEvent){
			var l = this.listeners[event]._functions.length;
			//for each function to execute with the event:
			for (var i = 0; i < l; i++) {
				var myFunction = this.listeners[event]._functions[i];
				
				//copy the arguments corresponding to the function
				var args = [];
				for (var k = 0; k < this.listeners[event]._args[i].length; k++){
					args[k] = this.listeners[event]._args[i][k];
				}
				for (var j = 0; j < args.length; j++){
					if (typeof(args[j]) == "string"){
						if (args[j].slice(0,6) == "eVeNt:"){
							args[j]  = eval( args[j].slice(6));
						}
					}
				}

				myFunction.apply(args[0], args.slice(1));
			}
			//end for listener
		});
		this.listeners[event]._functions = [];
		this.listeners[event]._args = [];
		this.listeners[event]._id = [];
	}
		
	this.listeners[event]._functions.push(newFunction);
	if (typeof(itsArgs) == 'undefined'){
		itsArgs = "";
	}
	this.listeners[event]._args.push(itsArgs);
	this.listeners[event]._id.push(this.listeners._id);

	return this.listeners._id++;
}
	
//remove a function call by an event on the map:
function removeFunctionsToListeners(idOfListener, event){
	var buffer = {};
	var l = this.listeners[event]._functions.length;
	// if the event parameter is set									//should make the possibility without event parameter
	if (event){
		//for all the functions set for the event:
		for (var i = 0; i < l; i++) {
			//find the one which has the id passed in parameter:					
			if (this.listeners[event]._id[i] == idOfListener){
				//remove this function:
				var bufferFunction = this.listeners[event]._functions.pop();
				var bufferArg = this.listeners[event]._args.pop();
				var bufferId = this.listeners[event]._id.pop();
				if (i != (l - 1)) {
					this.listeners[event]._functions.splice(i, 1, bufferFunction);
					this.listeners[event]._args.splice(i, 1, bufferArg);
					this.listeners[event]._id.splice(i, 1, bufferId);
				}
				//delete this.listeners[event].functions[i];
				break;
			}
		}
	}
	if (this.listeners[event]._functions.length === 0){
		gmap.event.removeListener(this.listeners[event]);
		delete this.listeners[event];
	}
}

function passArgumentsAgain(args){
	var array = [];
	for (var i = 0; i < args.length; i++){
		array[i] = args[i];
	}
	return array.join(',');
}

function removeEmptyLinesOfTable(table) {
	//remove the fields of the table that are empty:
	var trs = table.getElementsByTagName('tr');
	
	for ( var i = 0; i < trs.length; i++){
		if (trs[i].childNodes[0].childNodes.length === 0){
			trs[i].parentNode.removeChild(trs[i]);
			i--;
		}
	}
}

function removeNode(node){
	if(node !== null){
		node.parentNode.removeChild(node);
	}
}

function removeNodeById(id){
	removeNode(document.getElementById(id));
}

function appendAsFirstChild(parent, child){
	if(parent.firstChild){
		parent.insertBefore(child,parent.firstChild);
	}
	else{
		parent.appendChild(child);
	}
}

function getEltById(id){
	return document.getElementById(id);
}

function hideNodeById(id){
	getEltById(id).style.display = "none";
}

function hideNodesById(ids){
	for(var i = 0; i < ids.length; i++){
		hideNodeById(ids[i]);
	}
}

function showBlockById(id){
	getEltById(id).style.display = "block";	
}

function showBlocksById(ids){
	for(var i = 0; i < ids.length; i++){
		showBlockById(ids[i]);
	}
}

function createTableInElt(elt){
	var thead = document.createElement("thead");
	elt.appendChild(thead);
		
	var tbody = document.createElement("tbody");
	elt.appendChild(tbody);
		
	var tfoot = document.createElement("tfoot");
	elt.appendChild(tfoot);
	
	var tableParts = {
		thead: thead,
		tbody: tbody,
		tfoot: tfoot
	};
	
	return tableParts;
}
/*
 * more = {
 * 		lineTitle
 * 		lineId
 * 		lineClass
 * 		innerHTML
 * 		childs[]
 * };
 */
function addLineWithOneCellInTable(table, more, functionToExec){
	var newLine = document.createElement("tr");
	var newCell = document.createElement("td");
	table.appendChild(newLine);
	newLine.appendChild(newCell);
	
	if (typeof(more.innerHTML) != 'undefined') {
		newCell.innerHTML = more.innerHTML;
	}
	
	if (typeof(more.title) != 'undefined') {
		newLine.title = more.lineTitle;
	}
	if(typeof(more.lineClass) != 'undefined'){
		newLine.className = more.lineClass;
	}
	if(typeof(more.childs) != 'undefined'){
		for(var i = 0; i < more.childs.length; i++){
			newCell.appendChild(more.childs[i]);
		}
	}
	
	if(typeof(functionToExec) != 'undefined'){
		functionToExec(more, table, newLine, newCell);
	}
	
	var created = {
		line: newLine,
		cell: newCell
	};
	return created;
}

/*
 * more = {
 * 		lineTitle
 * 		lineId
 * 		lineClass
 * 		childsInCells[]
 * 		classCell
 * };
 */

function addLineInTable(table, more, functionToExec){
	var newLine = document.createElement("tr");
	
	if (typeof(more.title) != 'undefined') {
		newLine.title = more.lineTitle;
	}
	if(typeof(more.lineClass) != 'undefined'){
		newLine.className = more.lineClass;
	}
	if(typeof(more.childsInCells) != 'undefined'){
		var newCell;
		for(var i = 0; i < more.childsInCells.length; i++){
			newCell = document.createElement("td");
			newCell.appendChild(more.childsInCells[i]);
			newLine.appendChild(newCell);
			if(typeof more.childsInCells[i].classCell != 'undefined' ){
				newCell.className = more.childsInCells[i].classCell;
			}
		}
	}
	
	if(typeof(functionToExec) != 'undefined'){
		functionToExec(more, table, newLine, newCell);
	}
	
	var created = {
		line: newLine,
		cell: newCell
	};
	
	table.appendChild(newLine);
	return created;
}

function newButton(more){
	var newButtonVar = document.createElement("button");
	newButtonVar.setAttribute("type", "button");
	newButtonVar.setAttribute('id', more.id);
	newButtonVar.setAttribute('class', more.myClass);
	return newButtonVar;
}

function isInsideArray(elt, array){
	for(var i = 0; i < array.length; i++){
		if(array[i] == elt)
			return true;
	}
	return false;
}

isInArray = isInsideArray;
