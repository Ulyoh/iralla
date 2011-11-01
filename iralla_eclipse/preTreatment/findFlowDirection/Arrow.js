/**
 * @author Yoh
 */
/*
 * opts.vector
 * opts.point
 * opts.length
 *
 */

function Arrow(opts){

    var arrow = new gmap.Polygon({
           clickable: true,
           fillColor: opts.color,
           fillOpacity: 0.5,
           map: map,
           zIndex: opts.zIndex,
           strokeColor: "#FFFFFF",
           strokeOpacity: 0.5,
           strokeWeight: 0.5
        });

    //set the side to length/2
    opts.vector.setMagnitude(opts.length/2);
    //create a opts.vector at 90°de opts.vector:
    var orthVector = opts.vector.rotate(Math.PI/2);
    //the 2 possibles arrays of the path:
    var pathNormal = [];
    var pathOpposite = [];
	
	//record the busline:
	this.busLine = opts.busLine;

    var point = Point.latLngToPoint(opts.latLng);

    pathNormal[0]= point.convertToLatLng();
    pathNormal[1]= point.addVector(orthVector).addVector(opts.vector).convertToLatLng();
    pathNormal[2]= point.subVector(opts.vector).convertToLatLng();
    pathNormal[3]= point.subVector(orthVector).addVector(opts.vector).convertToLatLng();

    pathOpposite[0]= point.convertToLatLng();
    pathOpposite[1]= point.addVector(orthVector).subVector(opts.vector).convertToLatLng();
    pathOpposite[2]= point.addVector(opts.vector).convertToLatLng();
    pathOpposite[3]= point.subVector(orthVector).subVector(opts.vector).convertToLatLng();

    //public methods:
    arrow.setFlow = function(flow){
        if ((typeof(flow) == 'undefined') || (flow == "normal")){
            arrow.setPath(pathNormal);
        }
        else if ((typeof(flow) != 'undefined') && (flow == "reverse")){
            arrow.setPath(pathOpposite);
        }
        else if ((typeof(flow) != 'undefined') && (flow == "both")){
			opts.vector.setMagnitude(opts.length/2.2);
			for( var i = 0; i < pathNormal.length; i++){
				pathNormal[i] = Point.latLngToPoint(pathNormal[i]).subVector(opts.vector).convertToLatLng();
				pathOpposite[i] = Point.latLngToPoint(pathOpposite[i]).addVector(opts.vector).convertToLatLng();
			}
            arrow.setPath(pathNormal.concat(pathOpposite));
        }
		arrow.flow = flow;
    };

    //constructor:
    arrow.setFlow(opts.flow);
	
	return arrow;
}

if (typeof(loaded.findFlowDirection) != 'undefined'){
	loaded.findFlowDirection.push('Arrow.js');
}

 