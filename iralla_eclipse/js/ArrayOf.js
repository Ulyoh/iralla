/**
 * @author Yoh
 */

	// NOT TESTED AT ALL
/*
if (aa == true){
	var a = 2;
}

vArray
*/
ArrayOf = function(){
	//private:
	var typeObjectPrivate = arguments[0];
	
	
	for(var i = 1 ; i < arguments.length; i++){
		arguments[i-1] = arguments[i];
	}
	delete arguments[i];
	arguments.length--;
	
	var arrayOf = [passArgumentsAgain(arguments)];
// non-static variables
	//public:
		//none
	//"protected":
		//none
	
	
	arrayOf.getItemById = function(id){
		for(var i = 0; i < this.length; i++){
			if(this[i].id == id){
				return this[i];
			}
		}
		return false;
	};
	
	//constructor:

	
	arrayOf.setOptionsToAll = function(opts){
		if ((typeof(opts.strokeColor) != undefined) && (opts.strokeColor == 'default')) {
			setDefaultColor = true;
		}
		for (var i = 0; i < arrayOf.length; i++) {
		if(setDefaultColor == true){
			opts.strokeColor = arrayOf[i].defaultColor;
		}
			arrayOf[i].setOptions(opts);
		}
		
	};
	return arrayOf;
 };

ArrayOfPolylines = function(){
	for(var i = arguments.length; i > 0; i--){
		arguments[i] = arguments[i-1];
	}
	arguments[0] = "Polyline";
	var arrayOfPolylines = new ArrayOf(passArgumentsAgain(arguments));
// non-static variables
	//public:
		//none
	//"protected":
		//none
	//private:
	var typeObjectPrivate = "Polyline";
		
	//constructor:

	
//Methods:
	//Public:		
	arrayOfPolylines.setSizeOfPolylinesDependingOnZoomLevel = function(sizesDependingOnZoomsLevels){
		var sizesDependingOnZoomsLevelsArray = [];
		sizesDependingOnZoomsLevelsArray.length = 30;
		var key;
		for (key in sizesDependingOnZoomsLevels){
			sizesDependingOnZoomsLevelsArray[key] = sizesDependingOnZoomsLevels[key];
		}
		
		//complete the array with the size set in the smaller value:
		var previousSize;
		for (var i = 0; i < sizesDependingOnZoomsLevelsArray.length; i++){
			var size = sizesDependingOnZoomsLevelsArray[i];
			if (i === 0){
				if (size === undefined){
					size = '1';
				}
			}
			else if (size === undefined){
				size = previousSize;
			}
			sizesDependingOnZoomsLevelsArray[i] = size;
			previousSize = size; 
		}
		this.sizeForAZoomValue = sizesDependingOnZoomsLevelsArray;
	};
	
	arrayOfPolylines.enableSizeDependingOnZoom = function(bool){
		
			if ( (bool === true) && ((this._idListenerSizeDependingOnZoom == -1) || (this._idListenerSizeDependingOnZoom === undefined )) && (this.sizeForAZoomValue.length == 30))
			{
				function sizingDependingOnZoom(){
					var theMap;
					for (var i = 0; i < this.length; i++) {
						theMap = this[i].getMap();
						if (theMap !== null) {
							this[i].setOptions({
								strokeWeight: this.sizeForAZoomValue[theMap.getZoom()]
							});
						}
					}
				}
		        this._idListenerSizeDependingOnZoom = this[0].map.addFunctionsToListener('zoom_changed', sizingDependingOnZoom, [this]);
	
				this.sizeDependingOnZoom = "on";
			}
			else if (( bool === false) && (this._idListenerSizeDependingOnZoom >= 0))
			{
				this[0].map.removeFunctionsToListeners(this._idListenerSizeDependingOnZoom, 'zoom_changed');
				this.strokWeight = this._oldStrokeWeight;
				this._idListenerSizeDependingOnZoom = -1;
				this.sizeDependingOnZoom = "off";
			}
	};
	
	arrayOfPolylines.removeOnePolylineFromId = function(id){
		for(var i = 0; i < arrayOfPolylines.length; i++){
			if(arrayOfPolylines[i].id == id){
				arrayOfPolylines[i].setMap(null);
				arrayOfPolylines.splice(i,0);
				break;
			}
		}
	};
	
	arrayOfPolylines.removePolylinesFromIds = function(idsArray){
		mainLoop: for(var i = 0; i < arrayOfPolylines.length; i++){
			for(var j = 0; j < idsArray.length; j++){
				if (arrayOfPolylines[i].id == idsArray[j]) {
					arrayOfPolylines[i].setMap(null);
					arrayOfPolylines.splice(i, 0);
					if(idsArray.length <= 0){
						break mainLoop;
					}
					break;
				}
			}
		}
	};

	//Private:
		//none
		
	
	return arrayOfPolylines;
};
 
