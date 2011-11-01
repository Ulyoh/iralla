/**
 * @author Yoh
 */

gmap.Polyline.prototype.distanceBetweenTwoVertex = function(firstIndex, endIndex){

	var path = this.getPath();
	var distance = 0;
	
	if (firstIndex > endIndex){
		var buffer = firstIndex;
		firstIndex = endIndex;
		endIndex = buffer;
	}
	if (typeof(firstIndex) == 'undefinded')
		firstIndex = 0;
	if (typeof(endIndex) == 'undefinded')
		endIndex = path.getLength()-1;
		
	for( var i = firstIndex; i < endIndex; i++)
		distance += Point.latLngToPoint(path.getAt(i)).distanceOf(Point.latLngToPoint(path.getAt(i+1)));
	return distance;
};
	
gmap.Polyline.prototype.findNearestProyectionOrthogonal = function(latLng, symetricBoundariesOfBusLine, canBeFirstLink){
	var path = this.getPath();
	if ((typeof(latLng.type) != 'undefined') && (latLng.type == 'Point')){
		var point = latLng;
	}
	else{
		var point = Point.latLngToPoint(latLng);
	}
	var distance = Infinity;
	var shortestDistance = Infinity;
	var previousIndex;
	var vertexMatch = -1;
	var segment;
	var result;
	var pointMatch;
	var pt1;
	var pt2;
	var vector;

	var firstVertex = 0;
	var endVertex = path.getLength() - 1;
	var distanceAfterFirstVertex = 0;
	var distanceBeforeEndVertex = 0;
	
	if ((typeof(canBeFirstLink) != 'undefined') && (canBeFirstLink == false)){
		firstVertex = 1;
	}

	if ((typeof(symetricBoundariesOfBusLine) != 'undefined') && (typeof(symetricBoundariesOfBusLine.firstVertex) != 'undefined')){
		firstVertex = symetricBoundariesOfBusLine.firstVertex;
	}

	if ((typeof(symetricBoundariesOfBusLine) != 'undefined') && (typeof(symetricBoundariesOfBusLine.endVertex) != 'undefined')){
		endVertex = symetricBoundariesOfBusLine.endVertex;
	}

	if ((typeof(symetricBoundariesOfBusLine) != 'undefined') && (typeof(symetricBoundariesOfBusLine.distanceAfterFirstVertex) != 'undefined')){
		distanceAfterFirstVertex = symetricBoundariesOfBusLine.distanceAfterFirstVertex;
	}

	if ((typeof(symetricBoundariesOfBusLine) != 'undefined') && (typeof(symetricBoundariesOfBusLine.distanceBeforeEndVertex) != 'undefined')){
		distanceBeforeEndVertex = symetricBoundariesOfBusLine.distanceBeforeEndVertex;
	}
	
	if ((typeof(firstVertex) != 'undefined') && (typeof(endVertex) != 'undefined')) {
		if (firstVertex > endVertex){
			var buffer = firstVertex;
			firstVertex = endVertex;
			endVertex = buffer;
		}
		if (firstVertex >= path.getLength()){
			return false;
		}
		if(endVertex <= 0 ){
			return false;
		}
	}
	else{
		firstVertex = 0;
		endVertex = path.getLength()-1;
	}
	
	//find which segment have the nearest proyection orthogonal of latLng:
	for (var i = firstVertex; i < endVertex; i++) {
		
		pt1 = Point.latLngToPoint(path.getAt(i));
		//if a limitation has to been done:
		if( i == firstVertex ){
			if (typeof(distanceAfterFirstVertex) != 'undefined'){
				distanceAfterFirstVertex = 0;
			}
			vector = new Vector(pt1, Point.latLngToPoint(path.getAt(i+1)));
			vector.setMagnitude(distanceAfterFirstVertex);
			pt1 = pt1.addVector(vector);
		}
		
		pt2 = Point.latLngToPoint(path.getAt(i+1));
		//if a limitation has to been done:
		if( i+1 == endVertex ) {
			if (typeof(distanceBeforeEndVertex) != 'undefined'){
				distanceBeforeEndVertex = 0;
			}
			vector = new Vector(pt2, Point.latLngToPoint(path.getAt(i)));
			vector.setMagnitude(distanceAfterFirstVertex);
			pt2 = pt2.addVector(vector);
		}
		
		segment = new Segment(pt1 , pt2);
		result = segment.proyectionOf(point);
		if ( result !== false){
			
			distance = result.distanceOf(point);
	
			if (distance < shortestDistance) {
				previousIndex = i;
				pointMatch = result;
				shortestDistance = distance;
			}
		}
	}
	
	//for each vertex (inside the limitation) which one is the nearest to latLng
	//and near than pointMatch
	for (i = firstVertex; i <= endVertex; i++) {
		pt1 = Point.latLngToPoint(path.getAt(i));
		//if a limitation has been done:
		if ((i != endVertex) && (i == firstVertex) && (distanceAfterFirstVertex != 0)) {
			vector = new Vector(pt1, Point.latLngToPoint(path.getAt(i + 1)));
			vector.setMagnitude(distanceAfterFirstVertex);
			pt1 = pt1.addVector(vector);
		}
		else if ((i != firstVertex) && (i == endVertex) && (distanceBeforeEndVertex != 0)) {
			vector = new Vector(pt1, Point.latLngToPoint(path.getAt(i - 1)));
			vector.setMagnitude(distanceBeforeEndVertex);
			pt1 = pt1.addVector(vector);
		}
		distance = pt1.distanceOf(point);
		
		//preferance keeping the first vertex to another same one
		//and preference keeping last vertex
		if ((distance < shortestDistance) || ((distance == 0) && (vertexMatch != 0)))  {
			vertexMatch = i;
			shortestDistance = distance;
		}
	}

	
	if (vertexMatch >= 0){
		result = {
			type: 'vertex',
			index: vertexMatch,
			coord: path.getAt(vertexMatch)
		};
	}
	else{
		result = {
			type: 'proyection',
			index: previousIndex,
			coord: pointMatch.convertToLatLng()
		};
	}	
	return result;
};

