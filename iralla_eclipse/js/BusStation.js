/**
 * @author Yoh
 */

 function BusStation(opts){
	/* var options = {
		clickable: opts.clickable,
		cursor: opts.cursor,
		draggable: opts.draggable,
		flat: opts.flat,
		icon: opts.icon,
		map: opts.map,
		position: opts.position,
		raiseOnDrag: opts.raiseOnDrag,
		shadow: opts.shadow,
		shape: opts.shape,
		title: opts.title,
		visible: opts.visible,
		zIndex: opts.zIndex
	 }*/

	
	var martkerShape = {
		coord: [1,1,50,50], //[-15,-30,50,0],
		type: 'rect'
	};
	opts.shape = martkerShape;
	
	var busStation = new gmap.Marker(opts);

	
//non-static variables:	
	//public:
/*	busStation.name= opts.name;
	busStation.layerId= opts.layerId;
	busStation.id= opts.id;*/

    //"protected":
    
		
    //private:
		//none
	
//constructor:


	
//non-static methods:
    //public:

	//add listener to show the name of the station when the mouse is over a marker of bus station
	busStation.addListenerOnBusStation = function(){
	gmap.event.addListener(this, 'click', function(){
		var position = new gmap.Point();
		position = this.map.convertLatLngToPixelCoord(this.getPosition());
		var myInfo = document.getElementById('myInfo');
		myInfo.innerHTML = this.name;
		myInfo.style.display = "block";
		var x = position.x + 5;
		var y = position.y - 5;
		myInfo.style.left = x + "px";
		myInfo.style.top = y + "px";
		if (typeof(myInfo.listener) != 'undefined') {
			gmap.event.removeListener(myInfo.listener);
		}
		//set unable to show my info for the polyilnes:
		map.myInfoUnableForPolyline = false;
		
		if(typeof( myInfo.showingList) === undefined){
			for(var i = 0; i < myInfo.showingList.length; i++){
				removeNode(myInfo.showingList[i].tableLine);
				myInfo.showingList[i].busline.setOptions({zIndex:1000});
				myInfo.showingList.splice(i,1);
			}
		}
		
		myInfo.listener =  gmap.event.addListener(this, 'mouseout', function(){
				map.myInfoUnableForPolyline = true;
				var myInfo = document.getElementById("myInfo");
				clearTimeout(myInfo.idTimeOutMyInfo);
				myInfo.idTimeOutMyInfo = setTimeout(function(){document.getElementById("myInfo").style.display = "none";},1000);
			});
	});

};
    //private:
		//none
       
    return busStation;
}
 