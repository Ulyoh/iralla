/**
 * @author Yoh
 */

function mainAddNewDatas(){
	var divIframeSendFile = document.createElement('div');
	divIframeSendFile.setAttribute('id', 'divIframeSendFile');
	document.body.appendChild(divIframeSendFile);
	
	var iframeSendFile = document.createElement('iframe');
	iframeSendFile.setAttribute('id', 'iframeSendFile');
	iframeSendFile.setAttribute('src', monsite + '/iframeUploadFile.html');
	divIframeSendFile.appendChild(iframeSendFile);
}


function hide(nodeName){
	var node = document.getElementById(nodeName);
	node.parentNode.removeChild(node);
}

loaded.addNewDatas.push('mainAddNewDatas.js');

