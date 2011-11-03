/**
 * @author Yoh
 */


function mainAreaAroundTroncales(){
	findingArea = true;
	//settings:
	var distance = 0.001;
	
	var j = 0;
	var i = 0;
	//create the spaces on the way of the Troncales where the rutas alimentadoras
	//can be connected only at the bus stations:
	if (typeof(troncalAreas) != 'undefined'){
		while(troncalAreas.length > 0){
			troncalAreas.shift().setMap(null);
		}
	}
	troncalAreas = [];
	
	for (i = 0; i < SubMap._busStationArray.length; i++){
		if (SubMap._busStationArray[i].type == 'mainLine'){
			troncalAreas.push(AreaSurroundedPolyline(SubMap._busStationArray[i],distance));
			troncalAreas[troncalAreas.length - 1].mergedStackedPart();
		}
	}
	//aatroncalAreas.push(AreaSurroundedPolyline(aapolylineTest,distance));
	findingArea = false;	

	//find which vertex of the alimentadoras are inside the troncalAreas:
	//for each bus line
	var k;
	var intersections = [];
	for (i = 0; i < SubMap._busStationArray.length; i++) {
		var busLine = SubMap._busStationArray[i];
		//if it s an Alimentadora
		if (busLine.type == 'feeder'){
			var polylinePath = busLine.getPath();
			var length = polylinePath.getLength();
			var enterVertex;
			var outVertex;
			
			var firstVertex = polylinePath.getAt(0);
			var lastVertex = polylinePath.getAt(length-1);
			//remove the vertex which were created by the previous calculation
			if (typeof(busLine.vertexInsideMainLineArea) != 'undefined') {
				for (j = 0; j < busLine.vertexInsideMainLineArea.length; j++) {
					enterVertex = busLine.vertexInsideMainLineArea[j].enter;
					outVertex = busLine.vertexInsideMainLineArea[j].out;
					if(enterVertex != firstVertex){
						busLine.removeVertexAtThePoint(enterVertex);
					}
					if(outVertex != lastVertex){
						busLine.removeVertexAtThePoint(outVertex);
					}
				}
			}
			busLine.vertexInsideMainLineArea = [];
			
			//calculate the new length:
			length = polylinePath.getLength();
			
			//show a green circle around the first point:
			new gmap.Circle({
				map: map,
				clickable: false,
				draggable: false,
		//		fillColor: '#000000',
		//		fillOpacity: 0.4,
				strokeColor: '#00FF00',
				strokeOpacity: 0.8,
				strokeWeight: 2,
				zIndex: 2,
				radius: 20,
				center: firstVertex
			});
	
			//show a red circle at the end of the bus line
			new gmap.Circle({
				map: map,
				clickable: false,
				draggable: false,
			//	fillColor: '#000000',
			//	fillOpacity: 0.4,
				strokeColor: '#FF0000',
				strokeOpacity: 0.8,
				strokeWeight: 2,
				zIndex: 2,
				radius: 30,
				center: lastVertex
			});
			
			//init
			var vertexInside = false;
			var previousVertexInside = false;
			var goInPoint;
			var goOutPoint;
			enterVertex = null;
			outVertex = null;
			
			var previousVertex = polylinePath.getAt(0);
			var resultIntersection;
			
			//if the first vertex is inside one of the AreaSurroundedPolyline
			for(k = 0; k < troncalAreas.length; k++){
				resultIntersection = troncalAreas[k].isPointInsideTheArea(previousVertex);
				if (( resultIntersection === true) || (resultIntersection== 'border')){
					//save the intersection point:
					goInPoint = previousVertex;
					//newGreenCircle(goInPoint);
					previousVertexInside = true;
					break;
				}

				//the vertex is inside and very close to the line, we consider it is
				// in the line
			}
			//TO DEBUG:
			var r=3;
			
			//for each vertex
			for(j = 1; j < length; j++){
				var vertex = polylinePath.getAt(j);
			
				//to debug:
			/*	if  (j < 20){
				//show a red circle at the end of the bus line
				
					new gmap.Circle({
						map: map,
						clickable: false,
						draggable: false,
						fillColor: '#000000',
						fillOpacity: 0,
						strokeColor: '#000000',
						strokeOpacity: 1,
						strokeWeight: 1,
						zIndex: 10,
						radius: r,
						center: vertex
					});
				r +=2;
					
				}	*/	
				//if the vertex is different than the previous one:
				if ( ( vertex.lng() != previousVertex.lng() ) && ( vertex.lat() != previousVertex.lat() ) ){				
					vertexInside = false;
					
					//if the vertex is inside one of the AreaSurroundedPolyline
					for(k = 0; k < troncalAreas.length; k++){
						resultIntersection = troncalAreas[k].isPointInsideTheArea(vertex);
						if ( (resultIntersection === true) || (resultIntersection === 'border') ) {
							vertexInside = true;
							break;
						}
					}
					
					//if the vertex go in the area:
					if ((previousVertexInside === false) && ( vertexInside === true )){
						if(resultIntersection === true){
							//save the intersection point:
							goInPoint = (new Segment(
								previousVertex,
								vertex
							)).nearestIntersectionOfFirstPointWithAreas().convertToLatLng();
						}
						else if (resultIntersection === 'border'){
							//save the intersection point:
							goInPoint = vertex;
						}
						
						//END TO DEBUG
					}
					
					//if the vertex go out of the area:
					if ((previousVertexInside === true) && ( vertexInside === false )){
						//save the intersection point:
						goOutPoint = (new Segment(
							previousVertex,
							vertex
						)).nearestIntersectionOfFirstPointWithAreas().convertToLatLng();

						//save the couple of go in and go out points:
						busLine.vertexInsideMainLineArea.push({
							enter: goInPoint ,
							out: goOutPoint
						});
						//if the next vertex is egual to the goOutPoint pass it
						while( ( vertex.lng() == goOutPoint.lng() ) && ( vertex.lat() == goOutPoint.lat() ) ){
							j++;
							vertex = polylinePath.getAt(j);
						}
					}
	
					previousVertexInside = vertexInside;
					previousVertex = vertex;
				}
			}
			//if the last vertex is inside one of the AreaSurroundedPolyline
			for (k = 0; k < troncalAreas.length; k++) {
				if (troncalAreas[k].isPointInsideTheArea(previousVertex) === true) {
					//save the last point:
					goOutPoint = previousVertex;
					vertexInside = true;
					break;
				}
			}
			
			if(vertexInside === true){
				//save the couple of go in and go out points:
				busLine.vertexInsideMainLineArea.push({
					enter: goInPoint,
					out: goOutPoint
				});
			}
			
			var enter;
			var out;
			
			//adding the go in an go out points to the busLine:
			for(j = 0; j < busLine.vertexInsideMainLineArea.length; j++){
				enter = busLine.vertexInsideMainLineArea[j].enter;
				if( enter != polylinePath.getAt(0) ){
					busLine.addNewVertex(enter);
				}
				newGreenCircle(enter);
				enter = null;
				
				out = busLine.vertexInsideMainLineArea[j].out;
				if( out != polylinePath.getAt(polylinePath.getLength()-1)){
					busLine.addNewVertex(out);
				}
				newRedCircle(out);
				out = null;
			}
		}
	}
	
	return true;
}

