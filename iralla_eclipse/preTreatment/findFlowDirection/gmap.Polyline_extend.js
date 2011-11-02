/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

gmap.Polyline.prototype.enableAddArrow = function(){
    this.idOfListenerOfAddArrow = this.addFunctionsToListener('click',this.addArrows,[this, "normal", "eVeNt:MouseEvent.latLng"]);
};

gmap.Polyline.prototype.disableAddArrow = function(){
    this.removeFunctionsToListeners(this.idOfListenerOfAddArrow, 'click');
	this.idOfListenerOfAddArrow = -1;
};

gmap.Polyline.prototype.addArrows = function(flow, latLngOrIndexOf){
	var i = 0;
	var sectionIndex = 0;
	
	if (typeof(latLngOrIndexOf.previousBoundary) == 'undefined') {
		var indexOf = {
			previousBoundary: 0,
			nextBoundary: 0,
			sectionIndex: 0
		};
		
		var indexOf = this.betweenWhichBoundariesIs(latLngOrIndexOf);
	}
	else{
		var indexOf = latLngOrIndexOf;
	}
		
	//if sections not created or the sections[sectionIndex] not created:
	if ((typeof(this.sections) == 'undefined') || (typeof(this.sections[sectionIndex]) == 'undefined')){
		this.sections = [];
		this.sections[indexOf.sectionIndex] = {
			arrayOfArrows: [],
			flowOrder: ''
			};
	}
	
	//remove all the arrows of the section:
	if(typeof(this.sections[indexOf.sectionIndex].arrayOfArrows) != 'undefined'){
		while( this.sections[indexOf.sectionIndex].arrayOfArrows.length > 0){
			this.sections[indexOf.sectionIndex].arrayOfArrows.shift().setMap(null);
		}
	}
	
	//set the options of the arrows:
    var opts= {
        color: this._color,
        zIndex: this._zIndex,
		flow: flow,
		busLine: this
    };
	
	//set the coordinates where to put the new arrows:
	var distanceBetweenArrow = 0.001;
	var pointsToPlaceArrows = [];
	pointsToPlaceArrows = this.createArrayOfPointsWithAStepOf(distanceBetweenArrow, indexOf.previousBoundary, indexOf.nextBoundary);
	
    for( i = 0; i < pointsToPlaceArrows.length; i++){
        //index = pointsToPlaceArrows[i].index;
        //opts.vector =  new Vector(Point.latLngToPoint(path.getAt(index)), Point.latLngToPoint(path.getAt(index+1)));
		opts.vector = pointsToPlaceArrows[i].vector;
		opts.length = 0.0003;
        opts.latLng = pointsToPlaceArrows[i].coord;
		if(typeof(this.sections[indexOf.sectionIndex].arrayOfArrows) == 'undefined'){
			this.sections[indexOf.sectionIndex].arrayOfArrows = [];
		}
        this.sections[indexOf.sectionIndex].arrayOfArrows.push(Arrow(opts));
    }
	
	this.sections[indexOf.sectionIndex].flowOrder = flow;
};

gmap.Polyline.prototype.extractCorrespondingVertexListFromMarkerList =  function(markerList){
	//extract the index of each boundary:
	//var arrayOfBoundariesIndexDebug = [];
	var path = this.getPath();
	var arrayOfBoundariesIndex = [];
	var i = 0;
	var center = markerList[i].getPosition();
	var length = path.getLength();

	//found the index of the vertex corresponding to each boundaries:
	//for each vertex of the bus line
	
	for (var j = 0; j < length; j++) {
		//if the vertex igual center of the boundary
		if ((Math.abs(path.getAt(j).lat() - center.lat()) < 0.00001) && (Math.abs(path.getAt(j).lng() - center.lng()) < 0.00001)) {
			//record the index of the vertex corresponding to the boundary:
			arrayOfBoundariesIndex.push(j);
			//if i < qte of boundaries
			if (i < (markerList.length - 1)) {
				i++;
				center = markerList[i].getPosition();
			}
			else {
				break;
			}
		}
	}
	return arrayOfBoundariesIndex;
}

