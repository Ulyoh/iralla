/**
 * @author Yoh
 */


//to include a new javascript file
function includeJs(file) 
{ 
  var script = document.createElement('script'); 
  script.setAttribute('type','text/javascript'); 
  script.setAttribute('src',file); 
  document.getElementsByTagName('head')[0].appendChild(script); 
} 

//to include a new css file
function includeCss(file) 
{ 
	var css = document.createElement('link'); 
	css.setAttribute('href',file);
	css.setAttribute('title','Design'); 
	css.setAttribute('type','text/css');
	css.setAttribute('media','screen');
	css.setAttribute('rel','stylesheet');
	document.getElementsByTagName('head')[0].appendChild(css); 
}

var preTreatment = {
	current: null
};

function accessToPreTreatment(){

	map.removeAllBusLines();

	//constantes:
	radiusInMetre = 50;
	coeffRound = Math.pow(10, 10);
	findingArea = false;
	
	includeJs("preTreatment/infosBox.js");
	if(typeof(map.listenerIdWhenClickMap) != 'undefined'){
		map.removeFunctionsToListeners(map.listenerIdWhenClickMap, 'click');
	}
	map.listenerIdWhenClickMap = -1;
	
	//create the head menu of preTreatment:
	var headMenuPreTreatment = document.createElement('div');
	headMenuPreTreatment.setAttribute('id', 'headMenuPreTreatment');
	document.getElementsByTagName('body')[0].insertBefore(headMenuPreTreatment, document.getElementsByTagName('body').firstChild);
	headMenuPreTreatment.onclick = function(){
		showMenuPreTreatment();
	};
	
	//create the menu of preTreatment:
	var menuPreTreatment = document.createElement('div');
	menuPreTreatment.setAttribute('id', 'menuPreTreatment');
	document.getElementsByTagName('body')[0].insertBefore(menuPreTreatment, document.getElementsByTagName('body').firstChild);
	
	//table header (to access to the menu)
	var tableHeaderPreTreatment = document.createElement('table');
	tableHeaderPreTreatment.setAttribute('id', 'tableHeaderPreTreatment');
	var firstLine = document.createElement('tr');
	firstLine.setAttribute("class", "headerPreTreatment");
	var header = document.createElement('th');
	header.setAttribute("class", "headerPreTreatment");
	header.innerHTML = 'MENU';
	firstLine.appendChild(header);
	tableHeaderPreTreatment.appendChild(firstLine);
	headMenuPreTreatment.appendChild(tableHeaderPreTreatment);
	
	//the table:
	var tablePreTreatment = document.createElement('table');
	tablePreTreatment.setAttribute('id', 'tablePreTreatment');
	menuPreTreatment.appendChild(tablePreTreatment);
	
	//list of button inside the menu:
	newPretreatmentAccessButton('UpABusLine', 'up a Bus Line');
	newPretreatmentAccessButton('FindFlowDirection', 'determinate flow direction');
	newPretreatmentAccessButton('AreaAroundTroncales', 'create areas around Troncales');
	newPretreatmentAccessButton('LinkBusStationToTroncales', 'create link: bus lines / troncales');
	newPretreatmentAccessButton('MakeVirtualsBusStation', 'make virtual bus stations');
	newPretreatmentAccessButton('DataBaseCreation', 'create dataBase');
	newPretreatmentAccessButton('AddNewDatas', 'add new datas');
	newPretreatmentAccessButton('ShowHideAreas', 'show/hide areas');
	newPretreatmentAccessButton('SavingModifications', 'save');
	
	
	includeJs("preTreatment/findFlowDirection/gmap.Polyline_extend.js");
	
	//include tools:
	loaded = {};
	loaded.tools = [];
	includeJs("preTreatment/tools/Circle.js");
	includeJs("preTreatment/tools/LatLng.js");
	includeJs("preTreatment/tools/Line.js");
	includeJs("preTreatment/tools/Point.js");
	includeJs("preTreatment/tools/Segment.js");
	includeJs("preTreatment/tools/Vector.js");
	includeJs("preTreatment/tools/round.js");
	includeJs("preTreatment/tools/Polyline_extended.js");
	includeJs("preTreatment/tools/gmap.Circle_extended.js");
	includeJs("preTreatment/tools/communFunctionsTools.js");
	includeJs("preTreatment/tools/communFunctionsTools.js");
	
	//show list of all the bus lines saved in the servor:
	includeJs("preTreatment/showBusLinesList/mainShowBusLinesList.js");
	includeCss("preTreatment/preTreatment.css");
	
	//show selected bus lines on map:
	includeJs("preTreatment/showLinesOnMap/mainShowLinesOnMap.js");
	
	//show all bus stations on map:
	includeJs("preTreatment/showBusStationsOnMap/mainShowBusStationsOnMap.js");
	
	//show arrows:
	includeJs("preTreatment/findFlowDirection/Arrow.js");
	includeJs("preTreatment/findFlowDirection/gmap.Polyline_extend.js");
	
}