Segment.prototype.intersectWithAreas = function(){
	var intersections = [];
	var intersectionsOfOneTroncal;
	for (var i = 0; i < troncalAreas.length; i++) {
		intersectionsOfOneTroncal = this.IsIntersectWithPolygon(troncalAreas[i]);
		for( var j = 0; j < intersectionsOfOneTroncal.length; j++){
			intersections.push(intersectionsOfOneTroncal[j]);
		}
	}
	return intersections;
};

Segment.prototype.nearestIntersectionOfFirstPointWithAreas = function(){
	var intersections = this.intersectWithAreas();
	var firstPoint = this.getPt1();
	var distance;
	var shortestDistance = Infinity;
	var nearestIntersection;
	
	for (var i = 0; i < intersections.length; i++){
		distance = firstPoint.distanceOf(intersections[i].latLng);
		if(distance < shortestDistance){
			shortestDistance = distance;
			nearestIntersection = intersections[i].latLng;
		}
	}
	
	if(intersections.length > 0){
		return nearestIntersection;
	}
	else{
		return 'error';
	}
	
};

Segment.prototype.nearestIntersectionOfLastPointWithAreas = function(){
	var intersections = this.intersectWithAreas();
	var lastPoint = this.getPt2();
	var distance;
	var shortestDistance = Infinity;
	var nearestIntersection;
	
	for (var i = 0; i < intersections.length; i++){
		distance = lastPoint.distanceOf(intersections[i].latLng);
		if(distance < shortestDistance){
			shortestDistance = distance;
			nearestIntersection = intersections[i].latLng;
		}
	}
	
	return nearestIntersection;
};

function newGreenCircle(latLng){
	return new gmap.Circle({
		map: map,
		clickable: false,
		draggable: false,
		fillColor: '#00FF00',
		fillOpacity: 0.4,
		strokeColor: '#FFFFFF',
		strokeOpacity: 0.8,
		strokeWeight: 2,
		zIndex: 2,
		radius: 8,
		center: latLng	
	});
}

function newRedCircle(latLng){
	return new gmap.Circle({
		map: map,
		clickable: false,
		draggable: false,
		fillColor: '#FF0000',
		fillOpacity: 0.4,
		strokeColor: '#FFFFFF',
		strokeOpacity: 0.8,
		strokeWeight: 2,
		zIndex: 2,
		radius: 8,
		center: latLng
						
	});
}

loaded.redCreation.push('mainRedCreation.js');

 
 