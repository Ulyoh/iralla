
var debug = true;

function debugMode(){
	if((typeof debugModeLoaded != 'undefined') && (debugModeLoaded == true))
		return;
	var reload_button = newButton({id: 'reload_button'});
	reload_button.innerHTML = 'reload_button';
	document.getElementsByTagName('body')[0].appendChild(reload_button);
	var style = reload_button.style;
	style.bottom = 0;
	style.left = 0;
	style.position = 'absolute';
	reload_button.setAttribute('onclick', 'reload()');
	debugModeLoaded = true;
}

function reload(){
	//remove global variable:
	map = null;
	
	
	var myScripts = document.getElementsByClassName('my_script');
	var src = [];
	for (var i=0; i < myScripts.length; i++){
		var myScript = myScripts[i];
		
		var refreshOne = document.createElement('script');
		refreshOne.src = myScript.src;
		refreshOne.type="text/javascript";
		refreshOne.className="my_script";
		//removed it of the DOM:
		removeNode(myScript);
		
		//write it back in the dom to load the new script
		document.getElementsByTagName('head')[0].appendChild(refreshOne);
	}
	eval(document.getElementsByTagName('body')[0].getAttribute('onload'));
}