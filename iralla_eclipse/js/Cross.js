/**
 * @author Yoh
 */

 /**
  * Cross
  *		handle the show of a cross
  */
 
 //static properties:
 //"private":
Cross._image = "data/unvalid.png";				//put with the constantes variables
 
function Cross(objectAttachFrom){
	//non-static properties:
	//public:
	this._objectAttachFrom = objectAttachFrom;
	//"private":
	this._coord = new gmap.Point(0, 0);
	//private:
	var _divNode = document.createElement('div');
	var _imgNode = document.createElement('img');
	_imgNode.cross = this;
	
	//constructor:
	this._objectAttachFrom._cross = this;
	//div node parameters:	
	_divNode.style.display = 'none';
	_divNode.style.position = 'absolute';
	_divNode.style.width = '22px';
	_divNode.style.zIndex = '900';
	_divNode.className = 'crossDiv';
	//img node parameters:
	_imgNode.src = Cross._image;
	_imgNode.className = 'crossImg';
	_imgNode.setAttribute('alt', 'sacar'); //TRADUCTION
	// append nodes  to the body
	var body = document.getElementsByTagName('body')[0];
	body.appendChild(_divNode);
	_divNode.appendChild(_imgNode);
	
	
	this.show = function(coord){
		this._coord = coord;
		/*this.*/
		_divNode.style.left = coord.x + 'px';
		/*this.*/
		_divNode.style.top = coord.y + 'px';
		/*this.*/
		_divNode.style.display = 'block';
		/*this.*/
		_imgNode.style.display = 'block';
	};
	
	this.showLatLng = function(theMap, latLng){
		var coord = theMap.convertLatLngToPixelCoord(latLng);
		this.show(coord);
	};
	
	this.hide = function(){
		_divNode.style.display = 'none'; ///remove from the node from the body FERIFY IF IT WORKS
		_imgNode.style.display = 'none';
	};
	
	//add a fonction to a listener on the cross, if the listener doesn t exist, it s created:
	// // itsArgs[0] = this of newFunction
	this.addFunctionsToListener = function(event, newFunction, itsArgs){
		var self = this;
		if (!(this.listeners)){
			this.listeners = {};
		}
		if ((this.listeners._id === null)){
			this.listeners._id = 0;
		}
		
		//if there is no listeners created for this event:
		if (!(this.listeners[event])) {
			//this.listeners[event] = new Object();
			
			//set of the listener:
			this.listeners[event] = gmap.event.addDomListener(_imgNode, event, function(MouseEvent){
				var l = this.cross.listeners[event]._functions.length;
				
				//copy of the function and args in case the function change or remove _cross
				var _functions = this.cross.listeners[event]._functions;
				var _args = this.cross.listeners[event]._args;
				
				//for each function to execute with the event:
				for (var i = 0; i < l; i++) {
					var myFunction = _functions[i];
					
					//copy the arguments corresponding to the function
					var args = [];
					for (var k = 0; k < _args[i].length; k++) {
						args[k] = _args[i][k];
					}
					for (var j = 0; j < args.length; j++) {
						if (typeof(args[j]) == "string") {
							if (args[j].slice(0, 6) == "eVeNt:"){
								args[j] = eval(args[j].slice(6));
							}
						}
					}
					
					myFunction.apply(args[0], args.slice(1));
				}
			//end fo listener
			});
			this.listeners[event]._functions = [];
			this.listeners[event]._args = [];
			this.listeners[event]._id = [];
		}
		
		this.listeners[event]._functions.push(newFunction);
		this.listeners[event]._args.push(itsArgs);
		this.listeners[event]._id.push(this.listeners._id);
		
		return this.listeners._id++;
	};
	
	this.destructor = function(){
		_imgNode.parentNode.removeChild(_imgNode);
		_divNode.parentNode.removeChild(_divNode);
		this._objectAttachFrom._cross = null;
	};
}


Cross.prototype.removeFunctionsToListeners = gmap.Map.prototype.removeFunctionsToListeners;