gmap.Polyline.prototype.findNearestVertexOf = function(pointOrLatLng){
	var path = this.getPath();
	var distance;
	var shortestDistance = Infinity;
	var point;
	var vertexMatch;
	
	if (typeof(pointOrLatLng.type) == 'undefined'){
		point = Point.latLngToPoint(pointOrLatLng);
	}
	else if(pointOrLatLng.type == 'Point') {
		point = pointOrLatLng;
	}

	
	//for each vertex found which one is the nearest to latLng:
	var length = path.getLength();
	for (var i = 0; i < length; i++) {
		distance = Point.latLngToPoint(path.getAt(i)).distanceOf(point);
		
		if (distance < shortestDistance) {
			vertexMatch = i;
			shortestDistance = distance;
		}
	}
	
	return {
		vertexMatch: vertexMatch,
		distance: shortestDistance 
		};
};

gmap.Polyline.prototype.isPolygonCircleCrossed = function(polygonCircle){
 	var partialResult = [];
	var segment;
	var resultIntersections = [];
	var path = this.getPath();
 	for( var k = 0; k < path.getLength() - 1; k++){
		segment = new Segment(Point.latLngToPoint(path.getAt(k)), Point.latLngToPoint(path.getAt(k+1)));
		partialResult = segment.IsIntersectWithPolygonCircle(polygonCircle);
		if (partialResult.length > 0){
			for ( var l = 0; l < partialResult.length; l++){
				partialResult[l].index = k;
				resultIntersections.push(partialResult[l]);
			}	
		}
	}
	return resultIntersections;
 };
 
gmap.Polyline.prototype.isPolygonCrossed = gmap.Polyline.prototype.isPolygonCircleCrossed;
	
gmap.Polyline.prototype.addNewVertex = function(latLng){
	if((typeof(latLng.type) != 'undefined') && (latLng.type == 'Point')){
		latLng = latLng.convertToLatLng();
	}
 	var path = this.getPath();
	
 	//find where to add it:
	var result = this.findNearestProyectionOrthogonal(latLng);
	
	path.insertAt(result.index + 1, latLng);
	
	return result.index + 1;
 };
 
 gmap.Polyline.prototype.removeVertexAtThePoint = function(pointOrLatLng){
 	if((typeof(pointOrLatLng.type) != 'undefined') && (pointOrLatLng.type == 'Point')){
		pointOrLatLng = pointOrLatLng.convertToLatLng();
	}
	
	//find the corresponding index of the vertex:
	var result = this.findNearestVertexOf(pointOrLatLng);
	
	if ((result.vertexMatch != 0) && (result.vertexMatch != this.getPath().getLength() - 1)) {
		if (result.distance < 0.0000001) {
		
			this.getPath().removeAt(result.vertexMatch);
			
		}
		else {
			alert('error 6953');
		}
	}
 }
 
