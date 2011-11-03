
SubMap._busStationsArray.createAreasAroundBusLines = function(index){

	if(typeof(index)== 'undefined'){
		index = 0;
	}
	if (index < this.length) {
		this[index].createAreasAroundBusLines();
		this[index].areaSurrounded.mergedStackedPart();
	
		//affichage du résultat
		setTimeout('function(){SubMap._busStationsArray[" + index + "].areaSurrounded.setOptions({map:null})}', 2000);
	}
	
};

SubMap._busStationsArray.lookForLinks = function(){
	for( var i = 0; i < this.length; i++){
		this[i].lookForLinks();
	}
};

SubMap._busStationsArray.groupingLinks = function(){
	for( var i = 0; i < this.length; i++){
		this[i].groupingLinks();
	}
};


loaded.makeVirtualsBusStation.push('gmap.ArrayOfBusStation_extended.js');