gmap.Polyline.prototype.betweenWhichBoundariesIs = function(latLng){
	var path = this.getPath();
			
	var indexOf = {
		previousBoundary: 0,
		nextBoundary: path.getLength() - 1,
		sectionIndex: 0
		};
	
	//is the busLine split with boundaries?
	if ((typeof(this.nbrOfBoundaries) != 'undefined') && (this.nbrOfBoundaries > 0)) {

		var arrayOfBoundariesIndex = this.extractCorrespondingVertexListFromMarkerList(
			this.arrayOfBoundaries
		);

		//FIND BETWEEN WHICH BOUNDARIES IS LATLNG:
		//find the value of the index before latlng & the corresponding section
		var result = this.findNearestProyectionOrthogonal(latLng);
		indexOf = {
			previousBoundary: 0,
			nextBoundary: 0,
			sectionIndex: 0
		};
		
		//result.index
		//for each vertex of the busline:
		for (i = 0; i < arrayOfBoundariesIndex.length; i++) {
			//if the index of the boundary > result.index
			if (arrayOfBoundariesIndex[i] > result.index) {
				//if i > 0
				if (i > 0) {
					indexOf.previousBoundary = arrayOfBoundariesIndex[i - 1];
				}
				indexOf.nextBoundary = arrayOfBoundariesIndex[i];
				break;
			}
		}
		
		indexOf.sectionIndex = i;
		
		//if there isn't index of boundaries > result.index:
		if ((indexOf.previousBoundary === 0) && (indexOf.nextBoundary === 0)) {
			indexOf.previousBoundary = arrayOfBoundariesIndex[arrayOfBoundariesIndex.length - 1];
			indexOf.nextBoundary = path.getLength() - 1;
		}
	}
	
	return indexOf;
};

gmap.Polyline.prototype.createArrayOfPointsWithAStepOf = function(step, startIndex, endIndex){
    var array = Array();
    var first = startIndex;
    var next = first;
    var distance = 0;
    var path =  this.getPath();
    var vector;
    var nextVertex;
	var passing = 0;
	
    while( next < endIndex ){
		//calculate distance from index "first" to an index "next"
		//and do next++ until distance > step
        do {
			next++;
            distance = this.distanceBetweenTwoVertex(first, next) + passing;
        } while(( distance < step ) && ( next < endIndex ));
		
		//passing of step
		passing = distance - step;
		
        if ( next <= endIndex ){
			
			do {
				//calculte the coordinates of the point coming back of the passing from the vertex of index "next"
				nextVertex = Point.latLngToPoint(path.getAt(next));
				vector = new Vector(nextVertex, Point.latLngToPoint(path.getAt(next - 1)));
				//if passing > 0
				if (passing > 0){
					array.push({
						coord: nextVertex.addVector(vector.setMagnitude(passing)).convertToLatLng(),
						vector: vector
					});
				}
				//if passing == 0
				else{
					array.push({
						coord: nextVertex.convertToLatLng(),
						vector: vector
					});
				}
			} while (( passing > step) && (passing -= step));
        }
		
		//init for next loop
		first = next;
		distance = 0;
    }

    return array;
};

gmap.Polyline.prototype.enableAddBoundary = function(){
	this.idOfListenerOfAddBoundary = this.addFunctionsToListener('click',this.addBoundary,[this, "eVeNt:MouseEvent.latLng"]);
};

gmap.Polyline.prototype.disableAddBoundary = function(){
    this.removeFunctionsToListeners(this.idOfListenerOfAddBoundary, 'click');
	this.idOfListenerOfAddBoundary = -1;
};


