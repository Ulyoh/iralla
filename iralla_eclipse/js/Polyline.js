/**
 * @author Yoh
 */

gmap.Polyline.prototype.addFunctionsToListener = addFunctionsToListener;		//see commonsFunctions.js
gmap.Polyline.prototype.removeFunctionsToListeners = removeFunctionsToListeners;		//see commonsFunctions.js

gmap.Polyline.prototype.setSizeOfPolylineDependingOnZoomLevel = function(sizesDependingOnZoomsLevels){
	sizesDependingOnZoomsLevelsArray = new Array(30);
	var key;
	for (key in sizesDependingOnZoomsLevels)
		sizesDependingOnZoomsLevelsArray[key] = sizesDependingOnZoomsLevels[key];
	
	//complete the array with the size set in the smaller value:
	for (var i = 0; i < sizesDependingOnZoomsLevelsArray.length; i++){
		var size = sizesDependingOnZoomsLevelsArray[i];
		if (i == 0){
			if (size == undefined)
				size = '1px';
		}
		else if (size == undefined)
			size = previousSize;
		
		sizesDependingOnZoomsLevelsArray[i] = size;
		var previousSize = size; 
	}
	this.sizeForAZoomValue = sizesDependingOnZoomsLevelsArray;
};

gmap.Polyline.prototype.enableSizeDependingOnZoom = function(bool){
	
		if ( (bool == true) && ((this._idListenerSizeDependingOnZoom == -1) || (this._idListenerSizeDependingOnZoom == undefined )) && (this.sizeForAZoomValue.length == 30))
		{
			this._oldStrokeWeight = this.strokWeight;
			function sizingDependingOnZoom(){
				this.setOptions({
					strokeWeight: this.sizeForAZoomValue[this.map.getZoom()]
					});
			}
	        this._idListenerSizeDependingOnZoom = this.map.addFunctionsToListener('zoom_changed', sizingDependingOnZoom, [this]);

			this.sizeDependingOnZoom = "on";
		}
		else if (( bool == false) && (this._idListenerSizeDependingOnZoom >= 0))
		{
			this.map.removeFunctionsToListeners(this._idListenerSizeDependingOnZoom, 'zoom_changed');
			this.strokWeight = this._oldStrokeWeight;
			this._idListenerSizeDependingOnZoom = -1;
			this.sizeDependingOnZoom = "off";
		}
	
};


