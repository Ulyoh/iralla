/**
 * @author Yoh
 */

//init of the geocoder:
var geocoder = new google.maps.Geocoder();

var SW = new gmap.LatLng(-2.3046,-80.001);
var NE = new gmap.LatLng(-2.0185,-79.8211);
var bounds = gmap.LatLngBounds(SW, NE);
	
var address ={
	latLng: new gmap.LatLng(-2.17,-79.9),
	bounds: bounds,
	language: "spanich"
};

var listOfMarker = new Array();

function onChangeAddress(event){
	var suggestionListNode = document.getElementById('suggestionListNode');
	suggestionListNode.style.display = 'none';
	
	//removed all the markers:
	/*while(listOfMarker.length != 0)	{
		listOfMarker[0].visible = false;
		listOfMarker.shift();
	}*/		

	
	//if the enter key have been pressed:
	//if (event.keyCode == 13) {
		var seizureTxt = document.getElementById('address').value;
		if (seizureTxt != "") {
			seizureTxt += ", Guayaquil, Guayas, Ecuador";
			var newNode;
			address.address = seizureTxt;
			
			//remove all the childs of suggestionNode
			while (suggestionListNode.firstChild) 
				suggestionListNode.removeChild(suggestionListNode.firstChild);
			
			//call the geocoder
			geocoder.geocode(address, function(results, status){
				var numberOfMarkerCreated = 0;
				if (status == gmap.GeocoderStatus.OK) {
					//for each result:
					typeFind : for (var i = 0; i < results.length; i++) {
						var route = "";
						for (var j = 0; j < results[i].address_components.length; j++) {	
						//for each type of the address_component:
							for (var k = 0; k < results[i].address_components[j].types.length; k++){
								switch (results[i].address_components[j].types[k]) {
									case 'route':
										route = results[i].address_components[0/*j*/].short_name;
									break;
									case 'street_address':
									case 'intersection':
									case 'colloquial_area':
									case 'neighborhood':
									case 'premise':
									case 'subpremise':
									case 'airport':
									
										var result = results[i].address_components[j].short_name + " " + route;
										//create a new marker:
										if (listOfMarker.length <= numberOfMarkerCreated){
											listOfMarker[numberOfMarkerCreated] = new BusStation(result,"",{
												map: map,
												zIndex: 2000
											});
											
											//set the listener:
											listOfMarker[numberOfMarkerCreated].addListenerOnBusStation();
										}
										 //change or set the options:
										listOfMarker[numberOfMarkerCreated].setPosition(results[i].geometry.location);
										listOfMarker[numberOfMarkerCreated].nameToShow = result;
										listOfMarker[numberOfMarkerCreated].setVisible(true);

										//create the list:
										var newNode = document.createElement("li");
										newNode.innerHTML = result;
										suggestionListNode.appendChild(newNode);
										//count the number of markers made:
										numberOfMarkerCreated++;
										//on click on the node:
										gmap.event.addDomListener(newNode, 'click', function(){



											//hide the others markers:
											for(var i = 0; i < listOfMarker.length; i++){
												if (this.innerHTML != listOfMarker[i].nameToShow)
													listOfMarker[i].setVisible(false);
											}
											//set the value of input
											document.getElementById('address').value = this.innerHTML;
											//hide the list
											suggestionListNode.style.display = 'none';
										});
										continue typeFind;
								}
							}
						}

					}
				}
				for(var i = numberOfMarkerCreated; i < listOfMarker.length; i++)
					listOfMarker[i].setVisible(false);
				if (numberOfMarkerCreated > 0) 
					suggestionListNode.style.display = 'block';
			});
		}
		
		for(var i = 0; i < listOfMarker.length; i++)
			listOfMarker[i].setVisible(false);
	//}
}