gmap.Polyline.prototype.addBoundary = function(latLng, loading, busStation){
	var boundaryCoord;
	var path = this.getPath();
	var distance = Infinity;
	var shortestDistance = Infinity;
	var index;
	var indexOfNewBoundary;
	
	//if the array of boundary doesn't exist in the busline make it:
	if(typeof(this.arrayOfBoundaries) == "undefined"){
		this.arrayOfBoundaries = [];
		this.nbrOfBoundaries = 0;
	}
	
	//found point on the polyline nearest to the argument latLng:
	if ((typeof(loading) == 'undefined') || (loading === false)) {
		var result = this.findNearestProyectionOrthogonal(latLng);
		if (result.type == 'vertex') {
			boundaryCoord = path.getAt(result.index);
			boundaryCoord = new gmap.LatLng(boundaryCoord.lat() + 0.0000000001, boundaryCoord.lng() + 0.0000000001);
			path.insertAt(result.index + 1, boundaryCoord);
			indexOfNewBoundary = result.index + 1;
		}
		else if (result.type == 'proyection') {
			boundaryCoord = result.coord;
			//add a vertex to the polyline:
			path.insertAt(result.index + 1, result.coord);
			indexOfNewBoundary = result.index + 1;
		}
		else {
			alert('error 6523');
		}
	}
	else if (loading == true){
		boundaryCoord = latLng;
		var result = this.findNearestVertexOf(latLng);
		indexOfNewBoundary = result.vertexMatch;
	}
	
	if (typeof(loading) == 'undefined') {
		//create a busStation where there is the new boundary:
		var busStation = map.addNewBusStation(boundaryCoord, 'boundary');
	}
	
		
	//make link from the busStation to the busLine
	busStation.busLine = this;
	busStation.layerId = this.id;
	
	//creating the new sections
	if(typeof(this.sections) == 'undefined'){
		this.sections = [];	
	}
		
	//adding the new boundary in order:
	if ((this.arrayOfBoundaries.length === 0) && (this.sections.length == 0)){
		this.arrayOfBoundaries.push(busStation);
		this.sections.push([]);
		this.sections.push([]);
	}
	else{
		var indexOf = this.betweenWhichBoundariesIs(path.getAt(indexOfNewBoundary));

		//remove all the arrows in the section where the new boundary is added:
		if ( typeof(this.sections[indexOf.sectionIndex].arrayOfArrows) != 'undefined'){
			while( this.sections[indexOf.sectionIndex].arrayOfArrows.length > 0){
				this.sections[indexOf.sectionIndex].arrayOfArrows.shift().setMap(null);	
			}
			this.sections[indexOf.sectionIndex].flowOrder = null;
		}

		//add the boundary
		this.arrayOfBoundaries.splice( indexOf.sectionIndex, 0, busStation);
		//create a new section
		this.sections.splice(indexOf.sectionIndex, 0, []);
	}
	
	//set the listener to remove the boundary with a double click
	//and the vertex of the busline:
	gmap.event.addListener(busStation.circle, 'dblclick', function(){
		this.busStation.busLine.removeVertexAtThePoint(this.busStation.getPosition());
		this.busStation.remove('findFlowDirection'); //call .removeBoundary
		this.busStation = null;
	});
	
	this.nbrOfBoundaries++;
};

gmap.Polyline.prototype.removeBoundary = function(busStation){
	
	//found sections of the busStation:
	for(var i =0; i < this.arrayOfBoundaries.length; i++){
		if(this.arrayOfBoundaries[i] == busStation){
			break;
		}
	}

	//remove the arrows from the two sections
	if (typeof(this.sections[i].arrayOfArrows) != 'undefined') {
		while (this.sections[i].arrayOfArrows.length > 0) {
			this.sections[i].arrayOfArrows.shift().setMap(null);
		}
	}
	if (typeof(this.sections[i + 1].arrayOfArrows) != 'undefined') {
		while (this.sections[i + 1].arrayOfArrows.length > 0) {
			this.sections[i + 1].arrayOfArrows.shift().setMap(null);
		}
	}
	
	//remove one section 
	this.sections.splice(i, 1);
	
	//remove the boundary  from arrayOfboundaries:
	for (var i = 0; i < this.arrayOfBoundaries.length; i++) {
		if (this.arrayOfBoundaries[i] == busStation) {
			this.arrayOfBoundaries.splice(i, 1);
			break;
		}
	}
	
	//adjust the number of boundary:
	this.nbrOfBoundaries--;
	
	//remove the vertex of busLine which was used for the boundary:
	latLngBusStation = busStation.getPosition();
	var path = this.getPath();
	var latLng;
	for( i = 0; i < path.getLength(); i++){
		latLng = path.getAt(i);
		if((Math.abs(latLng.lat() - latLngBusStation.lat()) < 0.00000001) && 
		(Math.abs(latLng.lng() - latLngBusStation.lng()) < 0.00000001)){
			path.removeAt(i);
			break;
		}
	}
	
}

