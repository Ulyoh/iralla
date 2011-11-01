<?php
	require_once 'Vertex.php';
	function extract_path_from_string($path_as_string){
		
		$path = array();

		$path_lat_lngs = json_decode($path_as_string);
			
		foreach ($path_lat_lngs as $lat_lng){
			$path[] = new Vertex($lat_lng->lat, $lat_lng->lng);
		}
		return $path;

	}
	
	function extract_path_from_string_and_divide($path_as_string, $denominator){
		
		$path = array();

		$path_lat_lngs = json_decode($path_as_string);
			
		foreach ($path_lat_lngs as $lat_lng){
			$path[] = new Vertex(bcdiv($lat_lng->lat, $denominator, 6), bcdiv($lat_lng->lng, $denominator, 6));
		}
		return $path;

	}
	
	function divide_all_coordinates_of_path($path, $denominator){
		foreach ($path as $vertex) {
			$vertex->lat = bcdiv($vertex->lat, $denominator, 6);
			$vertex->lng = bcdiv($vertex->lng, $denominator, 6);
		}
	}
	
	function found_main_square_coords_of_vertex($vertex){
		global $precision;
		global $grid_path;
		//found the square around the vertex
		$square_main_corner_coordinates = new Vertex("0 0", NULL);
		$square_main_corner_coordinates->lat = round($vertex->lat,$precision);
		$square_main_corner_coordinates->lng = round($vertex->lng,$precision);
		
		if($square_main_corner_coordinates->lat < $vertex->lat){
			$square_main_corner_coordinates->lat += $grid_path;
		}
		
		if($square_main_corner_coordinates->lng < $vertex->lng){
			$square_main_corner_coordinates->lng += $grid_path;
		}
		
		return $square_main_corner_coordinates;
	}
	
	function is_vertex_equal($vertex_1, $vertex_2){
		if(($vertex_1->lat == $vertex_2->lat) && ($vertex_1->lng == $vertex_2->lng)){
			return true;
		}
		else{
			return false;
		}
	}
	
	/*
	 * found the coordinate of the intersection between the segment
	 * [$vertex_in, $vertex_out] with a square of sides = $grid_path
	 * and the NE point = $sqaure_coordinates
	 * 
	 */
	function found_out_point($vertex_in, $vertex_out, $square_coordinates){
		
		global $grid_path;
		$intersection = new Vertex("0 0", NULL);
		$intersection->lat = NULL;
		$intersection->lng = NULL;
		
		$square_coordinates_NE = $square_coordinates;
		$square_coordinates_SW = new Vertex("NULL NULL", NULL);
		$square_coordinates_SW->lat = $square_coordinates->lat - $grid_path;
		$square_coordinates_SW->lng = $square_coordinates->lng - $grid_path;
		$lng_diff = $vertex_out->lng - $vertex_in->lng;
		$lat_diff = $vertex_out->lat - $vertex_in->lat;
				
		//found by which side of the square pass the line from $vertex_in
		//to $vertex_out
		
		//if the two vertex have diff lng coordinates:
		if($lng_diff != 0){
			$coeff_lat = bcdiv($lat_diff,$lng_diff,12); //TODO : case $lng_diff =0
		//if vertex out possibly on east side:			
			if( $vertex_out->lng > $square_coordinates_NE->lng){
				$intersection->lat  = 
				$vertex_in->lat + intval(
				bcmul($coeff_lat,($square_coordinates_NE->lng - $vertex_in->lng)));
			}
			//or on west side:
			elseif( $vertex_out->lng <=  $square_coordinates_SW->lng){
				$intersection->lat = 
				$vertex_in->lat + intval(
				bcmul($coeff_lat,($square_coordinates_SW->lng - $vertex_in->lng)));
			}
		}
		//if the two vertex have same lng coordinates:
		else{
			if($vertex_out->lat < $vertex_in->lat){
				$intersection->lat = $square_coordinates_SW->lat;
			}
			else if($vertex_out->lat > $vertex_in->lat){
				$intersection->lat = $square_coordinates_NE->lat;
			}
			else{
				exit('error: $vertex_in = $vertex_out');
			}
		}
		
		//if the two vertex have diff lat coordinates:
		if($lat_diff != 0){
			$coeff_lng = bcdiv($lng_diff,$lat_diff,12); //TODO : case $lat_diff = 0
			
			//if vertex out possibly on north side
			if( $vertex_out->lat > $square_coordinates_NE->lat){
				$intersection->lng = 
				$vertex_in->lng + intval(
				bcmul($coeff_lng, ($square_coordinates_NE->lat - $vertex_in->lat)));
			}
			//or south side:
			elseif( $vertex_out->lat <=  $square_coordinates_SW->lat){
				$intersection->lng = 
				$vertex_in->lng + intval(
				bcmul($coeff_lng, ($square_coordinates_SW->lat - $vertex_in->lat)));
			}
		}
		//if the two vertex have same lat coordinates:
		else{
			if($vertex_out->lng < $vertex_in->lng){
				$intersection->lng = $square_coordinates_SW->lng;
			}
			else if($vertex_out->lng > $vertex_in->lng){
				$intersection->lng = $square_coordinates_NE->lng;
			}
			else{
				exit('error: $vertex_in = $vertex_out');
			}
		}
		
		$where = array();
		$next_square = clone $square_coordinates;
		//correction of $intersection coordinates:
		//if intersection on north or south side: 
		if(( $square_coordinates_SW->lng <= $intersection->lng )
		 && ($intersection->lng <= $square_coordinates_NE->lng)){
		 	//if the intersection is on north side:
			if (($intersection->lat != NULL) && ($intersection->lat >= $square_coordinates_NE->lat)
			|| ((($intersection->lat == NULL) || ($lng_diff == 0)) 
				&& ($vertex_out->lat >= $square_coordinates_NE->lat)))
			{
				array_push($where, 'north');
				$next_square->lat += $grid_path;
			}
			//or the south side:
			elseif (($intersection->lat != NULL) && ($intersection->lat <= $square_coordinates_SW->lat)
			|| ((($intersection->lat == NULL) || ($lng_diff == 0)) 
				 && ($vertex_out->lat <= $square_coordinates_SW->lat)))
			{
				array_push($where, 'south');
				$next_square->lat -= $grid_path;
			}
		}

		//correction of $intersection coordinates:
		//if intersection on east or west side: 
		if(( $square_coordinates_SW->lat <= $intersection->lat )
		 && ($intersection->lat <= $square_coordinates_NE->lat)){
		 	
			//if lng outside of the east side:
			if (($intersection->lng != NULL) && ($intersection->lng >= $square_coordinates_NE->lng)
			|| ((($intersection->lng == NULL) || ($lat_diff == 0)) 
				 && ($vertex_out->lng >= $square_coordinates_NE->lng)))
			{
				array_push($where, 'east');
				$next_square->lng += $grid_path;
			}
			// or the west side 
			elseif (($intersection->lng != NULL) && ($intersection->lng <= $square_coordinates_SW->lng)
			|| ((($intersection->lng == NULL) || ($lng_diff == 0)) 
				 && ($vertex_out->lng <= $square_coordinates_SW->lng)))
			{
				array_push($where, 'west');
				$next_square->lng -= $grid_path;
			}
			else{
				
			}
		}
		
		$where_length = count($where);
		if($where_length <= 0){
			//check if the lat of intersection point is near of a side:
			$north_diff = $square_coordinates_NE->lat - $intersection->lat;
			$south_diff = $intersection->lat - $square_coordinates_SW->lat;
			if((0 < $north_diff) && ($north_diff <= 1 )){
				array_push($where, 'north');
				$next_square->lat += $grid_path;
			}
			//TODO: is that  part possible? :
			else if ((0 < $south_diff) && ($south_diff <= 1 )){
				array_push($where, 'south');
				$next_square->lat -= $grid_path;
			}
			//end TODO
			
			//check if the lng of intersection point is near of a side:
			$east_diff = $square_coordinates_NE->lng - $intersection->lng;
			$west_diff = $intersection->lng - $square_coordinates_SW->lng;
			if((0 < $east_diff) && ($east_diff <= 1 )){
				array_push($where, 'east');
				$next_square->lng += $grid_path;
			}
			
			//TODO: is that  part possible? :
			else if ((0 < $west_diff) && ($west_diff <= 1 )){
				array_push($where, 'west');
				$next_square->lng -= $grid_path;
			}
			//end TODO
			//exit('error: out side not found');
		}
		
		if ($where_length > 2){
			exit('error: out side more than 2');
		}
		
		//correction of one of the coordinate of the intersection point:
		
		foreach ($where as $cardinal_direction) {
			switch ($cardinal_direction){
				case 'north':
				$intersection->lat = $square_coordinates_NE->lat;
					break;
					
				case 'south':
				$intersection->lat = $square_coordinates_SW->lat;
					break;
				
				case 'east':
				$intersection->lng= $square_coordinates_NE->lng;
					break;
					
				case 'west':
				$intersection->lng = $square_coordinates_SW->lng;
					break;
				
			}
		}
		
		$where[intersection] = $intersection;
		$where[next_square] = $next_square;
		return $where;
	}
	
	function distanceFromFirstVertex($path, $vertex_index){
		$distance = 0;
		foreach ($path as $key => $coords) {
			if ($key == 0){
				continue;
			}
			if ($key > $vertex_index){
				break;
			}
			//$distance += distanceBetweenTwoPoint($path[$key-1], $path[$key]);
			if ((is_object($path[$key-1])) && (is_object($path[$key]))){
				$distance = bcadd($distance, distanceBetweenTwoVertex($path[$key-1], $path[$key]));
			}
			else{
				$distance = bcadd($distance, distanceBetweenTwoPoint($path[$key-1], $path[$key]));
			}
		}
		return $distance;
	}
	
	function extarct_path($path_as_string){
		$path = array();

		$path_lat_lngs = json_decode($path_as_string);
		
		$length = count($path_lat_lngs);
		for ($i = 0; $i < $length; $i++) {
			$lat_lng = $path_lat_lngs[0];
			array_push($path, array($lat_lng->lat, $lat_lng->lng));
		}
		/*foreach (path_lat_lngs as $lat_lng){
			
		}*/
		return $path;
	}
	
	function distanceBetweenTwoPoint($point_1, $point_2){
		return sqrt(( pow($point_1[0] - $point_2[0], 2) + pow($point_1[1] - $point_2[1], 2) ) );
	}
	
	function distanceBetweenTwoVertex($vertex_1, $vertex_2){
		return sqrt(( pow($vertex_1->lat - $vertex_2->lat, 2) + pow($vertex_1->lng - $vertex_2->lng, 2) ) );
	}
	
	function found_vertex_index_from_coordinates($vertex_to_found, $path){
		$shortest_distance = -log(0);
		foreach ($path as $index => $vertex) {
			$distance = distanceBetweenTwoVertex($vertex_to_found, $vertex);
			if($distance < $shortest_distance){
				$shortest_distance = $distance;
				$index_found = $index;
			}
		}
		if($shortest_distance > 10){
			exit('$shorteste_distance > 10');
		}
		return $index_found;
	}
	
	
	function distance_between_2_vertex_on_path($path, $vertex_index_1, $vertex_index_2){
		if($vertex_index_2 < $vertex_index_1){
			$buffer = $vertex_index_1;
			$vertex_index_1 = $vertex_index_2;
			$vertex_index_2 = $buffer;
		}
		
		$distance = 0;
		foreach ($path as $key => $coords) {
			if ($key <= $vertex_index_1){
				continue;
			}
			elseif ($key > $vertex_index_2){
				break;
			}
			$distance_to_add = distance_between_2_point_2($path[$key-1], $path[$key]);
			$distance += $distance_to_add;
		}
		return $distance;
		
	}
	
	function deg_to_rad($degres){
		return $degres/180*pi();
	}
		
	function distance_between_2_point_2($point_1, $point_2){
		if(($point_1->lat == $point_2->lat) && ($point_1->lng == $point_2->lng)){
			return 0;
		}
		return acos((sin(deg_to_rad($point_1->lat)) * sin(deg_to_rad($point_2->lat))) + (cos(deg_to_rad($point_1->lat)) * cos(deg_to_rad($point_2->lat)) * cos(deg_to_rad($point_1->lng - $point_2->lng)))) * 6378137;
		
		/*
		$arg1 = bcpow(bcsub($point_1->lat, $point_2->lat, 10) , 2, 12);

		$arg2 = bcpow(bcsub($point_1->lng, $point_2->lng, 10), 2, 12);
		
		$sum = bcadd($arg1, $arg2, 12);
		
		return bcsqrt($sum);*/
	}
	
	function path_length($path){
		$table_length = count($path);
		$length = 0;
		for ($i = 1; $i < $table_length; $i++) {
			$length += distanceBetweenTwoVertex($path[$i - 1], $path[$i]);
		}
			
		return $length;
	}
	
	function real_path_length($path){
		$table_length = count($path);
		$length = 0;
		for ($i = 1; $i < $table_length; $i++) {
			$length += real_distance_between_2_vertex($path[$i - 1], $path[$i]);
		}
			
		return $length;
	}
	
	function real_distance_between_2_vertex($vertex_1,$vertex_2){
		if (is_object($vertex_1)){
			$buffer = array();
			$buffer[lat] = $vertex_1->lat;
			$buffer[lng] = $vertex_1->lng;
			$vertex_1 = $buffer;
		}
		
		if (is_object($vertex_2)){
			$buffer = array();
			$buffer[lat] = $vertex_2->lat;
			$buffer[lng] = $vertex_2->lng;
			$vertex_2 = $buffer;
		}
		
		//if $vertex1 == $vertex_2:
		if($vertex_1 == $vertex_2){
			return 0;
		}
		
		// R [cos^-1(sin(a)sin(b)+cos(a)cos(c-d)]
		$earth_radius = 6378000;
		$vertex_1_rad = new stdClass;
		$lat1 = abs(deg_to_rad($vertex_1[lat]));
		$lon1 = abs(deg_to_rad($vertex_1[lng]));
		
		$vertex_2_rad = new stdClass;
		$lat2 = abs(deg_to_rad($vertex_2[lat]));
		$lon2= abs(deg_to_rad($vertex_2[lng]));
		$part1 = bcmul(sin($lat1),sin($lat2), 14);
		$part2 = bcmul(bcmul(cos($lat1),cos($lat2), 14), cos($lon2-$lon1), 14);
		$pre_calcul = bcadd($part1, $part2, 14);
		//$pre_calcul = sin($lat1)*sin($lat2)+cos($lat1)*cos($lat2)*cos($lon2-$lon1);
		$result = acos($pre_calcul)*$earth_radius;
		
		return $result;
	}
	?>