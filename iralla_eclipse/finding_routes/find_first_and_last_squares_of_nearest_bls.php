<?php
require_once 'functions_for_finding_route_3.php';
/**
 * 
 * @param Point $lat_lng_pt
 * 
 * find the first and last squares of the nearest part of bus lines
 */
function find_first_and_last_square_of_nearest_bls(Point $lat_lng_pt){
	
	//arguments tests:
	if (get_class($lat_lng_pt) != 'Point'){
		die("arguments not valids");
	}
	
	//find nearest squares
	$req = $lat_lng_pt->find_nearest_squares();
	
	//keep first and last squares for each following ones by bus lines parts
	$lat_lng_pt->first_and_last_squares = keep_first_and_last_squares_by_bl_part($req);
	
}



function keep_first_and_last_squares_by_bl_part($req){
	
	//for each group of squares of a part of busline
	//in the mysql selection
	//keep only the first square and the last square
	$squares = array();
	
	//init:
	$square = $req->fetch();
	$first_and_last_square = array();
	$first_and_last_squares = array();
	$first_and_last_square['first'] = $square;
	$previous_square = $square;
	
	//to debug
	//$all_squares_found = $squares;
	
	while($square = $req->fetch()){
		//to debug
		//$all_squares_found[] = $square;
		
		//if the previous id is different than the current one
		//means it s a square where a part of bus line enter
		//in the selected area
		//and the previous square is the last square of the
		//part of bus line inside the area
		if($square['id'] != $previous_square['id'] + 1)
		{	
			$first_and_last_square['last'] = $previous_square;
			$first_and_last_squares[] = $first_and_last_square;
			$first_and_last_square = array();
			$first_and_last_square['first'] = $square;
		}
		$previous_square = $square;
	}
	//the last one:
	$first_and_last_square['last'] = $previous_square;
	$first_and_last_squares[] = $first_and_last_square;
	
	return $first_and_last_squares;
}

