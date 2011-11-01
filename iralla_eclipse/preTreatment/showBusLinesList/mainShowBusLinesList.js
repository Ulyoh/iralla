function mainShowBusLinesList() {
	//create the window where to show the result:
	if(document.getElementById('divListLinesBus') != null){
		removeNodeById('divListLinesBus');
	}
	var divListLinesBus = document.createElement('div');
	divListLinesBus.setAttribute('id', 'divListLinesBus');
	divListLinesBus.style.display = "none";
	
	//create the table inside the window:
	var tableListLineBus = document.createElement('table');
	tableListLineBus.setAttribute('id', 'tableListLineBus');
	divListLinesBus.appendChild(tableListLineBus);
	
	//create the first header for the table:
	var headerBusLineList1 = document.createElement('tr');
	headerBusLineList1.setAttribute('id', 'headerBusLineList11');
	tableListLineBus.appendChild(headerBusLineList1);
	
	//set the first header:
	var column0BusLineList = document.createElement('th');
	column0BusLineList.innerHTML = "";
	headerBusLineList1.appendChild(column0BusLineList);
	
	var column1BusLineList = document.createElement('th');
	column1BusLineList.innerHTML = "line bus name";
	headerBusLineList1.appendChild(column1BusLineList);
	
	var column2BusLineList = document.createElement('th');
	column2BusLineList.innerHTML = "type";
	headerBusLineList1.appendChild(column2BusLineList);
	
	var column3BusLineList = document.createElement('th');
	column3BusLineList.innerHTML = "in use";
	headerBusLineList1.appendChild(column3BusLineList);

	//create the second header for the table:
	var headerBusLineList2 = document.createElement('tr');
	headerBusLineList2.setAttribute('id', 'headerBusLineList21');
	tableListLineBus.appendChild(headerBusLineList2);
	
	//set the second header:
	column0BusLineList = document.createElement('th');
	var toShowButton = document.createElement('button');
	toShowButton.innerHTML = "to show";
	toShowButton.onclick = function(){mainShowLinesOnMap();};
	column0BusLineList.appendChild(toShowButton);
	headerBusLineList2.appendChild(column0BusLineList);
	
	column1BusLineList = document.createElement('th');
	column1BusLineList.innerHTML = 	
		"<div>" +
		"<input type='radio' name='allBusLines' value='unselect' checked='checked' onClick='updateSelection(this);'/><br />" +
		"<input type='radio' name='allBusLines' value='all' onClick='updateSelection(this);' /> all<br />" +
		"<input type='radio' name='allBusLines' value='none' onClick='updateSelection(this);'/> none " +
		"</div>";
	headerBusLineList2.appendChild(column1BusLineList);
	
	column2BusLineList = document.createElement('th');
	column2BusLineList.style.width = "80px";
	column2BusLineList.innerHTML = 
		"<div><input type='checkbox' name='mainLine' value='tronc' onClick='updateSelection(this);' /> Tron.<br />" +
		"<input type='checkbox' name='feeder' value='alim' onClick='updateSelection(this);'/> Alim.<br />" +
		"<input type='checkbox' name='other' value='other' onClick='updateSelection(this);'/> Other </div>";
	headerBusLineList2.appendChild(column2BusLineList);
	
	column3BusLineList = document.createElement('th');
	column3BusLineList.style.width = "80px";
	column3BusLineList.innerHTML =  	
		"<div><input type='radio' name='used' value='unSelect' checked='checked' onClick='updateSelection(this);'/> <br />" +
		"<input type='radio' name='used' value='inUse' onClick='updateSelection(this);'/> used<br />" +
		"<input type='radio' name='used' value='notInUse' onClick='updateSelection(this);'/> unused </div>";
	headerBusLineList2.appendChild(column3BusLineList);
	
	document.body.appendChild(divListLinesBus);

	//create the "button" to show/hide the bus line list:
	//which is a simple div colored:
	var button = document.createElement('div');
	button.setAttribute('id', 'showLinesButton');
	button.setAttribute('onclick', 'showHideDivListLinesBus();');
	button.style.display = "none";
	document.body.appendChild(button);

	
	setTimeout(askToServer,1000);
}

function showHideDivListLinesBus(){
	var divListLinesBus = document.getElementById('divListLinesBus');
	var button = document.getElementById('showLinesButton');
	if(divListLinesBus.style.display == 'none'){
		divListLinesBus.style.display = 'block';
		button.style.backgroundColor = '#AA2233';
		/*button.style.width = '7px';
		button.style.height = '20px';*/
	}
	else{
		divListLinesBus.style.display = 'none';
		button.style.backgroundColor = '#00DD00';
		/*button.style.width = '7px';
		button.style.height = '20px';*/
	}
}

function askToServer(){
		//ask to the db the list of the names of the bus lines:
	request({
		phpFileCalled: mysite + 'preTreatment/showBusLinesList/getBusListName.php',
		type: "",
		callback: showBusLinesList,
		asynchrone: true
	});

}

function showBusLinesList(answer){

	//ask to the database:
	var newLine;
	var tableListLineBus = document.getElementById("tableListLineBus");
	var column0BusLineList;
	var column1BusLineList;
	var column2BusLineList;
	var column3BusLineList;
	var checkbox;
	
	var parseAnswer = JSON.parse(answer);
	
	var test;
	
	for(var i = 0; i < parseAnswer.length; i++){
		//create a new line for the table:
		newLine = document.createElement('tr');
		newLine.setAttribute('id', 'newLine_' + i);
		tableListLineBus.appendChild(newLine);
		
		//set the new line:
		column0BusLineList = document.createElement('td');
		var checkbox0 = document.createElement('input');
		checkbox0.setAttribute('type', 'checkbox');
		checkbox0.setAttribute('class', 'selectToShow');
		checkbox0.setAttribute('myid', parseAnswer[i].id);
		checkbox0.checked = false;

		column0BusLineList.appendChild(checkbox0);
		newLine.appendChild(column0BusLineList);
		
		column1BusLineList = document.createElement('td');
		column1BusLineList.innerHTML = parseAnswer[i].name;
		column1BusLineList.setAttribute('class', 'column1BusLineList');
		newLine.appendChild(column1BusLineList);
		
		column2BusLineList = document.createElement('td');
		column2BusLineList.innerHTML = parseAnswer[i].type;
		column2BusLineList.setAttribute('class', 'column2BusLineList');
		newLine.appendChild(column2BusLineList);
		
		column3BusLineList = document.createElement('td');
		var checkbox3 = document.createElement('input');
		checkbox3.setAttribute('type', 'checkbox');
		if(parseAnswer[i].inuse === 1){
			checkbox3.checked = true;
		}
		else{
			checkbox3.checked = false;
		}
		column3BusLineList.appendChild(checkbox3);
		column3BusLineList.setAttribute('class', 'column3BusLineList');
		newLine.appendChild(column3BusLineList);
	}
	
	map.listOfLines = parseAnswer;
	
	//show the table:
	document.getElementById('divListLinesBus').style.display = "block";
	document.getElementById('showLinesButton').style.display = "block";
}

function updateSelection(info){

	var tableListOfBusLine = document.getElementById('tableListLineBus');
	var listOfLines = tableListOfBusLine.getElementsByTagName('tr');
	var result;
	
	for(var i = 2; i < listOfLines.length; i++){
		switch (info.name){
			
			case 'allBusLines':
				switch (info.value){
				case 'unselect':
					listOfLines[i].getElementsByTagName('input')[0].checked = 'nothingToDo';
					break;
					
				case 'all':
					listOfLines[i].getElementsByTagName('input')[0].checked = true;
					break;
				
				case 'none':
					listOfLines[i].getElementsByTagName('input')[0].checked = false;
					break;
				}
				break;
			
			case 'mainLine':
			case 'feeder':
			case 'other':
				if (info.name == listOfLines[i].getElementsByTagName('td')[2].innerHTML){
					if (info.checked == true){
						listOfLines[i].getElementsByTagName('input')[0].checked = true;
					}
					else{
						listOfLines[i].getElementsByTagName('input')[0].checked = false;
					}
				}
				break;
				
			case 'used':
				switch (info.value){
				case 'unselect':
					listOfLines[i].getElementsByTagName('input')[0].checked = 'nothingToDo';
					break;
					
				case 'inUse':
					if(listOfLines[i].getElementsByTagName('td')[3].getElementsByTagName('input')[0].checked == true ){
						listOfLines[i].getElementsByTagName('input')[0].checked = true;
					}
					else{
						listOfLines[i].getElementsByTagName('input')[0].checked = false;
					}
					break;
				
				case 'notInUse':
					if(listOfLines[i].getElementsByTagName('td')[3].getElementsByTagName('input')[0].checked == true ){
						listOfLines[i].getElementsByTagName('input')[0].checked = false;
					}
					else{
						listOfLines[i].getElementsByTagName('input')[0].checked = true;
					}
					break;
				}
				break;
				
				break;
			
			
		}
	}
	
}

function hide(id){
	document.getElementById(id).style.display = 'none';
}

mainShowBusLinesList();

