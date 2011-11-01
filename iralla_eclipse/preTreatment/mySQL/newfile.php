<?php

$multipicador = 100000000;
$grid_path = bcmul($multipicador, 0.0001);
$precision = - substr_count($grid_path, '0');

$count_test = 0;


class Vertex{
	public $lat;
	public $lng;
	
	public function __construct($lat_lng_string){
		global $multipicador;
		$buffer = explode(" ", $lat_lng_string);
		$this->lat = bcmul($buffer[0], $multipicador);
		$this->lng = bcmul($buffer[1], $multipicador);
	}
}

class Bus_line_part{
	public $id;
	public $name;
	public $vertex_list = array();
	public $go_in;
	public $go_out;
	public $next_link = NULL;
	public $path_to_next_link = NULL;
	public $previous_link = NULL;
	public $path_to_previous_link = NULL;
	
	
	public function __construct($bus_line){
		$this->id = $bus_line[bus_line_id];
		$this->name = $bus_line[bus_line_name];
		$this->go_in = new Vertex("0 0");
		$this->go_out = new Vertex("0 0");
	}
}

//divide all the town in square of $grid_path ~10m of the side 
//and found the bus lines and bus station inside each one

	//access to the database:
	$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	$bdd = new PDO('mysql:host=localhost;dbname=Guayaquil', 'root', '', $pdo_options);	

	//extract the links by bus lines and in order:
	$links_by_bus_lines_db = $bdd->query("
		 SELECT 
			*
		FROM 
			links
		ORDER BY
		busLineId, prevIndex, distanceToPrevIndex
	");
	
	//extract the bus lines of the database
	$bus_lines_list_db = $bdd->query("
		 SELECT 
			bus_lines.id AS bus_line_id,
		 	name,
		 	path AS path_string,
		 	type,
		 	flows
		 	
		FROM 
			bus_lines
	");
	
	while($bus_line = $bus_lines_list_db->fetch()){
		$bus_lines_list[$bus_line[bus_line_id]] = $bus_line;
	}
	
	//make list of connection and distance between them
	//for each bus line
	$previous_bus_line_id = NULL;

	while($one_link = $links_by_bus_lines_db->fetch()){
		
		$bus_line_id = $one_link[busLineId];
			
		if(($bus_line_id != $previous_bus_line_id) 
		&& ($bus_lines_list[$previous_bus_line_id][type] != 'mainLine')){
			if($previous_bus_line_id != NULL){
				//if the path of the previous bus line is a loop:
				if(($path[0][0] == $path[$path_length-1][0])
				&& ($path[0][1] == $path[$path_length-1][1])){
					$links_list_length = count($links_list);
					$links_list[$links_list_length-1][next_link_distance] = 
						$bus_line_length
						- $links_list[$links_list_length-1][distance_from_first_vertex]
						+ $links_list[0][distance_from_first_vertex];
						
					$links_list[0][previous_link_distance] = 
						$links_list[$links_list_length-1][next_link_distance];
				}
				//save the links list:
				$bus_lines_list[$previous_bus_line_id][links_list] = $links_list;
				$links_list = NULL;
			}
			$path = extarct_path($bus_lines_list[$bus_line_id][path_string]);
			$path_length = count($path);
			$one_link[distance_from_first_vertex] =
				distanceFromFirstVertex($path, $one_link[prevIndex])
				+ $one_link[distanceToPrevIndex];
			$bus_line_length = distanceFromFirstVertex($path, $path_length-1);
			$distance_to_last_vertex = $bus_line_length - $distance_from_first_vertex;
				
		}
		else if (($bus_line_id == $previous_bus_line_id) 
		&& ($bus_lines_list[$bus_line_id][type] != 'mainLine')){
			$one_link[distance_from_first_vertex] = 
				distanceFromFirstVertex($path, $one_link[prevIndex])
				+ $one_link[distanceToPrevIndex];
			
			$links_list_length = count($links_list);
			$one_link[previous_link_distance] = 
				$one_link[distance_from_first_vertex]
				- $links_list[$links_list_length-1][distance_from_first_vertex];
			
			$links_list[$links_list_length-1][next_link_distance] = $one_link[previous_link_distance];
		}
		if($bus_lines_list[$bus_line_id][type] != 'mainLine'){
			$links_list[] = $one_link;
		}
		$previous_bus_line_id = $bus_line_id;
	
	}
	//last bus line links to save:
	//if the path of the previous bus line is a loop:
	if($bus_lines_list[$previous_bus_line_id][type] != 'mainLine'){
		if(($path[0][0] == $path[$path_length-1][0])
		&& ($path[0][1] == $path[$path_length-1][1])){
			$links_list_length = count($links_list);
			$links_list[$links_list_length-1][next_link_distance] = 
				$bus_line_length
				- $links_list[$links_list_length-1][distance_from_first_vertex]
				+ $links_list[0][distance_from_first_vertex];
				
			$links_list[0][previous_link_distance] = 
			$links_list[$links_list_length-1][next_link_distance];
		}
		//save the links list:
		$bus_lines_list[$previous_bus_line_id][links_list] = $links_list;
		$links_list = NULL;
	}
	
	
	$square_list = array();
	$bus_line = array();
	
	//for each bus lines
	//while($bus_line = $bus_lines_list_db->fetch()){
	foreach ($bus_lines_list as $bus_line) {
		
		if($bus_line[type] != 'mainLine'){
				
			$bus_line_part = new Bus_line_part($bus_line);
			$path = extract_path_from_string($bus_line[path_string]);
			$path_length = count($path);
			$index = 0;
			$previous_link = NULL;
			$next_link = NULL;
			$links_list_length = count($bus_line[links_list]);
			
			 //TODO   IMPORTANT  this part will not work with multiple flows
			 
			//set the previous and next current link:
			$flows_list = explode(" ", $bus_line[flows]);
			
			//if the first vertex is a link:
			if (($path[0]->lat == $bus_line[path][0]->lat)
			&& ($path[0]->lng == $bus_line[path][0]->lng)){
				if (($flows_list[0] == 'reverse') || ($flows_list[0] == 'both')){
					$previous_link = $bus_line[links_list][0];
				}
				//if the bus line flow is normal or both:
				if(($flows_list[0] == 'normal') || ($flows_list[0] == 'both')){
					$next_link = $bus_line[links_list][0];
				}
			}
			else{
				//if the bus line is a loop and flow is reverse or both
				if(( $path[0]->lat == $path[$path_length-1]->lat)
				&& ( $path[0]->lng == $path[$path_length-1]->lng)
				&&(($flows_list[0] == 'reverse') || ($flows_list[0] == 'both'))){
					$previous_link = $bus_line[links_list][count($bus_line[links_list])-1];
				}
				//if the bus line flow is normal or both:
				if(($flows_list[0] == 'normal') || ($flows_list[0] == 'both')){
					$next_link = $bus_line[links_list][0];
				}
			}
			
			//for each next vertex found if it's inside the same square
			foreach ($path as $index => $current_vertex) {
				$current_square = found_main_square_coords_of_vertex($current_vertex);
				
				//DEBUG
				//echo "\n index: " .$index;
				
				//for the first vertex
				if($index == 0){
					//add the first vertex as the in:
					$bus_line_part->go_in = clone $current_vertex;
					// add the vertex coordinates
					array_push($bus_line_part->vertex_list, $current_vertex);
				}
				//if the vertex is inside the same square than the previous one:
				else if(is_vertex_equal($current_square, $previous_square) == true){
					// add the vertex coordinates
					array_push($bus_line_part->vertex_list, $current_vertex);
				}
				//if the vertex is in a new square:
				else{
					$loop_qte = 0;
					do{
						$count_test++;
						//calculate the out coordinates from the previous square:
						$out_coords = found_out_point($previous_vertex, $current_vertex, $previous_square);
						$bus_line_part->go_out = $out_coords[intersection];
						
						$lat = strval(abs(bcdiv($previous_square->lat,$grid_path)));
						$lng = strval(abs(bcdiv($previous_square->lng,$grid_path)));
						
						//handling of previous and next links:
						if($previous_link != NULL){
							$bus_line_part->previous_link = $previous_link;
							$extract_lenght = $index - $previous_link[prevIndex] + 1;
							
							//if previous vertex the last of the links list:
							if($extract_lenght < 0){
								$bus_line_part->path_to_previous_link = array_merge(
									array_slice($path, $bus_line[links_list][$links_list_length + 1][prevIndex]),
									array_slice($path, 0, $index)
								);
							}
							//else
							else{
								$bus_line_part->path_to_previous_link =
									array_slice($path, $previous_link[prevIndex], $extract_lenght);
							}
						}
						
						if($next_link != NULL){
							$bus_line_part->next_link = $next_link;
							$extract_lenght = $next_link[prevIndex] - $index + 1;
							
							//if next link pass the end vertex:
							if( $extract_lenght < 0 ){
								$bus_line_part->path_to_next_link = array_merge(
									array_slice($path, $index),
									array_slice($path, 0, $bus_line[links_list][0][prevIndex]+1)
								);
							}
							//else
							else{
								$bus_line_part->path_to_next_link = 
									array_slice($path, $index, $extract_lenght);
							}
						}
						
						//save the square:
						$square_list[$lat][$lng][]= clone $bus_line_part;
	
						//reinit $bus_line_part:
						//DEBUG
						$previous_bus_line_part = clone $bus_line_part;
						//END DEBUG
						$bus_line_part = new Bus_line_part($bus_line);
						$bus_line_part->go_in = $out_coords[intersection];
						$bus_line_part->go_out = null;
						$bus_line_part->vertex_list = array();
					
						$loop_qte++;
						if($loop_qte > 1000){
							echo "\n over than 1000 loops";
							echo "\nindex: ".$index;
							return "over than 1000 loops";
						}
						
						//coordinates of next square:
						$next_square = $out_coords[next_square];
						$previous_square = $next_square;
						
						//evolution of previous and next links:
			////////////////////////////////////////////////////////////
			
					//if the current vertex is a link:
					if (($path[0]->lat == $bus_line[path][0]->lat)
					&& ($path[0]->lng == $bus_line[path][0]->lng)){
						if (($flows_list[0] == 'reverse') || ($flows_list[0] == 'both')){
							$previous_link = $bus_line[links_list][0];
						}
						//if the bus line flow is normal or both:
						if(($flows_list[0] == 'normal') || ($flows_list[0] == 'both')){
							$next_link = $bus_line[links_list][0];
						}
					}
					else{
						//if the bus line is a loop and flow is reverse or both
						if(( $path[0]->lat == $path[$path_length-1]->lat)
						&& ( $path[0]->lng == $path[$path_length-1]->lng)
						&&(($flows_list[0] == 'reverse') || ($flows_list[0] == 'both'))){
							$previous_link = $bus_line[links_list][count($bus_line[links_list])-1];
						}
						//if the bus line flow is normal or both:
						if(($flows_list[0] == 'normal') || ($flows_list[0] == 'both')){
							$next_link = $bus_line[links_list][0];
						}
					}
						
			////////////////////////////////////////////////////////////////			
						
						
						
					} while(($next_square->lat != $current_square->lat) 
					|| ($next_square->lng != $current_square->lng));
					//add the current vertex to the vertex_list :
					/*$list_length = count($square_list[$lat][$lng]);
					
					array_push(
						$square_list[$lat][$lng][$list_length-1]->vertex_list, 
						$current_vertex);*/
					$square_list[$lat][$lng][] = $current_vertex;
					
				}
				$previous_vertex = $current_vertex;
				$previous_square= $current_square;
			}
		}	
	}
	
	
	$bus_stations_list = $bdd->query("
		 SELECT 
				bus_stations.id AS busStationId,
			 	bus_stations.name AS busStationName,
			 	bus_stations.lat AS busStationLat,
			 	bus_stations.lng AS busStationLng,
			 	bus_stations.type AS busStationType
		 	
		FROM 
			bus_stations
		
		HAVING
			busStationType = 'normal'
	");

		while($bus_station = $bus_stations_list->fetch()){
			$bus_station_coords = new Vertex($bus_station->lat . " " . $bus_station->lng);
			$bus_station_square =  found_main_square_coords_of_vertex($bus_station_coords);
			
			$lat = strval(abs(bcdiv($bus_station_square->lat,$grid_path)));
			$lng = strval(abs(bcdiv($bus_station_square->lng,$grid_path)));
					
			$square_list[$lat][$lng][]= $bus_station;
		}
		
	//store the results:
	echo 'test';
	
	function extract_path_from_string($string){
		$path = array();
		
		//extract the path of the line:
		$couple_of_coordinates = explode(",", $string);
			
		foreach ($couple_of_coordinates as $coordCouple){
			array_push($path, new Vertex($coordCouple));
		}
		return $path;
	}
	
	function found_main_square_coords_of_vertex($vertex){
		global $precision;
		global $grid_path;
		//found the square around the vertex
		$square_main_corner_coordinates = new Vertex("0 0");
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
	
	function found_out_point($vertex_in, $vertex_out, $square_coordinates){
		global $grid_path;
		$intersection = new Vertex("0 0");
		$intersection->lat = NULL;
		$intersection->lng = NULL;
		
		$square_coordinates_NE = $square_coordinates;
		$square_coordinates_SW = new Vertex("NULL NULL");
		$square_coordinates_SW->lat = $square_coordinates->lat - $grid_path;
		$square_coordinates_SW->lng = $square_coordinates->lng - $grid_path;
		$lng_diff = $vertex_out->lng - $vertex_in->lng;
		$lat_diff = $vertex_out->lat - $vertex_in->lat;
				
		//found by which side of the square pass the line from $vertex_in
		//to $vertex_out
		
		
		if($lng_diff != 0){
			$coeff_lat = bcdiv($lat_diff,$lng_diff,8); //TODO : case $lng_diff =0
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
		else{
			$intersection->lat = $vertex_in->lat;
		}
		
		if($lat_diff != 0){
			$coeff_lng = bcdiv($lng_diff,$lat_diff,8); //TODO : case $lat_diff = 0
			
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
		else{
			$intersection->lng = $vertex_in->lng;
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
			exit('error: out side not found');
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
					
				case 'sur':
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
			$distance += distanceBetweenTwoPoint($path[$key-1], $path[$key]);
		}
		return $distance;
	}
	
	function extarct_path($path_as_string){
		$path = array();

		//extract the path of the line:
		$couple_of_coordinates = array();
		$couple_of_coordinates = explode(",", $path_as_string);
			
		foreach ($couple_of_coordinates as $coordCouple){
			array_push($path, explode(" ", $coordCouple));
		}
		return $path;
	}
	
	function distanceBetweenTwoPoint($point_1, $point_2){
		return sqrt(( pow($point_1[0] - $point_2[0], 2) + pow($point_1[1] - $point_2[1], 2) ) );
	}
?>

