<?php

$origin_lat = -2.17;

require_once 'tools.php';
ini_set('memory_limit', '2000M');
set_time_limit(500000);
$path_to_save = "c:/nearest_squares";

if (!is_dir($path_to_save)){
	if(!mkdir($path_to_save)){
		die("path to save could not be created");
	}
}

//create square size of ~ 100m by 100m with gap lat and gap lng integer
$lat_gap = 0.001; //~111.3m
$lng_gap = 0.001;

//found longitude gap:
$vertex_1 = new stdClass();
$vertex_1->lat = $origin_lat;
$vertex_1->lng = 0;

$vertex_2 = new stdClass();
$vertex_2->lat = $origin_lat;
$vertex_2->lng = $lng_gap;

$last_numero = 1;

while( real_distance_between_2_vertex($vertex_1,$vertex_2) < 60 ){
	switch ($last_numero){
		case 1:
			$vertex_2->lng *= 2;
			$last_numero = 2;
		break;
		
		case 2:
			$vertex_2->lng *= 2.5;
			$last_numero = 5;
			
		break;
		
		case 5:
			$vertex_2->lng *= 2;
			$last_numero = 1;
			
		break;
		
		default:
			exit("ERROR");
	}
}

$lng_gap = $vertex_2->lng;
$multiple_of = $last_numero;

//coordinates of squares to be created:
