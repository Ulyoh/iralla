/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function createinfosPreBox(){
	//make a div to show informations:
	if(document.getElementById('infosPre') === null){
		var infosPre = document.createElement('div');
		infosPre.setAttribute('id', 'infosPre');
		document.body.appendChild(infosPre);
		infosPre.style.display = 'none';
	}

	//make a div to hide show the infosPre box:
	if(document.getElementById('hide_show_infosPre') === null){
		var hide_show_infosPre = document.createElement('div');
		hide_show_infosPre.setAttribute('id', 'hide_show_infosPre');
		hide_show_infosPre.setAttribute('onclick', 'showHideInfosPreBox();');
		document.body.appendChild(hide_show_infosPre);
	}
}

function getInfosPreBoxNode(){
	if(document.getElementById('infosPre') === null){
		createinfosPreBox();
	}
	return document.getElementById('infosPre');
}

function showHideInfosPreBox(){
	var infosPre = getInfosPreBoxNode();
	if( infosPre !== null){
		var displayValue = infosPre.style.display;
		if (displayValue == 'none'){
			infosPre.style.display = 'block';
		}
		else{
			infosPre.style.display = 'none';
		}
	}
}

function addInfoInNewDiv(){
	var add_info = document.getElementById('add_info');

	if (add_info !== null){
		add_info.removeAttribute('id');
	}
	add_info = document.createElement('div');
	add_info.setAttribute('id', 'add_info');
	getInfosPreBoxNode().appendChild(add_info);
	return add_info;
}

function getAddInfoDiv(){
	return document.getElementById('add_info');
}
createinfosPreBox();

function showResultIninfosPreBox(returnFromServor){
	addInfoInNewDiv().innerHTML = returnFromServor;

}
