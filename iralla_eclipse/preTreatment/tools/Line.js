/**
 * @author yoh
 */
//ax + by + c = 0
function Line(a ,b ,c){
	this.type = 'Line';
	this.a = a;
	this.b = b;
	this.c = c;
	
	this.intersection = function(otherLine){
			
		var xx = 0;
		var yy = 0;
		//if the line isn't vertical:
		if( this.b != 0){			
			//if the otherLine isn't vertical:
			if (otherLine.b != 0){				
				//if the two lines are not parallels: 
				if ( this.a / this.b != otherLine.a / otherLine.b ){				
					//intersection calcul:
					xx = ((otherLine.c/otherLine.b) - (this.c/this.b)) / ((this.a/this.b)-(otherLine.a/otherLine.b));
					yy = - this.a/this.b * xx - this.c/this.b;
					xx = round(xx);
					yy = round(yy);
					return new Point(xx,yy);
				}
				//if the lines are merged:
				else if (  (this.a/this.b) == (otherLine.a/otherLine.b) ){
					return true;
				}
				// if the lines are parallels
				else
					return false;
			}
			//if the otherLine is vertical:
			else{
				xx = - otherLine.c/otherLine.a;
				yy = - this.a/this.b * xx - this.c/this.b;
				xx = round(xx);
				yy = round(yy);
				return new Point(xx,yy);
			}
		}
		 //if the line is vertical:	
		else
		{
			//if the otherLine isn't vertical:
			if (otherLine.b != 0)
			{
				xx = -this.c / this.a;
				yy = - otherLine.a/otherLine.b * xx - otherLine.c/otherLine.b;
				xx = round(xx);
				yy = round(yy);
				return new Point(xx,yy);
			}
			//if the lines are merged:
			else if ( this.c / this.a == otherLine.c/otherLine.a) 
			{
				return true;
			}
			// if the line are parllels
			else
				return false;
		}
	};
	
	this.IsThisPointPartOfIt = function(pt){
		if ( (-0.00000001 < (this.a * pt.x + this.b * pt.y + this.c)) && ((this.a * pt.x + this.b * pt.y + this.c) < 0.00000001)){
			return true;
		}
		else
			return false;
	};
	
	this.CreateOrthogonalByThePoint = function(point){
		
		if (this.a == 0){
			return new Line(1, 0, - point.x);
		}
		else if (this.b == 0){
			return new Line(0, 1, - point.y);
		}
		else{
			return new Line(this.b, - this.a, this.a * point.y - this.b * point.x);
		}
	};
	
	this.yIntercept = function(){
		if (b != 0)
			return - a / b;
		else
			return Infinity;
	};
	
	//angle [-PI/2;PI/2[ with x-axis
	this.angle = function(){
		var yIntercept = this.yIntercept;
		if (yIntercept == Infinity){
			return - Math.PI / 2;
		}
		else{
			return atan(yIntercept);
		}
	};
	
	//angle [0;PI[ with x-axis
	this.angle2 = function(){
		return (this.angle + Math.PI);	
	};
	
	this.BisectorWith = function(line){
		var angleNewLine = (this.angle2 + Line.angle2) / 2;
		
		if (angleNewLine != Math.PI / 2)
			return Line.LineFromSlopeAndPoint(Math.tan(angleNewLine), point);
		else
			return Line.LineX(point.x);
	};
	
	//return a string depending on the position of the point compared with the line
	//values: 'up', 'down', 'right', 'left', 'coinsided'
	// right and left are used only if the line is vertical
	this.positionOf = function(point){
		//if the line is vertical:
		if(this.b == 0){
			var x = -this.c / this.a;
			
			if (point.x == x){
				return 'coinsided';
			}
			else if ( point.x < x){
				return 'left';
			}
			else
				return 'right';
		}
		else{
			var y = round( (-this.a * point.x - this.c) / this.b ,10);
			
			if (point.y == y){
				return 'coinsided';
			}
			else if ( point.y < y){
				return 'down';
			}
			else
				return 'up';
		}
	};
	
	this.intersectWithPolyline = function(polyline, firstIndex, endIndex){
		var path = polyline.getPath();
		var segment;
		var result = false;
		
		if (typeof(firstIndex) == "indefined")
			firstIndex = 0;
			
		if (typeof(endIndex) == "indefined")
			endIndex = path.getLength()-1;
		
		for ( var i = firstIndex; i < endIndex; i++){
			segment = new Segment( Point.latLngToPoint(path.getAt(i)), Point.latLngToPoint(path.getAt(i+1)));
			result = segment.IsIntersectWithLine(this);
			if (result != false)
				break;
		}
		return result;
	};
}

//creation of a line from two point:
Line.Line2pts = function(pt1, pt2){

	//if the points don't have the same x-coordinates 
	if (pt1.x != pt2.x)
	{
		var a = (pt1.y - pt2.y) / ( pt1.x - pt2.x);
		var b = -1;
		var c = pt1.y - a * pt1.x;
	}
	//else
	else
	{
		var a = -1;
		var b = 0;
		var c = pt1.x;
	}
	return new Line(a,b,c);
};

//creation of a line from slope and y-intercept:
Line.LineAxPlusB = function(slope,yIntercept){
	return new Line(slope, -1, yIntercept);
};

//creation of a line from slope and one point:
Line.LineFromSlopeAndPoint = function(slope, point){
	return new Line(slope, -1, point.y - slope * point.x);
};

//creation of a vertical line:
Line.LineX = function(x){
	return new Line(-1, 0, x);
};

//to verify the file is loaded
loaded.tools.push('Line');

