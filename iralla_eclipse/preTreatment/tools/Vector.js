/**
 * @author Yoh
 */
/*
 * possible combination of arguments :
 * (initialPoint, terminalPoint)
 * (terminalPoint)
 * (magnitude, angle )
*/
function Vector(){
	var args = arguments;
	this.Vector = 'Vector';
	//1 point
	if (arguments.length == 1){
		this.x = args[0].x;
		this.y = args[0].y;
	}
	else if (args.length == 2) {
        //polar coordinates:
		if ( (typeof(args[0]) == 'number')&& (typeof(args[1]) == 'number') ){
			this.x = round(args[0] * Math.cos(args[1]));
			this.y = round(args[0] * Math.sin(args[1]));
		}
        //2 points
        else if ( (args[0].type == 'Point') && (args[1].type == 'Point') ){
			this.x = args[1].x - args[0].x;
			this.y = args[1].y - args[0].y;
		}
	}

	//(fr: produit vectoriel)
	this.crossProduct = function(){
		
		
	};
	
	//(fr: produit scalaire)
	this.dotProduct = function(secondVector){
		return round((this.x * secondVector.x + this.y * secondVector.y));
	};
	
	this.determinant = function(){
		return round((this.x * this.y - this.y * this.x));
	};
	
	//(fr: norme)
	this.magnitude = function(){
		var origine = new Point(0,0);
		return origine.distanceOf(this);
	};
	/*
	this.calculAngleWith = function(secondVector){
		if (this.determinant() != 0)
			return round((this.determinant() / Math.abs(this.determinant()) * this.dotProduct(secondVector) / (this.magnitude * secondVector.magnitude)));
		else
			return false;
	}*/
	
	this.getAngleWith = function(vector){
		var angle = round(Math.atan2(vector.y, vector.x) - Math.atan2(this.y, this.x));
		
		while(angle > Math.PI){
			angle -= 2 * Math.PI;
		}
		while(angle < -Math.PI){
			angle += 2 * Math.PI;
		}
		return round(angle,10);	
	};
	
	this.getAngleWithXAxis = function(){
		return round(Math.atan2(this.y, this.x),10);
	};
	
	this.rotate = function(angle){
		var magnitude = this.magnitude();
		angle =  this.getAngleWithXAxis() + angle;

		while(angle > Math.PI){
			angle -= 2 * Math.PI;
		}
		while(angle < -Math.PI){
			angle += 2 * Math.PI;
		}
		return new Vector( magnitude, round(angle,10) );
	}
	;
	this.setMagnitude = function(newMagnitude){
		var oldMagnitude = this.magnitude();
		if(oldMagnitude == 0){
			return this;
		}
		this.x = round(this.x * newMagnitude / oldMagnitude, 10);
		this.y = round(this.y * newMagnitude / oldMagnitude, 10);
		return this;
	};
	
	this.addTo = function(vector){
		return new Vector(this.x + vector.x, this.y + vector.y);
	};
	
	
}

//to verify the file is loaded
loaded.tools.push('Vector');

