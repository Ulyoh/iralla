/**
 * @author Yoh
 */

function Segment(pt1, pt2){
	this.Segment = 'Segment';
	if((typeof(pt1.type) == 'undefined') || (pt1.type != 'Point')){
		pt1 = Point.latLngToPoint(pt1);
	}
	if((typeof(pt2.type) == 'undefined') || (pt2.type != 'Point')){
		pt2 = Point.latLngToPoint(pt2);
	}
	
	this._pt1 = new Point(pt1.x, pt1.y);
	this._pt2 = new Point(pt2.x, pt2.y);
	this.supportLine = new Line.Line2pts(this._pt1, this._pt2);
	this.type = 'Segment';
	
	this.isEgalTo = function(segment){
		if (((this._pt1.x == segment._pt1.x) && (this._pt1.y == segment._pt1.y) &&
		(this._pt2.x == segment._pt2.x) &&
		(this._pt2.y == segment._pt2.y)) ||
		((this._pt1.x == segment._pt2.x) && (this._pt1.y == segment._pt2.y) &&
		(this._pt2.x == segment._pt1.x) &&
		(this._pt2.y == segment._pt1.y))) 
			return true;
		else 
			return false;
	};
	
	this.setPt1 = function(pt){
		this._pt1.x = pt.x;
		this._pt1.y = pt.y;
	};
	
	this.setPt2 = function(pt){
		this._pt2.x = pt.x;
		this._pt2.y = pt.y;
	};
	
	this.setPts = function(pt1, pt2){
		this._pt1 = new Point(pt1.x, pt1.y);
		this._pt2 = new Point(pt2.x, pt2.y);
	};
	
	this.getLength = function(){
		return this._pt1.distanceOf(this._pt2);
	};
	
	this.getSupportLine = function(){
		return this.supportLine;
	};
	
	this.getPt1 = function(){
		return this._pt1;
	};
	
	this.getPt2 = function(){
		return this._pt2;
	};
	
	this.IsThisPointPartOfIt = function(point){
		//if the point is in the bounds formed by the two extremities of the segment:
		if (((this._pt1.x < point.x) && (point.x < this._pt2.x)) ||
		((this._pt2.x < point.x) && (point.x < this._pt1.x)) &&
		((this._pt1.y < point.y) && (point.y < this._pt2.y)) ||
		((this._pt2.y < point.y) && (point.y < this._pt1.y))) {
		
			return this.supportLine.IsThisPointPartOfIt(point);
		}
		return false;
	};
	
	this.getCenterOfSegment = function(){
		return new Point((this._pt1.x + this._pt2.x) / 2, (this._pt1.y + this._pt2.y) / 2);
	};
	
	this.IsIntersectWithLine = function(testedLine){
		var point = this.supportLine.intersection(testedLine);
		
		if (point == false) 
			return false;
		else 
			if (point == true) 
				return true;
			else {
				//is the point in the segment:
				if (((this._pt1.x <= point.x) && (point.x <= this._pt2.x)) || ((this._pt2.x <= point.x) && (point.x <= this._pt1.x))) {
					if (((this._pt1.y <= point.y) && (point.y <= this._pt2.y)) || ((this._pt2.y <= point.y) && (point.y <= this._pt1.y))) {
						return point;
					}
				}
				
				
				return false;
			}
	};
	
	this.IsIntersectWithSegment = function(testedSegment, returnValueOnOff){
	
		var test = this.IsIntersectWithLine(testedSegment.getSupportLine());
		
		//if the segment is merged with the suport line of the tested segment:
		if (test == true) {
			//is the segment have a comun part :
			if ((((this._pt1.x <= testedSegment.getPt1().x) && (testedSegment.getPt1().x <= this._pt2.x)) ||
			((this._pt2.x <= testedSegment.getPt1().x) && (testedSegment.getPt1().x <= this._pt1.x)) ||
			((this._pt1.x <= testedSegment.getPt2().x) && (testedSegment.getPt2().x <= this._pt2.x)) ||
			((this._pt2.x <= testedSegment.getPt2().x) && (testedSegment.getPt2().x <= this._pt1.x))) &&
			(((this._pt1.y <= testedSegment.getPt1().y) && (testedSegment.getPt1().y <= this._pt2.y)) ||
			((this._pt2.y <= testedSegment.getPt1().y) && (testedSegment.getPt1().y <= this._pt1.y)) ||
			((this._pt1.y <= testedSegment.getPt2().y) && (testedSegment.getPt2().y <= this._pt2.y)) ||
			((this._pt2.y <= testedSegment.getPt2().y) && (testedSegment.getPt2().y <= this._pt1.y)))) 
				return true;
			else 
				return false;
		}
		//if the supports lines are parallels:
		else 
			if (test == false) 
				return false;
			//if the supportLine of the tested segment cross the this segment
			//ie the point saved in test is in the tested segment:
			else {
				if ((((testedSegment.getPt1().x <= test.x) && (test.x <= testedSegment.getPt2().x)) ||
				((testedSegment.getPt2().x <= test.x) && (test.x <= testedSegment.getPt1().x))) &&
				(((testedSegment.getPt1().y <= test.y) && (test.y <= testedSegment.getPt2().y)) ||
				((testedSegment.getPt2().y <= test.y) && (test.y <= testedSegment.getPt1().y)))) 
					if ((returnValueOnOff == 'On') || (returnValueOnOff == 'on')) {
						return test;
					}
					else 
						return true;
				else 
					return false;
				
			}
	};
	
	//TO DO : make it working with all polygon and polyline
	this.IsIntersectWithPolygon = function(polygon, indexException){
		var path;
		//extract array of vertex:
		if(typeof(polygon.getPaths)!= 'undefined'){
			var paths = polygon.getPaths();
		}
		else{
			var paths = new gmap.MVCArray([polygon.getPath()]);
		}
		var intersections = new Array();
		var result;
		var segmentPolygon;
		
		for( var h = 0; h < paths.getLength(); h++) {
			path = paths.getAt(h);
			loop1: for (var i = 0; i < path.getLength() - 1; i++) {
				//is the index part of the indexException:
				if(typeof(indexException) != 'undefined'){
					for ( var j = 0; j < indexException.length; j++){
						if ( i == indexException[j])
							continue loop1;
					}				
				}
	
				var segmentPolygon = new Segment(Point.latLngToPoint(path.getAt(i)), Point.latLngToPoint(path.getAt(i + 1)));
				result = this.IsIntersectWithSegment(segmentPolygon, 'on');
				if (result == true) {
					intersections.push({
						type: "tangente",
						latLng: path.getAt(i)
					});
				}
				else 
					if ((result == path.getAt(i)) || (result == path.getAt(i + 1))) {
						intersections.push({
							type: "tangente",
							latLng: result
						});
					}
					else 
						if (result != false) {
							intersections.push({
								type: true,
								latLng: result
							});
						}
			}
			segmentPolygon = new Segment(Point.latLngToPoint(path.getAt(path.getLength() - 1)), Point.latLngToPoint(path.getAt(0)));
			result = this.IsIntersectWithSegment(segmentPolygon, 'on');
			if (result == true) {
				intersections.push({
					type: "tangente",
					latLng: path.getAt(i)
				});
			}
			else {
				if ((result == path.getAt(i)) || (result == path.getAt(i + 1))) {
					intersections.push({
						type: "tangente",
						latLng: result
					});
				}
				else {
					if (result != false) {
						intersections.push({
							type: true,
							latLng: result
						});
					}
				}
			}
		}
		return intersections;
	};
	
	this.IsIntersectWithPolygonCircle = this.IsIntersectWithPolygon;
	
	this.IsIntersectWithPolyline = this.IsIntersectWithPolygonCircle;
	
	
	this.proyectionOf = function(point){
		//                             (find the orthogonal line to the segment which pass by point:)
		var result = this.IsIntersectWithLine(this.getSupportLine().CreateOrthogonalByThePoint(point));
		//if the point is include to the supportLine of the segment 
		if (result == true) {
			//find which extremity is the nearest to point:
			if (this.getPt1.distanceOf(point) < this.getPt2.distanceOf(point)) 
				return this.getPt1;
			else 
				return this.getPt2;
		}
		else
			return result;
	};
	
	
}
//to verify the file is loaded
loaded.tools.push('Segment');
