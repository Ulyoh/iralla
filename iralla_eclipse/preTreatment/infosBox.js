/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function createInfosBox(){

	//make a div to show informations:
	if(document.getElementById('infos') === null){
		var infos = document.createElement('div');
		infos.setAttribute('id', 'infos');
		document.body.appendChild(infos);
		infos.style.display = 'none';
	}

	//make a div to hide show the infos box:
	if(document.getElementById('hide_show_infos') === null){
		var hide_show_infos = document.createElement('div');
		hide_show_infos.setAttribute('id', 'hide_show_infos');
		document.body.appendChild(hide_show_infos);

		hide_show_infos.onclick = showHideInfosBox;
	}

}

function getInfosBoxNode(){
	if(document.getElementById('infos') === null){
		createInfosBox();
	}
	return document.getElementById('infos');
}

function showHideInfosBox(){
	var infos = document.getElementById('infos');
	if( infos !== null){
		var displayValue = infos.style.display;
		if (displayValue == 'none'){
			infos.style.display = 'block';
		}
		else{
			infos.style.display = 'none';
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
	getInfosBoxNode().appendChild(add_info);
	return add_info;
}

function getAddInfoDiv(){
	return document.getElementById('add_info');
}
createInfosBox();

function showResultInInfosBox(returnFromServor){
	addInfoInNewDiv().innerHTML = returnFromServor;

}
