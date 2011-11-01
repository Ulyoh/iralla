/**
 * @author Yoh
 */

 function Node(options){
 	this.to = new Array();
	this.from = new Array();
	this.latLng = new gmap.latLng();
	
	if (typeof(options.to) == 'object'){
		this.to = options.to;
	}
	
	if (typeof(options.from) == 'object'){
		this.from = options.from;
	}
	
	if (typeof(options.latLng) == 'object'){
		this.latLng = options.latLng;
	}
	
	this.addTo = function(node, distance, time){
		
		if ((typeof(node) == 'object') && (typeof(distance) == 'number') && (typeof(time) == 'number')){
			to = new To(node, distance, time);
			this.to.push(to);
		}
	};
	
	this.addFrom = function(node){
		this.from.push(node);
	};
	
	this.setLatLng = function(latLng){
		this.latLng = latLng;
	};
	
 }
 
 function To(node, distance, time){
 	this.node = node;
 	this.distance = distance;
	this.time = time;
 }
 
 
  loaded.redCreation.push('node.js');
 
