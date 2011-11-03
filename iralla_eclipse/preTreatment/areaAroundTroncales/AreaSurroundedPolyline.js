/**
 * @author Yoh
 */

 
AreaSurroundedPolyline = function(polyline, distance){
	
	
	var i, j, indexOfPoint;
	
	var areaSurroundedPolyline = new gmap.Polygon({
		map: polyline.map,
		clickable: false,
		strokeColor: '#00008b',
		strokeOpacity: 0.5,
		//fillColor: '#6495ed',
		fillColor: '#00008b',
		fillOpacity : 0.2,
		strokeWeight: 1,
		zIndex: 1
	});
	
	polyline.areaSurrounded = areaSurroundedPolyline;
	var polylinePath = polyline.getPath();
	var polygonPath = [];
	
	//constructor
	
//for the first vertex:
	var pt1 = Point.latLngToPoint(polylinePath.getAt(0));
	var pt2 = Point.latLngToPoint(polylinePath.getAt(1));
	var vector21 = new Vector(pt2, pt1);
	
	//create vector plus 45° of vector21:
	var vectorPlus45Grados = vector21.rotate(Math.PI / 4);
	//create vector minus 45° of vector21:
	var vectorMinus45Grados = vector21.rotate(- Math.PI / 4);
	
	//set magnitudes : 
	vectorPlus45Grados.setMagnitude(Math.sqrt(2) * distance);
	vectorMinus45Grados.setMagnitude(Math.sqrt(2) * distance);
	
	//get the points found:
	polygonPath[0] = pt1.addVector(vectorPlus45Grados).convertToLatLng();
	polygonPath[1] = pt1.addVector(vectorMinus45Grados).convertToLatLng();
		
//for the other appart of the last one:
	var ptFind1;
	var ptFind2;
	for (i = 1; i < polylinePath.length - 1; i++){
		pt1 = Point.latLngToPoint(polylinePath.getAt(i-1));
		pt2 = Point.latLngToPoint(polylinePath.getAt(i));
		var pt3 = Point.latLngToPoint(polylinePath.getAt(i+1));
				
		//create vectores:
		vector21 = new Vector(pt2, pt1);
		var vector23 = new Vector(pt2, pt3);
		
		//the vectorSupport which the direction have the same slope than the line 
		//passing by the pt2 and the point we are looking for:
		var angle = /*Math.abs(*/vector21.getAngleWith(vector23)/*)*/;
		if (angle > Math.PI){
			angle -= Math.PI;
		}
		if (angle < -Math.PI){
			angle += Math.PI;
		}
		var vectorSupport;
		var magnitude;
		if ((angle != round(Math.PI)) && (angle != 0)) {
			magnitude = distance / Math.sin(angle / 2);
			
			//add the vector 21 and 23 to get a vector which its direction passing to the point we looking for
			vectorSupport = vector21.rotate(angle / 2);
			//set the magnitude:
			vectorSupport.setMagnitude(magnitude);
		}
		else{
			vectorSupport = vector21.rotate(Math.PI / 2);
			magnitude = distance;
			vectorSupport.setMagnitude(magnitude);
		}

		//calculate the two points we are looking for:
		ptFind1 = pt2.addVector(vectorSupport);
		ptFind2 = pt2.subVector(vectorSupport);
		
		//find in which order to add this two elements:
		//create two segments, each which one ptFind and one of the two previous point calculated
		//if the two segments are crossed, inverse the order to add them in the polygonPath
		
		/*if ((new Segment(polylinePath.getAt(i-1), ptFind1)).IsIntersectWithSegment(new Segment(polylinePath.getAt(i), ptFind2)) == false){
			*/polygonPath.splice(i,0,ptFind1.convertToLatLng(),ptFind2.convertToLatLng());
		/*}
		else{
			polygonPath.splice(i,0,ptFind2.convertToLatLng(),ptFind1.convertToLatLng());
		}*/
	}
	
//for the last vertex
	pt1 = Point.latLngToPoint(polylinePath.getAt(i-1));
	pt2 = Point.latLngToPoint(polylinePath.getAt(i));
	var vector12 = new Vector(pt1, pt2);
	
	//create vector plus 45° of vector21:
	vectorPlus45Grados = vector12.rotate(Math.PI / 4);
	//create vector minus 45° of vector21:
	vectorMinus45Grados = vector12.rotate(- Math.PI / 4);
	
	//set magnitudes : 
	vectorPlus45Grados.setMagnitude(Math.sqrt(2) * distance);
	vectorMinus45Grados.setMagnitude(Math.sqrt(2) * distance);
	
	//get the points found:
	ptFind1 = pt2.addVector(vectorMinus45Grados).convertToLatLng();
	ptFind2 = pt2.addVector(vectorPlus45Grados).convertToLatLng();
	polygonPath.splice(i,0,ptFind1,ptFind2);
	
	//test polygonPath:
	
	for ( i = 0; i < polygonPath.length; i++){
		if ((isNaN(polygonPath[i].lat()) === true) || (isNaN(polygonPath[i].lng()) === true)){
			alert('error 72');		//TODO change the alert to other thing
		}
	}
	
	areaSurroundedPolyline.setPath(polygonPath);
	
	//end constructor
	
	areaSurroundedPolyline.mergedStackedPart = function(){
		
		var pathOfTheAreaLatLng = areaSurroundedPolyline.getPath();
				
		//convert latlng to point:
		var pathOfTheArea= [];
		for(var i = 0; i < pathOfTheAreaLatLng.getLength(); i++){
			pathOfTheArea.push(Point.latLngToPoint(pathOfTheAreaLatLng.getAt(i)));
		}
		var length = pathOfTheArea.length;	

		//in case two point following each other are egal, the algorithm can not work
		//so we just move a bit the first point in order that the algo can still work :
		var e;
		for(i = 0; i < length; i++){
			if (( (i == length - 1) && (pathOfTheArea[i].y == pathOfTheArea[0].y) &&
				(pathOfTheArea[i].x == pathOfTheArea[0].x)
				) || (
				(i < length - 1) && (pathOfTheArea[i].y == pathOfTheArea[i+1].y) &&
				(pathOfTheArea[i].x == pathOfTheArea[i+1].x))){
				
				//we want to make the point of index i ant i+1 two distinguish point
				//to get a segment.
				//we will translate the point on index i in direction of the point in front of it "e" :
				e = length - 1 - i;
				var pti = Point.latLngToPoint(pathOfTheArea[i]);
				var facingVector = new Vector(pti, Point.latLngToPoint(pathOfTheArea[e]));
				
				//a translation of 10^-7:
				facingVector.setMagnitude(Math.pow(10,-7));
				
				//make the translation:
				pti.addVector(facingVector);
				
				//save result:
				pathOfTheArea[i] = pti.convertToLatLng();
			}
		}
		
		
	//searching all the points of the area that should be removed
	//to do not have part of the area stacked by itself.
		i = 0;		//beginning of the array
		e = length - 1; //end of the array
		
		//array of all the index of point that have to be removed from the area:
		var indexOfPointsToRemove = [];
		
		while( (i + 1) < (e - 1) ){
			//create the four points we need:
			var pti1 = pathOfTheArea[i];
			var pti2 = pathOfTheArea[i + 1];
			var pte1 = pathOfTheArea[e];
			var pte2 = pathOfTheArea[e - 1];
			
			//create the segment formed by the 'i' point:
			var iSegment = new Segment(pti1, pti2);
			//create the segment formed by the 'e' point:
			var eSegment = new Segment(pte1, pte2);
			//create the segment [pti1 , pte1]:
			var ie1Segment = new Segment(pti1, pte1);
			
			var ie2Segment;
			
			//testing if the quadrilateral we want to do doesn't cross it self:
			//ie: the segments [pti1, pte1] [pti2, pte2]doesn't cross each other
			//ie: pti2 and pte2 are on the same side of the supportLine of the
			//segment [pti1, pte1]
			var iLine = iSegment.getSupportLine();
			var ie1Line = ie1Segment.getSupportLine();
			
			//if the points are not on the same side
			if ( ie1Line.positionOf(pti2) != ie1Line.positionOf(pte2) ){
				ie1Segment = new Segment(pti1, pte2);
				ie2Segment = new Segment(pti2, pte1);
			}
			else{
				ie2Segment = new Segment(pti2, pte2);
			}
			
			//test of the position of each point appart of the ones used for the quadrilateral:
			for (j = 0; j < length; j++){
				if ((j != i) && (j != i+1) && (j != e) && (j != e-1)){
					var testedPoint = pathOfTheArea[j];
					
					iLine = iSegment.getSupportLine();
					var eLine = eSegment.getSupportLine();
					ie1Line = ie1Segment.getSupportLine();
					var ie2Line = ie2Segment.getSupportLine();
					
					//if the point is inside the quadrilateral
					//or on the border of the quadrilateral
					var test1 = iLine.positionOf(testedPoint);
					var test2 = eLine.positionOf(testedPoint);
					var test3 = ie1Line.positionOf(testedPoint);
					var test4 = ie2Line.positionOf(testedPoint);
					if ((
					(test1 == iLine.positionOf(pte1)) &&
					(test2 == eLine.positionOf(pti1)) &&
					(test3 == ie1Line.positionOf(pti2)) &&
					(test4 == ie2Line.positionOf(pti1))) ||
					(test1 == 'coinsided') ||
					(test2 == 'coinsided') ||
					(test3 == 'coinsided') ||
					(test4 == 'coinsided')) {
						indexOfPointsToRemove.push(j);

					}
				}
			}
			i++;
			e--;
		}//end while
		
		if (indexOfPointsToRemove.length > 0){
			//ordering point to remove:
			//sort the index of the points in indexOfPointsToRemove:
			indexOfPointsToRemove.sort(function(a,b){return a - b;});
			
			//remove the double:
			for(i = 0; i < indexOfPointsToRemove.length-2; i++){
				if(indexOfPointsToRemove[i+1] == indexOfPointsToRemove[i]){
					indexOfPointsToRemove.splice(i,1);
					i--;
				}
			}
			
			//create index of point to keep:
			var indexOfPointsToKeep = [];
			var k = 0;
			for(i = 0; i < length; i++){
				if(indexOfPointsToRemove[k] == i){
					k++;
				}
				else{
					indexOfPointsToKeep.push(i);

			/*			new PolygonCircle({
							map: map,
							clickable: false,
							fillColor: '#FF0000',
							fillOpacity: 0.4,
							strokeColor: '#FF0000',
							strokeOpacity: 0.5,
							strokeWeight: 2,
							zIndex: 10000,
							radius: 0.0002,
							center: pathOfTheArea[i].convertToLatLng(),
						});*/
					
				}
			}
			
			//creation of chains for each consecutive points in indexOfPointsToKeep:
			//at each extremity of the chains the extension point which has to be removed
			//is added
			var chains = [];
			var chainIndex = 0;
			var previousVertex;
			var currentVertex;
			
			chains[chainIndex] = new VertexLink( indexOfPointsToKeep[0], pathOfTheArea[indexOfPointsToKeep[0]]);
			previousVertex = chains[chainIndex];
			
			for( i = 1; i < indexOfPointsToKeep.length; i++){
				
				if((indexOfPointsToKeep[i] - indexOfPointsToKeep[i-1]) == 1){
					currentVertex = new VertexLink(indexOfPointsToKeep[i], pathOfTheArea[indexOfPointsToKeep[i]], previousVertex);
					previousVertex.next = currentVertex;
					previousVertex = currentVertex;
				}
				else{
					chains[chainIndex].last = currentVertex;
					chainIndex++;
					chains[chainIndex] = new VertexLink(indexOfPointsToKeep[i], pathOfTheArea[indexOfPointsToKeep[i]]);
					previousVertex = chains[chainIndex];
					currentVertex = chains[chainIndex];
				}
				
			}
			chains[chainIndex].last = currentVertex;
			
			//if the end and beginning of indexOfPointsToKeep are the corresponding end and beginning index of pathOfTheArea
			//the first chain and the last chain of chains[] as to be joined
			if (( indexOfPointsToKeep[indexOfPointsToKeep.length-1] == length -1) 
			&& (indexOfPointsToKeep[0] === 0)){
				var last = chains[0].last;
				chains[0].previous = chains[chainIndex].last;
				chains[chainIndex].last.next = chains[0];
				chains[0] = chains[chainIndex];
				chains[0].last = last;
				chains.pop(); 
			}

			//adding the extremities points which have been removed:
			for( i = 0; i < chains.length; i++){
				var index = chains[i].index - 1;
				if ( index >= 0){
					chains[i].previous = new VertexLink(index , pathOfTheArea[index], "undefined", chains[i]);
				}
				else{
					chains[i].previous = new VertexLink(length - 1 , pathOfTheArea[length - 1], "undefined", chains[i]);
				}
				index = chains[i].last.index + 1;
				if ( index < length){
					chains[i].last.next = new VertexLink(index , pathOfTheArea[index], chains[i].last);
				}
				else{
					chains[i].last.next = new VertexLink(0 , pathOfTheArea[0], chains[i].last);
				}
			}
			
			//connect the chains which are separate of only one point:
			var segment1;
			var segment2;
			loop1 : for (i = 0; i < chains.length - 1; i++) {
				if( Math.abs(chains[i].last.index - chains[i+1].index) == 2){
					//only if the two segments from the points are not cut by an other segment
					//index of the point to test:
					indexOfPoint = ( chains[i].last.index + chains[i+1].index ) / 2;
					//the two segment from the point:
					segment1 = new Segment(pathOfTheArea[indexOfPoint - 1], pathOfTheArea[indexOfPoint]);
					segment2 = new Segment(pathOfTheArea[indexOfPoint], pathOfTheArea[indexOfPoint + 1]);
					
					//for all the segments apart of the semgent1 and segment2:
					for( j = 0; j < indexOfPointsToKeep.length - 1; j++){
						if ((indexOfPointsToKeep[j+1] - indexOfPointsToKeep[j] == 1 )
						&& (indexOfPointsToKeep[j+1] != indexOfPoint) && (indexOfPointsToKeep[j] != indexOfPoint)){
							var segmentTest = new Segment(pathOfTheArea[indexOfPointsToKeep[j]], pathOfTheArea[indexOfPointsToKeep[j+1]]);
							//if one cut segment1 or segment2
							if ((segment1.IsIntersectWithSegment(segmentTest) === true)
							 || (segment2.IsIntersectWithSegment(segmentTest) === true)){
								continue loop1;
							}
						}
					}
					
				/*	if ((indexOfPointsToKeep[0] - indexOfPointsToKeep[indexOfPointsToKeep.length - 1] == 1 )
					&& (indexOfPointsToKeep[0] != indexOfPoint) && (indexOfPointsToKeep[indexOfPointsToKeep.length - 1] != indexOfPoint) ){
						var segmentTest = new Segment(pathOfTheArea[indexOfPointsToKeep[0]], pathOfTheArea[indexOfPointsToKeep[indexOfPointsToKeep.length - 1]]);
							if ((segment1.IsIntersectWithSegment(segmentTest) == true) || (segment2.IsIntersectWithSegment(segmentTest) == true)){
								continue test1;
							}
					}*/
					
					chains[i].last.next = chains[i+1];
					chains[i+1].previous = chains[i].last;
					chains[i].last = chains[i+1].last;
					chains.splice(i+1,1);
					i--;
				}
			}
			if( Math.abs(chains[chains.length-1].last.index - chains[0].index) == 2){
				var makeConnection = true;
				//only if the two segments from the points are not cut by an other segment
				//index of the point to test:
				indexOfPoint = ( chains[chains.length-1].last.index + chains[0].index ) / 2;
				//the two segment from the point:
				segment1 = new Segment(chains[chains.length-1].last.point, pathOfTheArea[indexOfPoint]);
				segment2 = new Segment(chains[0].point, pathOfTheArea[indexOfPoint]);
					
				//for all the segments apart of the semgent1 and segment2:
				for( j = 0; j < indexOfPointsToKeep.length - 1; j++){
					if ((indexOfPointsToKeep[j+1] - indexOfPointsToKeep[j] == 1 )
					&& (indexOfPointsToKeep[j+1] != indexOfPoint) && (indexOfPointsToKeep[j] != indexOfPoint)){
						var segmentTest = new Segment(pathOfTheArea[indexOfPointsToKeep[j]], pathOfTheArea[indexOfPointsToKeep[j+1]]);
						//if one cut segment1 or segment2
						if ((segment1.IsIntersectWithSegment(segmentTest) == 'true') || (segment2.IsIntersectWithSegment(segmentTest) == 'true')){
							makeConnection = false;
						}
					}
				}
					
				if ((indexOfPointsToKeep[0] - indexOfPointsToKeep[indexOfPointsToKeep.length - 1] == 1 )
				&& (indexOfPointsToKeep[0] != indexOfPoint) && (indexOfPointsToKeep[indexOfPointsToKeep.length - 1] != indexOfPoint) ){
					var segmentTest = new Segment(pathOfTheArea[indexOfPointsToKeep[0]], pathOfTheArea[indexOfPointsToKeep[indexOfPointsToKeep.length - 1]]);
						if ((segment1.IsIntersectWithSegment(segmentTest) == 'true') || (segment2.IsIntersectWithSegment(segmentTest) == 'true')){
							makeConnection = false;
						}
				}
				
				if (makeConnection === true){
					chains[chains.length-1].last.next = chains[0];
					chains[0].previous = chains[chains.length-1].last;
					chains[chains.length-1].last = chains[0].last;
					chains.splice(0,1);
				}
			}
			
			//close the chains for which the beginning and end are separate of one point
			//except the one formed of only two point:
			for (i = 0; i < chains.length; i++) {
				if(( Math.abs(chains[i].last.index - chains[i].index) <= 2)
				&& (chains[i].next != chains[i].last)){
					chains[i].last.next = chains[i];
					chains[i].previous = chains[i].last;
				}
			}	
			
			var segmentRef;
			var segmentTest;
			var distance;
			var validIntersectionPoint;
			var intersectionPoint;
			var validDistance = Infinity;
			var whichChain = 0;
			var where = 'noWhere';
			var link;
			//connect the chain which follow each other:
			for( i = 0; i < chains.length - 1; i++){
				//create the segment of the end of chains[i]:
				segmentRef = new Segment(chains[i].last.point,chains[i].last.next.point);
				
				whichChain = 0;
				for( var j = i + 1; j < chains.length; j++){
					//create the segment of the begining of chains[j]
					segmentTest = new Segment(chains[j].point, chains[j].previous.point);
					intersectionPoint = segmentTest.IsIntersectWithSegment(segmentRef, 'On');
					distance = chains[i].last.point.distanceOf(intersectionPoint);
							
					//if an intersection has been found:
					if (typeof(intersectionPoint) == 'object') {
						if ((typeof(validIntersectionPoint) != 'object') || (distance < validDistance)) {
							validIntersectionPoint = intersectionPoint;
							validDistance = distance;
							whichChain = j;
							where = 'begining';
						}
					}
					
					//create the segment of the end of chains[j]
					segmentTest = new Segment(chains[j].last.point, chains[j].last.next.point);
					intersectionPoint = segmentTest.IsIntersectWithSegment(segmentRef, 'On');
					distance = chains[i].last.point.distanceOf(intersectionPoint);
							
					//if an intersection has been found:
					if (typeof(intersectionPoint) == 'object') {
						if ((typeof(validIntersectionPoint) != 'object') || (distance < validDistance)) {
							validIntersectionPoint = intersectionPoint;
							validDistance = distance;
							whichChain = j;
							where = 'end';
						}
					}
				}
				
				//if one chain can be connected to chain[i]
				if ( whichChain > 0){
					
					if ( where == 'end'){
						//invert the sens of the chain:
						var next;
						var first = chains[whichChain];
						link = chains[whichChain].previous;
						do {
							next = link.next;
							link.next = link.previous;
							link.previous = next;
							link = next;
						}while (link.type == "VertexLink");
						chains[whichChain] = chains[whichChain].last;
						chains[whichChain].last = first;
					}
					
					chains[i].last.next = new VertexLink(chains[i].last.index + 0.1 , validIntersectionPoint, chains[i].last, chains[whichChain]);
					chains[whichChain].previous = chains[i].last.next;
					chains[i].last = chains[whichChain].last;
					chains.splice(whichChain,1);
					i--;
					validDistance = Infinity;
				}
			}
			//end of connecting the chain which follow each other:
			
			var intersectionPoint1;
			var intersectionPoint2;
			//search chains could be added to another from one segment
			for (i = 0; i < chains.length - 1; i++) {
				link = chains[i].previous;
				//for each segment in chain[i]:
				do{
					//create the segment formed with the link and link.next
					segmentRef = new Segment(link.point, link.next.point);
				
					//for all the next chains
					for( var j = i+1; j < chains.length; j++){
						//create the segment of the beginning of chains[j]:
						segmentTest = new Segment(chains[j].previous.point, chains[j].point);
						intersectionPoint1 = segmentTest.IsIntersectWithSegment(segmentRef, 'On');
						distance = link.point.distanceOf(intersectionPoint1);
						
						//if an intersection has been found:
						if (typeof(intersectionPoint1) == 'object') {
							//calculation of the second intersection:
							//create the segment of the end of chains[j]
							segmentTest = new Segment(chains[j].last.point,chains[j].last.next.point);
							intersectionPoint2 = segmentTest.IsIntersectWithSegment(segmentRef, 'On');
							
							if (intersectionPoint == "false")
								alert("error intersectionPoint 286");
							
							//if intersectionPoint1 is more near to link than intersectionPoint2
							if ( distance < link.point.distanceOf(intersectionPoint2)){
								//add chain[j]:
								var nextMaillon = link.next;
								link.next = new VertexLink(link.index + 0.1 , intersectionPoint1, link, chains[j]);
								chains[j].previous = link.next;
								chains[j].last.next = new VertexLink(chains[j].last.index + 0.1 , intersectionPoint2, chains[j].last, nextMaillon);
								nextMaillon.previous = chains[j].last;
							}
							else{
								//inverse chain[j]:
								var next;
								var first = chains[j];
								var buffer = first.previous;
								do {
									next = buffer.next;
									buffer.next = buffer.previous;
									buffer.previous = next;
									buffer = next;
								}while (typeof(buffer) != "undefined");
								chains[j] = chains[j].last;
								chains[j].last = first;
							
								
								//add chain[j]:
								var nextMaillon = link.next;
								link.next = new VertexLink(link.index + 0.1 , intersectionPoint2, link, chains[j]);
								chains[j].previous = link.next;
								chains[j].last.next = new VertexLink(nextMaillon.index - 0.1 , intersectionPoint1, chains[j].last, nextMaillon);
								nextMaillon.previous = chains[j].last;	
							}
							
							chains.splice(j,1);
							intersectionPoint1 = "undefined";
							intersectionPoint2 = "undefined";
							link = link.previous;
							break;
						}
					}
					link = link.next;
				}while((typeof(link.next) == "object") && (link != chains[i].previous));
			}
		//DEBUG 
		//show all the chains
		/*for (var i = 0; i < chains.length; i++) {
			strokeColor = '#' + i + i + i + i + i + i;
			showChain(chains[i], strokeColor)
		}*/
		//EN DEBUG
			//close the chains when are not already closed
		
		// comment for debug
		 
		 	for( i = 0; i < chains.length; i++){
				if (chains[i].last.next != chains[i]){
					//search if the end and beginning of the chains[i] can be connected:
					var segmentBegin = new Segment(chains[i].previous.point, chains[i].point);
					var segmentEnd = new Segment(chains[i].last.point, chains[i].last.next.point);
					intersectionPoint = segmentBegin.IsIntersectWithSegment(segmentEnd, 'On');
					if (typeof(intersectionPoint) == 'object') {
						chains[i].last.next = new VertexLink(chains[i].last.index + 0.1, intersectionPoint, chains[i].last, chains[i]);
						chains[i].previous = chains[i].last.next;
						intersectionPoint = "undefined";
					}
					else {
						//we need to find segments to make the junction:
						//searching for a segment which cut the end segment:
						for (var j = 0; j < length - 1; j++) {
							var segmentTest = new Segment(pathOfTheArea[j], pathOfTheArea[j + 1]);
							intersectionPoint = segmentEnd.IsIntersectWithSegment(segmentTest, 'On');
							if (typeof(intersectionPoint) == 'object') {
								chains[i].last.next = new VertexLink(chains[i].last.index + 0.1, intersectionPoint, chains[i].last);
								chains[i].last = chains[i].last.next;
								segmentEnd = segmentTest;
								
								if ((pathOfTheArea[j] == chains[i].point) || (pathOfTheArea[j + 1] == chains[i].point)) {
									//close the chains[i]:
									chains[i].previous = chains[i].last;
									chains[i].last.next = chains[i];
									break;
								}
								else {
									j = 0;
									intersectionPoint = "undefined";
								}
							}
						}
					}
				}
			} 
		
	
			//creation of the paths:
			var paths = [];
			for( var i = 0; i < chains.length; i++){
				paths[i] = [];
				link = chains[i];
				var firstMaillon = link;
				var j = 0;
				do{
					paths[i][j] = link.latLng;
					link = link.next;
					j++;
				}while(link != firstMaillon);
			}
			this.setPaths(paths);
		}
		
	};
	
	areaSurroundedPolyline.isPointInsideTheArea = function(latLng){
		
		if((typeof(latLng) != 'undefined') && latLng.type == 'Point'){
			latLng = latLng.convertToLatLng();
		}
		var lineTest = new Line(0,1, -latLng.lat());
		var pathOfTheArea = areaSurroundedPolyline.getPath();
		var length = pathOfTheArea.getLength();
		//
		var intersectionPoints = [];
		
		//find the intersection between the lineTest and the border of the area
		
		//for each segment of the area:
		for(var i = 0; i < length; i++ ){
			var latLng1 = pathOfTheArea.getAt(i);
			if ((i + 1) < length)
				var latLng2 = pathOfTheArea.getAt(i+1);
			else
				var latLng2 = pathOfTheArea.getAt(0);
			
			var segment = new Segment(Point.latLngToPoint(latLng1) , Point.latLngToPoint(latLng2));
			var intersectionPoint = segment.IsIntersectWithLine(lineTest);
			
			if (intersectionPoint == true){
				//if the point is part of segment
				if (((latLng1.lng() <= latLng.lng()) && (latLng.lng() <= latLng2.lng() ))
					||
					((latLng2.lng() <= latLng.lng()) && (latLng.lng() <= latLng1.lng() ))){
						return true;
					}
			}
			else if(typeof(intersectionPoint) == 'object'){
				
				//if the intersectionPoint = latlng1
				if (intersectionPoint == latLng1){
					var lat1 = pathOfTheArea.getAt(i-1).lat() - latLng.lat();
					if ((i + 1) < length)
						var lat2 = pathOfTheArea.getAt(i+1).lat() - latLng.lat();
					else
						var lat2 = pathOfTheArea.getAt(0).lat() - latLng.lat();
					
					//if one of lat1 and lat2 positive and the other negative
					//which means that one point is higher than the lineTest
					//and the other lower than the lineTest, so the lineTest
					//cut the segment
					if (lat1 * lat2 < 0){
						intersectionPoints.push(intersectionPoint);
					}
						
				}
				//if the intersectionPoint = latlng2
				else if (intersectionPoint == latLng2){
					var lat1 = pathOfTheArea.getAt(i).lat() - latLng.lat();
					if ((i + 2) < length)
						var lat2 = pathOfTheArea.getAt(i+1).lat() - latLng.lat();
					else if (i == length - 2)
						var lat2 = pathOfTheArea.getAt(0).lat() - latLng.lat();
					else if (i == length - 1)
						var lat2 = pathOfTheArea.getAt(1).lat() - latLng.lat();
					
					//if one of lat1 and lat2 positive and the other negative
					if (lat1 * lat2 < 0){
						intersectionPoints.push(intersectionPoint);
					}					
					
				}
				else
					intersectionPoints.push(intersectionPoint);
			}
		}
		
		//treatment function of the intersection point found:
		if (intersectionPoints.length == 0){
			return false;
		}
		//if an impair number of intersection found:
		else if((intersectionPoints.length % 2) == 1){
			alert("error 235");					 	//TODO
		}
		else{
			var nbrOfXpositive = 0;
			for ( var i = 0; i < intersectionPoints.length; i++){
				
				//to test:
	/*			var intersecPolygonCircle = [];
				if (intersectionPoints[i].lat() == -2.139652)
					intersecPolygonCircle[i] = new PolygonCircle({
								map: map,
								clickable: false,
								fillColor: '#FF0000' ,
								fillOpacity: 0.4,
								strokeColor: '#FF0000',
								strokeOpacity: 0.5,
								strokeWeight: 2,
								zIndex: 10000,
								radius: 0.0002,
								center:intersectionPoints[i]
							});		
				//end test*/
				
				
				//finding the points with a higher longitude than the latlng (argument of the function)
				var valueOfSubtraction = intersectionPoints[i].convertToLatLng().lng() - latLng.lng();
				if (valueOfSubtraction > 0){
					nbrOfXpositive++;
				}
				else if (valueOfSubtraction == 0 ){
					//the intersectionPoint = latLng
					return 'border';
				}
			}
			if ((nbrOfXpositive % 2) == 1){
				//the point is inside of the area
				return true;
			}
			else
				//the point is outside of the area
				return false;	
		}
	};
	
	areaSurroundedPolyline.findIntersectionsWithBusLine = function(busline){
		return busline.isPolygonCrossed(this);
	};
	
	return areaSurroundedPolyline;
 };
 
 
