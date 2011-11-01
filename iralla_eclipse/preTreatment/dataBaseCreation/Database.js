/**
 * @author Yoh
 */



Database = function(subMap, bounds, stepNbreOnWidthSize, stepNbreOnHeightSize){

	var cst = 0.0000001;  //to be sure the points of the buslines are not in one of the 4 segment of the field
	var fieldIndex = 0;
	var xIndexOfCurrentField = 0;
	var yIndexOfCurrentField = 0;
	var m = 0;
	
	var ptNW = new Object();
	var ptNE = new Object();
	var ptSW = new Object();
	var ptSEbis = new Object();
	
	var busLine = new gmap.Polyline();

	//determination of the size area:
	var northY = bounds.getNorthEast().lat();
	var southY = bounds.getSouthWest().lat();
	var westX = bounds.getSouthWest().lng();
	var eastX = bounds.getNorthEast().lng();
	
	var widthSize = eastX - westX;
	var heightSize = northY - southY ;
	
	//determination of the steps:
	var stepX = Math.ceil(widthSize / stepNbreOnWidthSize * 100000) / 100000;
	var stepY = Math.ceil(heightSize / stepNbreOnHeightSize * 100000) / 100000;
	
	//determination of the coordinates of the origin point:
	var originPt = new Object();
	originPt = {
		x: westX,
		y: northY
	};
	
	/*//create the database:
	var dataBase = new Array();
	for (var x = 0; x <= stepNbreOnWidthSize; x++) {
		dataBase[x] = new Array();
		for (var y = 0; y <= stepNbreOnHeightSize; y++) 
			dataBase[x][y] = new Array();
	}*/
	
	var arrayOfSelectedPolyline = new Array();
	
	//get all buslines to save in the database:
	var allBusLinesArray = new Array();
	
							/* ************** */
	
	for(var i = 0; i < subMap.getLengthOfBusLinesArray(); i++)
		allBusLinesArray.push(subMap.getBusLinesArray(i));
	
	//for each bus line:
	for ( var k = 0; k < allBusLinesArray.length ; k++){
	
		/*var dt = new Date();
		while ((new Date()) - dt <= 2000)*/
		
		busLine = allBusLinesArray[k];
		//busLine.polygons = new Array();
		
		//create a place to save all the fields where pass the polyline:
		busLine.fields = new Array();
		fieldIndex = 0;
		
		//add listener to show all the field when the polyline is clicked:
		/*busLine.listener = gmap.event.addListener(busLine, 'click', function(){
			this.polygons = new Array();
			//create the polygons to show:
			for(var i = 0; i < busLine.fields.length(); i++){
				var NW = new gmap.latLng(busLine.fields[i].ptNW.y, busLine.fields[i].ptNW.x);
				var NE = new gmap.latLng(busLine.fields[i].ptNE.y, busLine.fields[i].ptNE.x);
				var SE = new gmap.latLng(busLine.fields[i].ptSEbis.y, busLine.fields[i].ptSEbis.x);
				var SW = new gmap.latLng(busLine.fields[i].ptSW.y, busLine.fields[i].ptSW.x);

				this.polygons[i] = new gmap.Polygon({
					paths: [NW,NE,SE,SW],
					strokeColor: '#DDDDDD',
					zIndex:1,
				});
				
			}
			
		});*/
		
		var vertexArray = busLine.getPath();
		
		//for the first segment :

		//find in which field of the database is the first point of the polyline:
		var firstPt = {
				x: vertexArray.getAt(0).lng() + cst,
				y: vertexArray.getAt(0).lat() + cst
			};
			
		var x = (firstPt.x - originPt.x) / stepX;
		var y = - (firstPt.y - originPt.y) / stepY;
			
		xIndexOfCurrentField = Math.floor(x);
		yIndexOfCurrentField = Math.floor(y);
			
		//create the four segment limiting corresponding to
		//the field of dataBase[x][y] in which is the first
		//point of the polyline
				
		/////////////////////
		//
		//	     x1   x2
		//	  y1 +----+
		//		 |	  |
		//	  y2 +----+
		//
		/////////////////////
		
		//create the coordinates for the 4 points:
		var x1 = originPt.x + xIndexOfCurrentField * stepX;
		x1 = Math.floor(x1 * 100000) / 100000;
		var y1 = originPt.y - yIndexOfCurrentField * stepY;
		y1 = Math.floor(y1 * 100000) / 100000;
		var x2 = x1 + stepX;
		var y2 = y1 - stepY;
						
		ptNW = {x: x1, y: y1};
		ptNE = {x: x2, y: y1};
		ptSW = {x: x1, y: y2};
		ptSEbis = {x: x2, y: y2};
			
		//record the busline in the current database field
		//dataBase[xIndexOfCurrentField][yIndexOfCurrentField].push(busLine);
			
		//saved the field in the polyline:
		busLine.fields[fieldIndex] = xIndexOfCurrentField + "_" + yIndexOfCurrentField;
		
		/*new gmap.LatLngBounds(
			new gmap.LatLng(ptNE.y, ptNE.x),
			new gmap.LatLng(ptSW.y, ptSW.x)
			
		);*/
		fieldIndex++;
		
		

		var alreadyCrossed = "none";
		
		//find by which segment the polyline go out:
		//for each segment of bus line:
		for ( m = 0; m < vertexArray.getLength() - 1; m++){
			var pt1 = new Object();
			var pt2 = new Object();
						
		//create segment between two vertex:
			pt1 = {
				x: vertexArray.getAt(m).lng() + cst,
				y: vertexArray.getAt(m).lat() + cst
			};
			pt1.x = Math.round(pt1.x * 10000000) / 10000000;
			pt1.y = Math.round(pt1.y * 10000000) / 10000000;
			
			pt2 = {
				x: vertexArray.getAt(m+1).lng() + cst,
				y: vertexArray.getAt(m+1).lat() + cst
			};
			pt2.x = Math.round(pt2.x * 10000000) / 10000000;
			pt2.y = Math.round(pt2.y * 10000000) / 10000000;
			
			var segment = new Segment(pt1 , pt2);		
				
			//create the 4 segments which delimit the field :
			var segmentNorth = new Segment(ptNW, ptNE);
			var segmentEast  = new Segment(ptNE, ptSEbis);
			var segmentSouth = new Segment(ptSEbis, ptSW);
			var segmentWest  = new Segment(ptSW, ptNW);	
			
			//test if the second point of the segment is in one segment of the field:
			
			
			
			//test if one of the extremities of the field is part of the segment
			if ((segment.IsThisPointPartOfIt(ptNW)== true) 
			&& (alreadyCrossed != "ptNW")){
				
				moveToNewField(-1,1);
				
				//remind from which segment or point in the new field the segment come from:
				//it cross the segmentNorth so it s the segmentSouth for the next field:
				alreadyCrossed = "ptSEbis";
			}
			else if ((segment.IsThisPointPartOfIt(ptNE) == true) 
			&& (alreadyCrossed != "ptNE")){
				
				moveToNewField(1,1);
				
				//remind from which segment or point in the new field the segment come from:
				//it cross the segmentNorth so it s the segmentSouth for the next field:
				alreadyCrossed = "ptSW";
			}
			else if ((segment.IsThisPointPartOfIt(ptSEbis)== true) 
			&& (alreadyCrossed != "ptSEbis")){
				
				moveToNewField(1,-1);
				
				//remind from which segment or point in the new field the segment come from:
				//it cross the segmentNorth so it s the segmentSouth for the next field:
				alreadyCrossed = "ptNW";
			}
			else if ((segment.IsThisPointPartOfIt(ptSW)== true) 
			&& (alreadyCrossed != "ptSW")){
				
				moveToNewField(-1,-1);
				
				//remind from which segment or point in the new field the segment come from:
				//it cross the segmentNorth so it s the segmentSouth for the next field:
				alreadyCrossed = "ptNE";
			}
			
			//test if segment cross with one of the delimited zone:
			else if ((segment.IsIntersectWithSegment(segmentNorth) == true) 
			&& (alreadyCrossed != "north") && (alreadyCrossed != "ptNW") && (alreadyCrossed != "ptNE") ){
				
				moveToNewField(0,1);
				
				//remind from which segment or point in the new field the segment come from:
				//it cross the segmentNorth so it s the segmentSouth for the next field:
				alreadyCrossed = "south";
			}
			else if ((segment.IsIntersectWithSegment(segmentSouth) == true)
			&& (alreadyCrossed != "south") && (alreadyCrossed != "ptSEbis") && (alreadyCrossed != "ptSW")){
				
				moveToNewField(0,-1);
				
				//remind from which segment or point in the new field the segment come from:
				//it cross the segmentSouth so it s the segmentNorth for the next field:
				alreadyCrossed = "north";
			}
			else if ((segment.IsIntersectWithSegment(segmentWest) == true)
			&& (alreadyCrossed != "west") && (alreadyCrossed != "ptNW") && (alreadyCrossed != "ptSW")){
				
				moveToNewField(-1,0);
				
				//remind from which segment or point in the new field the segment come from:
				//it cross the segmentWest so it s the segmentEast for the next field:
				alreadyCrossed = "east";
			}
			else if ((segment.IsIntersectWithSegment(segmentEast) == true) &&
			(alreadyCrossed != "east") && (alreadyCrossed != "ptNE") && (alreadyCrossed != "ptSEbis")) {
				
				moveToNewField(1,0);
				
				//remind from which segment or point in the new field the segment come from:
				//it cross the segmentEast so it s the segmentWest for the next field:
				alreadyCrossed = "west";
			}
			else 
				alreadyCrossed = "none";
		}
	}
	
	 function moveToNewField(xMove, yMove){
		//set the new field
		ptNW.x += xMove * stepX;
		ptNE.x += xMove * stepX;
		ptSW.x += xMove * stepX;
		ptSEbis.x += xMove * stepX;
		ptNW.y += yMove * stepY;
		ptNE.y += yMove * stepY;
		ptSW.y += yMove * stepY;
		ptSEbis.y += yMove * stepY;
				
		//to resolve a bug:
		ptNW.x = Math.round(ptNW.x * 100000) / 100000;
		ptNE.x = Math.round(ptNE.x * 100000) / 100000;
		ptSW.x = Math.round(ptSW.x * 100000) / 100000;
		ptSEbis.x = Math.round(ptSEbis.x * 100000) / 100000;
		ptNW.y = Math.round(ptNW.y * 100000) / 100000;
		ptNE.y = Math.round(ptNE.y * 100000) / 100000;
		ptSW.y = Math.round(ptSW.y * 100000) / 100000;
		ptSEbis.y = Math.round(ptSEbis.y * 100000) / 100000;
				
		//move index to the new field:
		xIndexOfCurrentField += xMove;
		yIndexOfCurrentField -= yMove;
				
		//record the busline in the new database field
		//dataBase[xIndexOfCurrentField][yIndexOfCurrentField].push(busLine);
				
		//saved the field in the polyline:
		busLine.fields[fieldIndex] = xIndexOfCurrentField + "_" + yIndexOfCurrentField;
		
		/*new gmap.LatLngBounds(
			new gmap.LatLng(ptNE.y, ptNE.x),
			new gmap.LatLng(ptSW.y, ptSW.x)
			
		);*/
		
		//next loop should be done with the same segment:
		//in case this segment cross more than one field
		m--;
		
	/*	//create the polygon to show:
		var NW = new gmap.LatLng(busLine.fields[fieldIndex].getNorthEast().lat(), busLine.fields[fieldIndex].getSouthWest().lng());
		var NE = new gmap.LatLng();
		NE = busLine.fields[fieldIndex].getNorthEast();
		var SE = new gmap.LatLng(busLine.fields[fieldIndex].getSouthWest().lat(), busLine.fields[fieldIndex].getNorthEast().lng());
		var SW = new gmap.LatLng();
		SW = busLine.fields[fieldIndex].getSouthWest();
	
		busLine.polygons[fieldIndex] = new gmap.Polygon({
			map: busLine.map,
			paths: [NW,NE,SE,SW,NW],
			strokeColor: '#000000',
			zIndex: 2000,
			fillColor: '#DDDDDD',
		});
		
		
		busLine.polygons[fieldIndex].fieldIndex = fieldIndex;
		
		
		busLine.polygons[fieldIndex].listener = gmap.event.addListener(busLine.polygons[fieldIndex], 'mouseover', function(MouseEvent){
			var position = new gmap.Point();
			position = this.map.convertLatLngToPixelCoord(MouseEvent.latLng)
			var myInfo = document.getElementById('myInfo');
			myInfo.innerHTML = this.fieldIndex;
			myInfo.style.display = "block";
			myInfo.style.left = position.x + "px";
			myInfo.style.top = (position.y - 30) + "px";
			
			var listenerHideBubbleHandle =  google.maps.event.addListener(this, 'mouseout', function(){
					myInfo.style.display = "none";
					google.maps.event.removeListener(listenerHideBubbleHandle);
				});
	  	});
	*/	
		fieldIndex++;
	}
	
	
	//return dataBase;
};



loaded.dataBaseCreation.push('Database');