gmap.Polyline.prototype.removeAllBoundaries = function(){
	if ((typeof(this.arrayOfBoundaries) != 'undefined') && (this.arrayOfBoundaries.length > 0)) {
	
		while (this.arrayOfBoundaries.length > 0) {
			//remove all the arrow of the previous and next sections:
			if (typeof(this.sections[0].arrayOfArrows) != 'undefined'){
				while (this.sections[0].arrayOfArrows.length > 0) {
					this.sections[0].arrayOfArrows.shift().setMap(null);
				}
			}
			
			if (typeof(this.sections[1].arrayOfArrows) != 'undefined'){
				while (this.sections[1].arrayOfArrows.length > 0) {
					this.sections[1].arrayOfArrows.shift().setMap(null);
				}
			}
			//remove the section[0]
			this.sections.shift();
			
			//remove the boundary:
			this.arrayOfBoundaries.shift().setMap(null);
			this.nbrOfBoundaries--;
		}
		//remove the last section:
		this.sections.shift();
		
		//remove sections:
		this.sections = undefined;
		
		//remove arrayOfBoundaries:
		this.arrayOfBoundaries = undefined;
	}
	
};

gmap.Polyline.prototype.enableRemoveArrows = function(){
	this.idOfListenerOfRemoveArrows = this.addFunctionsToListener('click', this.removeArrows, [this, "eVeNt:MouseEvent.latLng"]);
}; //this.idOfListenerOfAddBoundary = this.addFunctionsToListener('click',this.addBoundary,[this, "eVeNt:MouseEvent.latLng"]);

gmap.Polyline.prototype.disableRemoveArrows = function(){
    this.removeFunctionsToListeners(this.idOfListenerOfRemoveArrows, 'click');
	this.idOfListenerOfRemoveArrows = -1;
};

gmap.Polyline.prototype.removeArrows = function(latLng){
	
	var indexOf = this.betweenWhichBoundariesIs(latLng);
	
	if ((typeof(this.sections) == 'object') && (this.sections.length > 0) && (typeof(this.sections[indexOf.sectionIndex].arrayOfArrows) == 'object' )){
		while( this.sections[indexOf.sectionIndex].arrayOfArrows.length > 0){
			this.sections[indexOf.sectionIndex].arrayOfArrows.shift().setMap(null);
			this.sections[indexOf.sectionIndex].flowOrder = null;
		}
	}
};

gmap.Polyline.prototype.enableFindFlowAuto = function(){
	this.idOfListenerOfFindFlowAuto = this.addFunctionsToListener('click', this.findFlowAuto, [this]);
};

gmap.Polyline.prototype.disableFindFlowAuto = function(){
    this.removeFunctionsToListeners(this.idOfListenerOfFindFlowAuto, 'click');
	this.idOfListenerOfFindFlowAuto = -1;
};