if (typeof(loaded.redCreation) != 'undefined'){
 	loaded.redCreation.push('AreaSurroundedPolyline.js');
}
if (typeof(loaded.makeVirtualsBusStation) != 'undefined') {
	loaded.makeVirtualsBusStation.push('AreaSurroundedPolyline.js');
}

//DEBUG:
function showChain(firstLink, strokeColor){
	var pathArray = [];
	var currentLink = firstLink.last;
	var i = 0;
	
	
	while (typeof(currentLink) == 'object'){
		pathArray.push(currentLink.latLng);
		currentLink = currentLink.previous;
	}
	
	new gmap.Polyline({
							map: map,
							clickable: false,
							strokeColor: strokeColor,
							strokeOpacity: 1,
							strokeWeight: 1,
							zIndex: 10000,
							path: pathArray
						});	
}		
		/*			
			
			
			
						new PolygonCircle({
							map: map,
							clickable: false,
							fillColor: '#00FF00',
							fillOpacity: 0.4,
							strokeColor: '#FFFFFF',
							strokeOpacity: 0.5,
							strokeWeight: 2,
							zIndex: 10000,
							radius: 0.0002,
							center: pathOfTheArea[96].convertToLatLng(),
						});				
				
			
			
				new PolygonCircle({
							map: map,
							clickable: false,
							fillColor: '#00FF00',
							fillOpacity: 0.4,
							strokeColor: '#000000',
							strokeOpacity: 0.5,
							strokeWeight: 2,
							zIndex: 10000,
							radius: 0.0002,
							center: pathOfTheArea[97].convertToLatLng(),
						});
						
						new PolygonCircle({
							map: map,
							clickable: false,
							fillColor: '#FF0000',
							fillOpacity: 0.4,
							strokeColor: '#FFFFFF',
							strokeOpacity: 0.5,
							strokeWeight: 2,
							zIndex: 10000,
							radius: 0.0002,
							center: pathOfTheArea[1].convertToLatLng(),
						});		

							new PolygonCircle({
							map: map,
							clickable: false,
							fillColor: '#FF0000',
							fillOpacity: 0.4,
							strokeColor: '#FFFFFF',
							strokeOpacity: 0.5,
							strokeWeight: 2,
							zIndex: 10000,
							radius: 0.0002,
							center: pathOfTheArea[2].convertToLatLng(),
						});				
				
			
			
				new PolygonCircle({
							map: map,
							clickable: false,
							fillColor: '#FFFF00',
							fillOpacity: 0.4,
							strokeColor: '#000000',
							strokeOpacity: 0.5,
							strokeWeight: 2,
							zIndex: 10000,
							radius: 0.0002,
							center: pathOfTheArea[171].convertToLatLng(),
						});	
						
											
							
							new gmap.Circle({
								map: map,
								clickable: false,
								fillColor: '#00FF00',
								fillOpacity: 0.4,
								strokeColor: '#FFFFFF',
								strokeOpacity: 0.5,
								strokeWeight: 2,
								center: intersectionPoint1.convertToLatLng(),
								zIndex: 10000,
								radius: 100
							});
							
							new gmap.Circle({
								map: map,
								clickable: false,
								fillColor: '#00FF00',
								fillOpacity: 0.4,
								strokeColor: '#FFFFFF',
								strokeOpacity: 0.5,
								strokeWeight: 2,
								center: intersectionPoint2.convertToLatLng(),
								zIndex: 10000,
								radius: 100
							});
			*/
 
 
 