gmap.Polyline.prototype.extractBoundariesAroundThisPoint = function(vertex, distanceFromVertex, amplitude){
	//determinate where to find the proyections on the busline tested:
	var path = this.getPath(this);
	var firstVertex;
	var distanceAfterFirstVertex;
	var testedVertex;
	var endVertex;
	var distanceBeforeEndVertex;
	var lengthOfThis = path.getLength();
	var distanceFromPointToTest;

	if ( vertex != lengthOfThis-1 ){
		var lengthOf_i_To_i_plus_1_Segment = Point.latLngToPoint(path.getAt(vertex)).distanceOf(Point.latLngToPoint(path.getAt(vertex + 1)));
		var distanceTo_i_plus_1 = lengthOf_i_To_i_plus_1_Segment - distanceFromVertex;
	}

	//if vertex = first vertex of the polyline and distanceFromVertex < amplitude
	if ((vertex == 0) && (distanceFromVertex < amplitude)) {
		firstVertex = 0;
		distanceAfterFirstVertex = 0;
	}
	//distance from vertex >= amplitude
	else if (distanceFromVertex >= amplitude) {
		firstVertex = vertex;
		distanceAfterFirstVertex = distanceFromVertex - (amplitude);
	}
	//si distance from vertex < amplitude
	else {
		distanceFromPointToTest = distanceFromVertex;
		testedVertex = vertex;
		while ((distanceFromPointToTest < amplitude) && (testedVertex > 0)) {
			distanceFromPointToTest += this.distanceBetweenTwoVertex(testedVertex - 1, testedVertex);
			testedVertex--;
		}
		if ((testedVertex < 0) && (distanceFromPointToTest < amplitude)) {
			firstVertex = 0;
			distanceAfterFirstVertex = 0;
		}
		else {
			firstVertex = ++testedVertex;
			distanceAfterFirstVertex = distanceFromPointToTest - amplitude;
		}
	}

	if (vertex == lengthOfThis - 1 ){
		endVertex = vertex;
		distanceBeforeEndVertex = 0;
	}
	else if (((vertex + 1) == lengthOfThis - 1) && (distanceTo_i_plus_1 < amplitude)) {
		endVertex = vertex + 1;
		distanceBeforeEndVertex = 0;
	}
	else if (distanceTo_i_plus_1 >= amplitude) {
		endVertex = vertex + 1;
		distanceBeforeEndVertex = distanceTo_i_plus_1 - amplitude;
	}
	else {
		distanceFromPointToTest = distanceTo_i_plus_1;
		testedVertex = vertex + 1;
		while ((distanceFromPointToTest < amplitude) && (testedVertex < lengthOfThis - 1)) {
			distanceFromPointToTest += this.distanceBetweenTwoVertex(testedVertex, testedVertex + 1);
			testedVertex++;
		}
		if ((testedVertex >= lengthOfThis - 1) && (distanceFromPointToTest < amplitude)) {
			endVertex = lengthOfThis - 1;
			distanceBeforeEndVertex = 0;
		}
		else {
			endVertex = --testedVertex;
			distanceBeforeEndVertex = distanceFromPointToTest - amplitude;
		}
	}
	return {
		firstVertex: firstVertex,
		distanceAfterFirstVertex: distanceAfterFirstVertex,
		endVertex: endVertex,
		distanceBeforeEndVertex: distanceBeforeEndVertex
	};
}
//symetricBoundariesOfBusLine is optional. In case of the polyline is going and returning near "from" and
//"from" is on the other side of the polyline
gmap.Polyline.prototype.moveOn = function(step, symetricBoundariesOfBusLine, originalVertex, distanceFromOriginalVertex){
	var path = this.getPath();
	var distance = 0;
	var vector;
	var segmentLength = 0;
	var overtaking = 0;
	var distanceFromI = 0;
	var coord;
	var latLng;
	
	//originalVertex can be an index or a latLng
	//if originalVertex is a LatLng:
	if(typeof(originalVertex) == 'object'){
		var result = this.findNearestProyectionOrthogonal(originalVertex, symetricBoundariesOfBusLine);
		if(result.type == 'vertex'){
			originalVertex = result.index;
			distanceFromOriginalVertex = 0;
		}
		else{
			originalVertex = result.index;
			distanceFromOriginalVertex = Point.latLngToPoint(result.coord).distanceOf(Point.latLngToPoint(path.getAt(originalVertex)));
			}
	}
	distance -= distanceFromOriginalVertex;

	//for the vertex which follow original vertex
	for (var i = originalVertex; i < path.getLength() - 1; i++) {
		vector = new Vector(Point.latLngToPoint(path.getAt(i)), Point.latLngToPoint(path.getAt(i + 1)));
		segmentLength = vector.magnitude();
		distance += segmentLength;
		
		if (distance > step) {
			overtaking = distance - step;
			distanceFromI = segmentLength - overtaking;
			//calcul of coordinates:
			vector = vector.setMagnitude(distanceFromI);
			coord = Point.latLngToPoint(path.getAt(i)).addVector(vector);
			latLng = coord.convertToLatLng();
			if (( i == originalVertex) && (distanceFromI < distanceFromOriginalVertex)){
				alert();
			}
			return {
				index: i,
				distanceFromI: distanceFromI,
				distanceToIPlus1: overtaking,
				coord: coord,
				latLng: latLng
			};
		}
	}

	var lastIndex = path.getLength() - 1;
	latLng = path.getAt(lastIndex);
	coord = Point.latLngToPoint(latLng);
	return {
				index: lastIndex,
				distanceFromI: 0,
				distanceToIPlus1: null,
				coord: coord,
				latLng: latLng
			};
};