function newPretreatmentAccessButton(nameOfPretreatment, buttonName ){
	
	var newField = newLineOfTablePreTreatment();
	
	eval("var button_" + nameOfPretreatment + " = document.createElement('button');" );
	eval("button_" + nameOfPretreatment + ".setAttribute('id', 'button_" + nameOfPretreatment + "');");
	eval("button_" + nameOfPretreatment + ".innerHTML = '" + buttonName + "';");
	eval("button_" + nameOfPretreatment + ".style.width = '198px';");
	eval("button_" + nameOfPretreatment + ".setAttribute('onclick', 'load" + nameOfPretreatment + "FilesAndLaunch();');");
	newField.appendChild(eval('button_' + nameOfPretreatment));	
}

//put a polyline to the first plan
function loadUpABusLineFilesAndLaunch(){
	loaded.upABusLine = [];
	
	if(loaded.upABusLine.length < 1){
		includeJs("preTreatment/upABusLine/mainUpABusLineCreation.js");
	}
	
	setTimeout("launchUpABusLine()",500);
}

function launchUpABusLine(){
	if ((loaded.upABusLine.length < 1) || (loaded.tools.length < 10))
		setTimeout("launchUpABusLine()",500);
	else
		mainUpABusLine();
}

//create links between bus lines and troncales:
function loadLinkBusStationToTroncalesFilesAndLaunch(option){
	if (typeof(loaded.linkBusStationToTroncales) == 'undefined') {
		loaded.linkBusStationToTroncales = [];
	}
	
	if (loaded.linkBusStationToTroncales.length < 6){
		includeJs("preTreatment/linkBusStationToTroncales/Connection.js");
		includeJs("preTreatment/linkBusStationToTroncales/gmap.Map_extended.js");
		includeJs("preTreatment/linkBusStationToTroncales/gmap.Marker_extended.js");
		includeJs("preTreatment/linkBusStationToTroncales/gmap.Polyline_extended.js");
		includeJs("preTreatment/linkBusStationToTroncales/mainLinkBusStationToTroncalesCreation.js");
		includeJs("preTreatment/makeVirtualsBusStation/mainMakeVirtualsBusStationOld.js");
	}
	
	if (typeof(option) == 'undefined'){
		option = "";
	}
	setTimeout("launchMainLinkBusStationToTroncales('"+ option + "')",500);	
}

function launchMainLinkBusStationToTroncales(option){
		if ((loaded.linkBusStationToTroncales.length < 6) || (loaded.tools.length < 10)) {
			setTimeout("loadLinkBusStationToTroncalesFilesAndLaunch()",500);
		}
		else if ((option != 'ToDoBoundaries') && (option != 'ToDoVirtualBusStations')){
			mainLinkBusStationToTroncales();
		}
}