gmap.Polyline.prototype.findFlowAuto = function(showMessages){
	
	if (this.nbrOfBoundaries > 0){
		var newInfo = document.createElement('div');
		newInfo.innerHTML = 'the bus line ' + this.name + ' has at least one boundary <br/>' +
							'the flow can not be found automaticaly';
		getInfosPreBoxNode().appendChild(newInfo);
		return false;
	}
	var crossRight = 0;
	var crossLeft = 0;
	var rating;
	var path = this.getPath();
	var currentSegment;
	var test = true;
	var confirmRating = false;
	var segmentRight;
	var segmentLeft;
	var vector;
	var pointRight;
	var pointLeft;
	var centerPoint;
	
	//for each segment of the busLine
	for (var i = 0; i < path.getLength() - 1; i++) {
		currentSegment = new Segment(Point.latLngToPoint(path.getAt(i)), Point.latLngToPoint(path.getAt(i+1)));
		//from the center of the segment create, on each side, a segment orthogonal of ~50m length (0.0015)
		//TODO : make a function to calculate real distance
		centerPoint = currentSegment.getCenterOfSegment();
		//    create vector:
		vector = new Vector(currentSegment.getPt1(), currentSegment.getPt2());
		//    rotate it to the right => -PI/2
		vector = vector.rotate(- Math.PI / 2);
		//    et to 50 meter:
		vector.setMagnitude(0.0005);
		//    conclude the value of the right point:
		pointRight = centerPoint.addVector(vector);
		//    and the value of the left point:
		pointLeft = centerPoint.subVector(vector);
		
		segmentRight = new Segment(centerPoint, pointRight);
		segmentLeft = new Segment(centerPoint, pointLeft);
	
		//if it cross segment Right add 1 to crossRight
		if (segmentRight.IsIntersectWithPolyline(this, [i]).length > 0){
			crossRight++;
		}
		//if it cross segment Left add 1 to crossLeft
		if (segmentLeft.IsIntersectWithPolyline(this, [i]).length > 0){
			crossLeft++;
		}
	}
	
	if((crossRight === 0) && (crossLeft === 0)){
		if (showMessages !== false){
			alert("it's not possible to determinate the flow automaticaly");
		}
		return false;
	}
	
	if ( crossRight > crossLeft ){
		rating = crossRight / crossLeft;
		ratingString = 'crossRight / crossLeft';
	}
	else if ( crossRight < crossLeft ){
		rating = crossLeft / crossRight;
		ratingString = 'crossLeft / crossRight';
	}
	
	if (( rating < 5) && (showMessages !== false)){
		confirmRating = confirm('the rating of ' + ratingString + ' = ' + rating + '\n' +
									'crossRight = ' + crossRight + '\n' +
									'crossLeft  = ' + crossLeft + '\n' +
									'do you confirm the flow found?');
	}
	else{
		confirmRating = true;
	}
	
	if (confirmRating === true){
		if((this.nbrOfBoundaries > 0) && (showMessages !== false) ){
			test = confirm('To Process, all the boundaries will be deleted');
		}
			
		if (test === false){
			return false;
		}
		
		//remove all boundaries if exist:
		//this.removeAllBoundaries();
		
		if ( crossRight > crossLeft ){
			this.addArrows("reverse", path.getAt(1));
		}
		else {
			this.addArrows("normal", path.getAt(1));
		}
		
		return true;
	}
	else{
		return false;
	}
	
};

gmap.Polyline.prototype.showArrowsFromXmlFile = function(documentXml){

	var latO = documentXml.getElementsByTagName('O')[0].getAttribute('lat');
	var lngO = documentXml.getElementsByTagName('O')[0].getAttribute('lng');
	
	latO = latO.replace(",", ".");
	lngO = lngO.replace(",", ".");
	latO = parseFloat(latO);
	lngO = parseFloat(lngO);
	
	var layersTag = documentXml.getElementsByTagName('L');
	var currentLayerTag = this.layerName;
	currentLayerTag.slice(0, currentLayerTag.indexOf('_') - 1);
	var layerName;
	var vertexList;
	var arrow;
	
	//found the layer corresponding to this:
	for( var i = 0; i < layersTag.length; i++){
		if( currentLayerTag == layersTag[i].getAttribute('name')){
			break;
		}
	}
	if ( i >= layersTag.length ){
		alert('error 538');
		return;
	}
		
	//create the array of Arrows:
	var arrowsXmlList = layersTag[i].getElementsByTagName("A");
		
	for (var j = 0; j < arrowsXmlList.length; j++) {
	
		//extract the list of vertex:
		vertexList = arrowsXmlList[j].getElementsByTagName("V");
		var pointsTab = extractPointsOfVertexList(vertexList, latO, lngO);
		
		//extract the color:
		color = arrowsXmlList[j].getElementsByTagName("C")[0].getAttribute("value");
		color = "#" + color;
		
		//create the polyline to make the arrow:
		opts = {
			map: map,
			path: pointsTab,
			strokeColor: color,
			strokeOpacity: 0.5,
			strokeWeight: 1
		};
		arrow = new gmap.Polyline(opts);
		
		//save the arrow:
		if (typeof(this.arrayOfArrows) == 'undefined'){
			this.arrayOfArrows = [];
		}
		
		this.arrayOfArrows.push(arrow);
		
	//pathsList.push(pointsTab);								what for? ?????
	}		
};

