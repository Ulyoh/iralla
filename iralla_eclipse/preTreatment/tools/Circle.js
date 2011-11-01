/**
 * @author Yoh
 */

function Circle(center, radius){
	this.type = 'Circle';
 	this.center = center;
	this.radius = radius;
	
	this.intersectWith = function(lineOrSegment){
		if (lineOrSegment.type == "Line") {
			var line = lineOrSegment;
		}
		else {
			var line = lineOrSegment.supportLine;
			var segment = lineOrSegment;
		}
		
		//found the line throw the center of the circle and perpendicular at the line:
		var orthogonal = line.CreateOrthogonalByThePoint(this.center);
			
		//intersection point between line and orthogonal:
		var intersectionPoint = line.intersection(orthogonal);
		
		//distance from the center of the circle to the line:
		var distance = intersectionPoint.distanceOf(this.center);
		
		if (distance > this.radius){
			return false;
		}
		else {
			if (lineOrSegment.type == "Line") {
				return intersectionPoint;
			}
			else {
				var pt1 = segment.getPt1();
				var pt2 = segment.getPt2();
				
				//if the intersection point is inside the segment:
				if ((((pt1.x <= intersectionPoint.x) && (intersectionPoint.x <= pt2.x) ||
				(pt2.x <= intersectionPoint.x) && (intersectionPoint.x <= pt1.x))) &&
				(((pt1.y <= intersectionPoint.y) && (intersectionPoint.y <= pt2.y) ||
				(pt2.y <= intersectionPoint.y) && (intersectionPoint.y <= pt1.y)))) {
					return intersectionPoint;
				}
				//if one extremity is at a distance < radius:
				else if (center.distanceOf(pt1) < radius){
					return 'pt1 of segment';
				}
				else if (center.distanceOf(pt2) < radius){
					return 'pt2 of segment';
				}
				else {
					return false;
				}
			}
		}
	};	
}

//to verify the file is loaded
loaded.tools.push('Circle');
 