//set the direction of flow of the bus lines
function loadFindFlowDirectionFilesAndLaunch(){
	
	if (typeof(loaded.findFlowDirection) == 'undefined') {
		loaded.findFlowDirection = [];
	}
	
	if (loaded.findFlowDirection.length < 6){
		includeJs("preTreatment/findFlowDirection/mainFindFlowDirectionCreation.js");
		includeJs("preTreatment/findFlowDirection/Arrow.js");
		includeJs("preTreatment/findFlowDirection/gmap.Polyline_extend.js");
		includeJs("preTreatment/findFlowDirection/SubMap._busLinesArray_extended.js");
		includeJs("preTreatment/findFlowDirection/showArrowsOnMap.js");
	}
	
	//to handle the boundaries:
	if ((typeof(loaded.linkBusStationToTroncales) == 'undefined')||(loaded.linkBusStationToTroncales.length < 6)) {
		loadLinkBusStationToTroncalesFilesAndLaunch('ToDoBoundaries');
	}
	
	setTimeout("launchMainFindFlowDirection()",500);
}

function launchMainFindFlowDirection(){
		if ((loaded.findFlowDirection.length < 5) || (loaded.linkBusStationToTroncales.length < 6) || (loaded.tools.length < 10))
			setTimeout("loadFindFlowDirectionFilesAndLaunch()",500);
		else
			mainFindFlowDirection();
}

//data base creation
function loadDataBaseCreationFilesAndLaunch(){
	if (typeof(loaded.dataBaseCreation) == 'undefined') {
		loaded.dataBaseCreation = [];
	}
	
	if (loaded.dataBaseCreation.length < 3){
		includeJs("preTreatment/dataBaseCreation/mainDataBaseCreation.js");
		includeJs("preTreatment/dataBaseCreation/Database.js");
		includeJs("preTreatment/dataBaseCreation/SendDatasToDataBase.js");		
	}
	
	setTimeout("launchMainDataBaseCreation()",500);
}

function launchMainDataBaseCreation(){
		if ((loaded.dataBaseCreation.length < 3) || (loaded.tools.length < 10))
			setTimeout("loadDataBaseCreationFilesAndLaunch()",500);
		else
			mainDataBaseCreation();	
}

//to create areas around Troncales
function loadAreaAroundTroncalesFilesAndLaunch(){
	if (typeof(loaded.redCreation) == 'undefined') {
		loaded.redCreation = [];
	}
	
	if (loaded.redCreation.length < 4) {
		includeJs("preTreatment/areaAroundTroncales/mainAreaAroundTroncales.js");
		includeJs("preTreatment/areaAroundTroncales/AreaSurroundedPolyline.js");
		includeJs("preTreatment/areaAroundTroncales/Node.js");
		includeJs("preTreatment/areaAroundTroncales/VertexLink.js");
	}
	setTimeout("launchMainAreaAroundTroncales()",500);
}

function launchMainAreaAroundTroncales(){
	
		if ((loaded.redCreation.length < 4) || (loaded.tools.length < 10))
			setTimeout("loadAreaAroundTroncalesFilesAndLaunch()",500);
		else
			mainAreaAroundTroncales();
}

//makeVirtualsBusStation
//to create virtual bus stations:
function loadMakeVirtualsBusStationFilesAndLaunch(){
	includeJs("preTreatment/savingModifications/mainSavingModifications.js");
	
	if (typeof(loaded.makeVirtualsBusStation) == 'undefined') {
		loaded.makeVirtualsBusStation = [];
	}
	
	if (loaded.makeVirtualsBusStation.length < 6) {
		includeJs("preTreatment/makeVirtualsBusStation/mainMakeVirtualsBusStationOld.js");
		includeJs("preTreatment/makeVirtualsBusStation/mainMakeVirtualsBusStation.js");
		includeJs("preTreatment/makeVirtualsBusStation/gmap.Polyline_extended.js");
		includeJs("preTreatment/makeVirtualsBusStation/gmap.ArrayOfBusStation_extended.js");
		includeJs("preTreatment/areaAroundTroncales/AreaSurroundedPolyline.js");
		includeJs("preTreatment/areaAroundTroncales/VertexLink.js");
	}
	
	//to handle the boundaries:
	if ((typeof(loaded.linkBusStationToTroncales) == 'undefined')||(loaded.linkBusStationToTroncales.length < 6)) {
		loadLinkBusStationToTroncalesFilesAndLaunch('ToDoVirtualBusStations');
	}
	
	setTimeout("launchMainMakeVirtualsBusStation()",500);
}

