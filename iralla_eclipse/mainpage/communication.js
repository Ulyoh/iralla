/*
LISTE DES FONCTIONS:
GetXmlHttpObject()
request(appelFichier, callback, type)
nouveauPoly(nomFichierXml)
savePoly(nomFichierXml)
*/

//////////////////////////////////////////////////////////////////////////
//																		//
//	GetXmlHttpObject()													//
//																		//
//		gestion de la creation de l objet xhttp suivant le navigateur	//
//																		//
//////////////////////////////////////////////////////////////////////////

function GetXmlHttpObject()
{

	//pour les autres browsers
	if (window.XMLHttpRequest)
	{
		return new XMLHttpRequest(); 
	}
		//pour IE
	else if (window.ActiveXObject)
	{
		try 
		{
			return new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch(e)
		{
			return new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return null;
}

//					Fin fonction : loadXMLDoc							//					
//////////////////////////////////////////////////////////////////////////


//////////////////////////////////////////////////////////////////////////
//																		//
//	request(envoieDeData, callback, type)								//
//																		//
//		permet de communiquer avec le serveur							//
//		fichier : emplacement et nom du fichier							//
//		callback : nom de la fonction js appelé lors de la réponse du //
//					serveur												//
//		type : "xml" ou rien											//
//																		//
//////////////////////////////////////////////////////////////////////////
/*
 * request({
		phpFileCalled: ,
		argumentsToPhpFile: ,
		type: 'xml',
		callback: callbackFunction,
		THIS: thisCallBack,
		argumentsCallback: argumentsCallback,
		xdebug: true/false
	});
 */
function request(options){
	var xmlhttp = new GetXmlHttpObject();
	
	if (typeof(options.xdebug) == 'undefined'){
		options.xdebug = true;
	}
	if (xmlhttp === null) {
		alert("Votre navigateur ne supporte pas le XMLHTTP");
		return;
	}
	
	if (typeof(options.postOrGet) == 'undefined') {
		options.postOrGet = 'POST';
	}
	
	if (typeof(options.asynchrone) == 'undefined') {
		options.asynchrone = true;
	}
	
	if (typeof(options.setRequestHeaderName) == 'undefined'){
		options.setRequestHeaderName = 'Content-Type';
		options.setRequestHeaderValue = 'application/x-www-form-urlencoded';
	}
	
	if (options.asynchrone === true) {
		xmlhttp.onreadystatechange = function(){
			if (xmlhttp.readyState == 4) {
				if (options.callback != 'undefined') {
					callbackFunction(options);
				}
			}
		};
	}
	
	xmlhttp.open(options.postOrGet, options.phpFileCalled, options.asynchrone);// Send the POST request
	if (options.postOrGet == 'POST') {
		xmlhttp.setRequestHeader(options.setRequestHeaderName, options.setRequestHeaderValue);
	}
	xmlhttp.send(options.argumentsToPhpFile);
	
	if (options.asynchrone === false) {
		if (typeof(options.callback) == 'string') {
			callbackFunction();
		}
		if ((options.type == "xml") || (options.type == "XML")) {
			return xmlhttp.responseXML;
		}
		else {
			return xmlhttp.responseText;
		}
	}
	
	return xmlhttp;
	
	function callbackFunction(options){
	
	/*	if ((xDebugOn == true) && (options.xdebug == true)) {
			toDebugResponse(options);
		}*/
		
		if ((options.type == "xml") || (options.type == "XML")) {
			if (options.THIS == 'undefined') {
				options.callback(xmlhttp.responseXML, options.argumentsCallback);
			}
			else {
				options.callback.call(options.THIS, xmlhttp.responseXML, options.argumentsCallback);
			}
		}
		else {
			if (options.THIS == 'undefined') {
				options.callback(xmlhttp.responseText, options.argumentsCallback);
			}
			else {
				options.callback.call(options.THIS, xmlhttp.responseText, options.argumentsCallback);
			}
		}
	}
}

//			Fin fonction : request(fichier, callback, type)				//					
//////////////////////////////////////////////////////////////////////////


function showResponse(index){
	var responseDiv = document.getElementById('responseDiv');
	responseDiv.innerHTML = responseDiv.responseToShow[index];
	responseDiv.style.zIndex = '4999';
	responseDiv.ondblclick = setZindexToZero;
}

function setZindexToZero(){
	this.style.zIndex = '-1';
}