ArrayOfBusLines = function(){
	var arrayOfBusLines = new ArrayOfPolylines(passArgumentsAgain(arguments));

// non-static variables
	//public:
		//none
	//"protected":
		//none
	//private:
	typeObjectPrivate = "BusLine";
	
	//constructor:

	
	return arrayOfBusLines;
 };
 
ArrayOfBusStation = function(){
	var arrayOfBusStations = new ArrayOf(passArgumentsAgain(arguments));
	if ((arrayOfBusStations[0] === "")  && (arrayOfBusStations.length)){
		arrayOfBusStations.shift();	
	}
	// non-static variables
		//public:
		//none
		//"protected":
		//none
		//private:
		var typeObjectPrivate = "BusStation";
		
		//constructor:
		
		
	//Methods:
		//Public:		
		arrayOfBusStations.setSizeOfBusStationsDependingOnZoomLevel = function(sizesDependingOnZoomsLevels){
			var sizesDependingOnZoomsLevelsArray = [];
			sizesDependingOnZoomsLevelsArray.length = 30;
			var key;
			for (key in sizesDependingOnZoomsLevels){
				sizesDependingOnZoomsLevelsArray[key] = sizesDependingOnZoomsLevels[key];
			}
			
			//complete the array with the size set in the smaller value:
			var previousSize;
			for (var i = 0; i < sizesDependingOnZoomsLevelsArray.length; i++) {
				var size = sizesDependingOnZoomsLevelsArray[i];
				if (i === 0) {
					if (size === undefined){
						size = 20;
					}
				}
				else 
					if (size === undefined){   //TODO : make this part clear !!!!
						size = previousSize;
					}
				
				sizesDependingOnZoomsLevelsArray[i] = size;
				previousSize = size;		//TODO : see it
			}
			this.sizeForAZoomValue = sizesDependingOnZoomsLevelsArray;
		};
		
		arrayOfBusStations.enableSizeDependingOnZoom = function(bool){
		
			if ((bool === true) && ((this._idListenerBusStationsSizeDependingOnZoom == -1) || (this._idListenerBusStationsSizeDependingOnZoom === undefined)) && (this.sizeForAZoomValue.length == 30)) {
			
				this._idListenerBusStationsSizeDependingOnZoom = this[0].map.addFunctionsToListener('zoom_changed', this.busStationsSizingDependingOnZoom, [this]);
				this.busStationsSizeDependingOnZoom = "on";
			}
			else {
				if ((bool === false) && (this._idListenerBusStationsSizeDependingOnZoom >= 0)) {
					this[0].map.removeFunctionsToListeners(this._idListenerBusStationsSizeDependingOnZoom, 'zoom_changed');
					this.strokWeight = this._oldStrokeWeight;
					this._idListenerBusStationsSizeDependingOnZoom = -1;
					this.busStationsSizeDependingOnZoom = "off";
				}
			}
		};
		
		arrayOfBusStations.busStationsSizingDependingOnZoom = function(){
					theMap = this[0].map;
					var size = this.sizeForAZoomValue[theMap.getZoom()];
					for (var i = 0; i < this.length; i++) {
						var iconPath = this[i].iconPath;
						var iconStation = new gmap.MarkerImage(iconPath, null,  null, null, new gmap.Size(size, size));	
						this[i].setIcon(iconStation);
					}
				};
		
	return arrayOfBusStations;
};