gmap.Polyline.prototype.isIndexInsideMainLineArea = function(index){

	var vertexLatLngEnter;
	var vertexLatLngOut;
	var indexEnter;
	var indexOut;
	
	//if there are vertex inside the main line area:
	if (typeof(this.vertexInsideMainLineArea) != 'undefined') {
	
		//if the index of vertex inside main line area are not defined:
		if (typeof(this.vertexIndexInsideMainLineArea) == 'undefined') {
			this.vertexIndexInsideMainLineArea = [];
			
			for (var k = 0; k < this.vertexInsideMainLineArea.length; k++) {
			
				//extract vertex latLng of enter and out:
				vertexLatLngEnter = this.vertexInsideMainLineArea[k].enter;
				vertexLatLngOut = this.vertexInsideMainLineArea[k].out;
				
				//found corresponding index:
				indexEnter = this.findNearestVertexOf(vertexLatLngEnter);
				indexOut = this.findNearestVertexOf(vertexLatLngOut);
				
				
				//if distance from vertex higher than 0.00001:
				if ((indexEnter.distance > 0.00001) || (indexOut.distance > 0.00001)) {
					addInfoInNewDiv().innerHTML = 'error 419 in tools/Polyline_extended.js';
				}
				
				//saving:
				this.vertexIndexInsideMainLineArea.push({
					enter: indexEnter.vertexMatch,
					out: indexOut.vertexMatch
				});
			}
			//if the last out is = 0, replace by last index:
			if(this.vertexIndexInsideMainLineArea[this.vertexIndexInsideMainLineArea.length-1].out == 0){
				this.vertexIndexInsideMainLineArea[this.vertexIndexInsideMainLineArea.length-1].out = this.getPath().length-1;
			}
		}
		
		//is vertex inside main line area:
		var previousOut = 0;
		for (var k = 0; k < this.vertexIndexInsideMainLineArea.length; k++) {
			//if it is out:
			if ((previousOut <= index) && (index < this.vertexIndexInsideMainLineArea[k].enter)) {
				return {
					isInside: false,
					nextBoundary: this.vertexIndexInsideMainLineArea[k].enter
				}
			}
			//if it is inside:
			else if ((this.vertexIndexInsideMainLineArea[k].enter <= index) && (index <= this.vertexIndexInsideMainLineArea[k].out)) {
				return {
					isInside: true,
					nextBoundary: this.vertexIndexInsideMainLineArea[k].out + 1
				}
			}
			previousOut = this.vertexIndexInsideMainLineArea[k].out;
		}
	}
	
	
	return {
		isInside: false,
		nextBoundary: this.getPath().length - 1
	}
}

 //to verify the file is loaded
loaded.tools.push('Polyline_extended.js');