gmap.Polyline.prototype.enableFindFlowFromXmlArrows = function(){
	this.idOfListenerOfFindFlowFromXmlArrows = this.addFunctionsToListener('click', this.findFlowFromXmlArrows, [this]);
};

gmap.Polyline.prototype.disableFindFlowFromXmlArrows = function(){
    this.removeFunctionsToListeners(this.idOfListenerOfFindFlowFromXmlArrows, 'click');
	this.idOfListenerOfFindFlowFromXmlArrows = -1;
};


//TODO : to debug!!!
gmap.Polyline.prototype.findFlowFromXmlArrows = function(showMessages){
	var pathBusLine = this.getPath();
	var pathArrow;
	
	if ((typeof(this.map.arrowsListFromBdd[this.layerId]) == 'undefined') || (map.arrowsListFromBdd[this.layerId].length === 0)){
		if ( showMessages !== false){
			alert('this bus line does not have arrows from the xml file');
		}
		return false;
	}
	
	var arrayOfArrows = map.arrowsListFromBdd[this.layerId];
	
	var path;
	var vertexBuffer = [];
	var frontVertex;
	var backVertex;
	var backResult;
	var frontResult;
	var normal = 0;
	var reverse = 0;
	var confirmRating = false;
	
	for (var i = 0; i < arrayOfArrows.length; i++) {
		pathArrow = arrayOfArrows[i].polyline.getPath();
		//only with 'normals' arrows :
		if (pathArrow.getLength() == 6) {
			vertexBuffer[0] = pathArrow.getAt(0);
			vertexBuffer[4] = pathArrow.getAt(4);
			vertexBuffer[1] = pathArrow.getAt(1);
			vertexBuffer[5] = pathArrow.getAt(5);
			
			//find the extremities
			if((Math.abs(vertexBuffer[0].lat() - vertexBuffer[4].lat()) < 0.000001) && (Math.abs(vertexBuffer[0].lng() - vertexBuffer[4].lng()) < 0.000001)){
				frontVertex = vertexBuffer[4];
				backVertex = pathArrow.getAt(5);
			}
			else if((Math.abs(vertexBuffer[5].lat() - vertexBuffer[1].lat()) < 0.000001) && (Math.abs(vertexBuffer[5].lng() - vertexBuffer[1].lng()) < 0.000001)){
				frontVertex = vertexBuffer[1];
				backVertex = pathArrow.getAt(0);
			}
			else{
				alert('error');
			}
			
			//find the segment of the polyline the nearest of the backVertex:
			backResult = this.findNearestProyectionOrthogonal(backVertex);
			//find the segment of the polyline the nearest of the frontVertex:
			frontResult = this.findNearestProyectionOrthogonal(frontVertex);
		
			if ((frontResult.index - backResult.index) > 0){
				normal++;
			}
			else if ((frontResult.index - backResult.index) < 0){
				reverse++;
			}
			// if frontResult.index == backResult.index
			else{
				if((backResult.type == 'vertex') && (frontResult.type == 'vertex')){
					continue;
				}
				else if (backResult.type == 'vertex'){
					normal++;
				}
				else if (frontResult.type == 'vertex'){
					reverse++;
				}
				//if the point of backResult is nearer to *Result.index than frontResult
				else if ((Point.latLngToPoint(pathBusLine.getAt(backResult.index))).distanceOf(Point.latLngToPoint(backResult.coord)) <
						 (Point.latLngToPoint(pathBusLine.getAt(frontResult.index))).distanceOf(Point.latLngToPoint(frontResult.coord))){
					normal++;
				}
				//else
				else{
					reverse++;
				}
			}
			
		}
	}
	
	if (normal === reverse){
		if (showMessages !== false){
			alert("it's not possible to determinate the flow automaticaly");
		}
		return false;
	}
	
	if ( normal > reverse ){
		rating = normal / reverse;
		ratingString = 'normal / reverse';
	}
	else if ( normal < reverse ){
		rating = reverse / normal;
		ratingString = 'reverse / normal';
	}
	
	if (( rating < 5 ) && ( showMessages !== false )){
		confirmRating = confirm('the rating of ' + ratingString + ' = ' + rating + '\n' +
									'normal = ' + normal + '\n' +
									'reverse  = ' + reverse + '\n' +
									'do you confirm the flow found?');
	}
	else{
		confirmRating = true;
	}
	
	if (confirmRating === true) {
		if (normal > reverse) {
			this.addArrows('normal', pathBusLine.getAt(1));
		}
		else if (normal < reverse) {
			this.addArrows('reverse', pathBusLine.getAt(1));
		}
		return true;
	}
	else{
		return false;
	}
};