function launchMainMakeVirtualsBusStation(){
	
		if ((loaded.makeVirtualsBusStation.length < 6) || (loaded.tools.length < 10))
			setTimeout("loadMakeVirtualsBusStationFilesAndLaunch()",500);
		else
			mainMakeVirtualsBusStation();
}

//add new datas
function loadAddNewDatasFilesAndLaunch(){
	if (typeof(loaded.addNewDatas) == 'undefined') {
		loaded.addNewDatas = [];
	}
	
	if (loaded.addNewDatas.length < 1) {
		includeJs("preTreatment/addNewDatas/mainAddNewDatas.js");
	}
	setTimeout("launchMainAddNewDatas()",500);
}

function launchMainAddNewDatas(){
	
		if ((loaded.addNewDatas.length < 1) || (loaded.tools.length < 10))
			setTimeout("loadAddNewDatasFilesAndLaunch()",500);
		else
			mainAddNewDatas();
}

//show / hide areas:
function loadShowHideAreasFilesAndLaunch(){
	loaded.ShowHideAreas = [];

	if(loaded.ShowHideAreas.length < 1){
		includeJs("preTreatment/areaAroundTroncales/mainShowHideAreas.js");
	}

	setTimeout("launchShowHideAreas()",500);
}

function launchShowHideAreas(){
	if ((loaded.ShowHideAreas.length < 1) || (loaded.tools.length < 10))
		setTimeout("launchShowHideAreas()",500);
	else
		mainShowHideAreas();
}

function loadSavingModificationsFilesAndLaunch(){
	if (typeof(loaded.savingModifications) == 'undefined') {
		loaded.savingModifications = [];
	}
	
	if (loaded.savingModifications.length < 1) {
		includeJs("preTreatment/savingModifications/mainSavingModifications.js");
	}
	setTimeout("launchMainSavingModifications()",500);
}

function launchMainSavingModifications(){
	
		if ((loaded.savingModifications.length < 1) || (loaded.tools.length < 10))
			setTimeout("loadSavingModificationsFilesAndLaunch()",500);
		else
			mainSavingModifications();
}

function launchMainShowBusStationsOnMap(){
	if (typeof(mainShowBusStationsOnMap) != 'undefined'){
		mainShowBusStationsOnMap();
	}else{
		setTimeout("launchMainShowBusStationsOnMap()",500);
	}
}

//handling of showing the menu:

function showMenuPreTreatment(){
	document.getElementById('menuPreTreatment').style.zIndex = 10;
	document.getElementById('headMenuPreTreatment').onclick = function(){hideMenuPreTreatment();};

}

function hideMenuPreTreatment(){
	document.getElementById('menuPreTreatment').style.zIndex = -1;
	document.getElementById('headMenuPreTreatment').onclick = function(){showMenuPreTreatment();};
}

function newLineOfTablePreTreatment(){
	//line model:
	var tablePreTreatment = document.getElementById('tablePreTreatment');
	var newLine = document.createElement('tr');
	var newField = document.createElement('td');
	newLine.appendChild(newField);
	var numLine = tablePreTreatment.childNodes.length + 1;
	newLine.setAttribute('id', 'line_' + numLine);
	tablePreTreatment.appendChild(newLine);
	
	return newField;
}
