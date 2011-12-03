<?php
require_once 'Vertex.php';
require_once 'tools.php';
require_once 'find_nearest_roads_function.php';

$origin_lat = - 2.17;

//rectangle where to create the squares:
//TODO take in count menos number of lat and lng, and size of the integer part of the lat or lng to create the path

class Rectangle {
	public $vertex_1;
	public $vertex_2;
	
	public function __construct($lat1, $lng1, $lat2, $lng2) {
		$this->vertex_1 = new stdClass();
		$this->vertex_1->lat = $lat1;
		$this->vertex_1->lng = $lng1;
		$this->vertex_2 = new stdClass();
		$this->vertex_2->lat = $lat2;
		$this->vertex_2->lng = $lng2;
	}
}

$rec = array ();
$rec[] = new Rectangle ( - 2.17, - 79.9, - 2.175, - 79.905 );

ini_set ( 'memory_limit', '2000M' );
set_time_limit ( 500000 );
$path_to_save = "c:/nearest_squares";

create_repertory($path_to_save);

//create square size of ~ 100m by 100m with gap lat and gap lng integer
$lat_gap = 0.001; //~111.3m
$lng_gap = 0.001;

//found longitude gap:
$vertex_1 = new stdClass ();
$vertex_1->lat = $origin_lat;
$vertex_1->lng = 0;

$vertex_2 = new stdClass ();
$vertex_2->lat = $origin_lat;
$vertex_2->lng = $lng_gap;

$last_numero = 1;

while ( real_distance_between_2_vertex ( $vertex_1, $vertex_2 ) < 60 ) {
	switch ($last_numero) {
		case 1 :
			$vertex_2->lng *= 2;
			$last_numero = 2;
			break;
		
		case 2 :
			$vertex_2->lng *= 2.5;
			$last_numero = 5;
			
			break;
		
		case 5 :
			$vertex_2->lng *= 2;
			$last_numero = 1;
			
			break;
		
		default :
			exit ( "ERROR" );
	}
}

$lng_gap = $vertex_2->lng;
$multiple_of = $last_numero;

$lng_precision_len = strlen($lng_gap)-2;
$lat_precision_len = strlen($lat_gap)-2;

$lng_precision = pow(10,$lng_precision_len);
$lat_precision = pow(10,$lat_precision_len);


$max_lat_saved = -180;
$max_lng_saved = -180;
/*foreach ( $rec as $rectangle ){
	
	if ($rectangle->vertex_1->lat > $rectangle->vertex_2->lat) {
		$max_lat_of_rec = $rectangle->vertex_1->lat;
		$min_lat_of_rec = $rectangle->vertex_2->lat;
	}
	else{
		$max_lat_of_rec = $rectangle->vertex_2->lat;
		$min_lat_of_rec = $rectangle->vertex_1->lat;
	}
	
	if($max_lat_of_rec > $max_lat_saved){
		$max_lat_saved = $max_lat_of_rec;
	}
	
	if($min_lat_of_rec > $min_lat_saved){
		$min_lat_saved = $min_lat_of_rec;
	}
	
	
	if ($rectangle->vertex_1->lng > $rectangle->vertex_2->lng) {
		$max_lng_of_rec = $rectangle->vertex_1->lng;
		$min_lng_of_rec = $rectangle->vertex_2->lng;
	}
	else{
		$max_lng_of_rec = $rectangle->vertex_2->lng;
		$min_lng_of_rec = $rectangle->vertex_1->lng;
	}
	
	if($max_lng_of_rec > $max_lng_saved){
		$max_lng_saved = $max_lng_of_rec;
	}
	
	if($min_lng_of_rec > $min_lng_saved){
		$min_lng_saved = $min_lng_of_rec;
	}	
}
*/

//coordinates of squares to be created:
foreach ( $rec as $rectangle ) {
	//remove number under precision of the gap:
	$rectangle->vertex_1->lat = round($rectangle->vertex_1->lat * $lat_precision) / $lat_precision;
	$rectangle->vertex_1->lng = round($rectangle->vertex_1->lng * $lng_precision) / $lng_precision;
	$rectangle->vertex_2->lat = round($rectangle->vertex_2->lat * $lat_precision) / $lat_precision;
	$rectangle->vertex_2->lng = round($rectangle->vertex_2->lng * $lng_precision) / $lng_precision;
	
	$init = new stdClass();
	$last = new stdClass();
	if ($rectangle->vertex_1->lat > $rectangle->vertex_2->lat) {
		$init->lat = $rectangle->vertex_1->lat;
		$last->lat = $rectangle->vertex_2->lat;
	} 
	else {
		$init->lat = $rectangle->vertex_2->lat;
		$last->lat = $rectangle->vertex_1->lat;
	}
	
	if ($rectangle->vertex_1->lng > $rectangle->vertex_2->lng) {
		$init->lng = $rectangle->vertex_1->lng;
		$last->lng = $rectangle->vertex_2->lng;
	}
	else {
		$init->lng = $rectangle->vertex_2->lng;
		$last->lng = $rectangle->vertex_1->lng;
	}
	
	$lat = $init->lat - $lat_gap;
	$lng = $init->lng - $lng_gap;
	while($lat >= $last->lat){
		$lat_path = '';
		$lat_path = create_path($path_to_save, $lat, $lat_precision_len);
		
		while($lng >= $last->lng){
			$buslines = find_nearest_roads(array('lat' => $lat, 'lng' => $lng));
			$path = create_path($lat_path, $lng, $lng_precision_len);
			write_on_disk($path,$buslines);
			$lng += $lng_gap;
		}
		$lat += $lat_gap;
	}
}

function write_on_disk($path, $buslines){
	create_repertory($path);
	
	$fh = fopen($path.'/buslines', 'w') or die("can't open file\n");
	fwrite($fh, json_encode($buslines));
	fclose($fh);
}

function create_path($lat_or_lng_path, $lat_or_lng, $precision){
	$lat_or_lng_array = str_split($lat_or_lng);
	foreach($lat_or_lng_array as $numero){
		if(is_numeric($numero)){
			$lat_or_lng_path .= '/'.$numero;
			create_repertory($lat_or_lng_path);
		}
	}
	
	$lat_or_lng_after_point = abs($lat_or_lng - round($lat_or_lng));
	$length = strlen($lat_or_lng_after_point)-2;
	while($length < $precision){
		$lat_or_lng_path .= '/0';
		$length++;
	}
	return $lat_or_lng_path;
}


function create_repertory($path){
	if (! is_dir ( $path )) {
		if (! mkdir ( $path )) {
			die ( "path to save could not be created" );
		}
	}
}