gmap.Polyline.prototype.enableReverseFlow = function(){
	this.idOfListenerOfReverseFlow = this.addFunctionsToListener('click', this.reverseFlow, [this, "eVeNt:MouseEvent.latLng"]);
};

gmap.Polyline.prototype.disableReverseFlow = function(){
    this.removeFunctionsToListeners(this.idOfListenerOfReverseFlow, 'click');
	this.idOfListenerOfReverseFlow = -1;
};

gmap.Polyline.prototype.reverseFlow = function(latLng){
	var indexOf = this.betweenWhichBoundariesIs(latLng);
	var originalFlow = this.sections[indexOf.sectionIndex].flowOrder;
	var newFlow;
	if (originalFlow == 'normal'){
		newFlow = 'reverse';
	}
	else if (originalFlow == 'reverse'){
		newFlow = 'normal';
	}
	else{
		alert('there is no flow to inverse');
		return;
	}
	
	if ((typeof(this.sections) == 'object') && (this.sections.length > 0) && (typeof(this.sections[indexOf.sectionIndex].arrayOfArrows) == 'object' )){
		this.removeArrows(latLng);
		this.addArrows(newFlow, latLng);
	}
	else{
		alert('there is no flow to inverse');
	}
};

gmap.Polyline.prototype.enableAddBidirectionalArrows = function(){
	this.idOfListenerOfAddBidirectionalArrows = this.addFunctionsToListener('click', this.AddBidirectionalArrows, [this, "eVeNt:MouseEvent.latLng"]);
};

gmap.Polyline.prototype.disableAddBidirectionalArrows = function(){
    this.removeFunctionsToListeners(this.idOfListenerOfAddBidirectionalArrows, 'click');
	this.idOfListenerOfAddBidirectionalArrows = -1;
};

gmap.Polyline.prototype.AddBidirectionalArrows = function(latLng){
	this.addArrows("both", latLng);
};

gmap.Polyline.prototype.findFlow = function(){
	if( this.findFlowFromXmlArrows(false) !== true ){
		if( this.findFlowAuto(false) === false ){
			return false;
		}
	}
	return true;
};

gmap.Polyline.prototype.showFlow = function(){
	for( var i = 0; i < this.sections.length  ; i++){
		for( var j = 0; j < this.sections[i].arrayOfArrows.length; j++){
			this.sections[i].arrayOfArrows[j].setMap(map);
		}
		
	}
}

gmap.Polyline.prototype.hideFlow = function(){
	for( var i = 0; i < this.sections.length  ; i++){
		for( var j = 0; j < this.sections[i].arrayOfArrows.length; j++){
			this.sections[i].arrayOfArrows[j].setMap(null);
		}
		
	}
}

if (typeof(loaded.findFlowDirection) != 'undefined'){
	loaded.findFlowDirection.push('gmap.Polyline_extend.js');
}

