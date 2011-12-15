<?php
require_once 'access_to_db.php';
require_once 'saveToDb.php';
require_once 'tools.php';
require_once 'Vertex.php';
require_once 'Bus_line_part.php';


bcscale(0);
	
$multipicador = 10000000; //if it needs to be mayor, lat and lng in to_square and from_square must be resetting
$grid_path = bcmul($multipicador, 0.001);
$precision = - substr_count($grid_path, '0');
$path_to_save = "c:/squares2";

if(!is_dir($path_to_save)){
	if (!mkdir($path_to_save)) {
		die('error to create folders\n');
	}
}

ini_set(memory_limit, "1000M");
set_time_limit(30000);

create_grid();


function create_grid(){

	class Enter_and_out{
		public $enter;
		public $out;
	}
//divide all the town in square of $grid_path ~10m of the side 
//and found the bus lines and bus station inside each one

	//access to the database:
	global $bdd;
	global $bus_lines_list;
	

	extract_datas_from_db();
	$last_id = 0;
	//for each bus lines
	foreach ($bus_lines_list as $bus_line) {
		//to debug
		//$bus_line = $bus_lines_list[47];
		//end to debug
		
		//to debug
		/*if(  $bus_line[bus_line_id] != 5){
			continue;
		}
		*/
		$square_list = array();

		//to debug
		/*if($bus_line[bus_line_id] != 35){
			continue;
		}
		else{
			$last_id = 121590;
		}*/
		//end to debug
		
		
		if($bus_line[type] != 'mainLine'){
		//to debug
		//if(($bus_line[type] == 'feeder') || ($bus_line[bus_line_id] == 164)){
			
			echo 'processing grid creation for bus line : ' . $bus_line[bus_line_id] . "<br \\> \n";
			$last_id = treatment($bus_line, $last_id);
			echo ' grid creation for bus line : ' . $bus_line[bus_line_id] . " done <br \\> \n";
			
		}
	}
	
	
	
	echo 'all done';
}
		
	function extract_datas_from_db(){
		
		global $bus_lines_list;
		global $bdd;
		
		//todebug, removing the truncates:
	////////////////////////////////////////////////////////////////////// SEGURE	
		//$bdd->query("TRUNCATE TABLE to_square");
		//$bdd->query("TRUNCATE TABLE from_square");
		
		
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
			 	name AS bus_line_name,
			 	path AS path_string,
			 	type,
			 	flows,
			 	areaOnlyBusStations
			 	
			FROM 
				bus_lines
		");
		
		//to debug above:
		// WHERE bus_line_id = 35
		
		
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
				if($previous_bus_line_id != NULL)
				{
					//if the path of the previous bus line is a loop:
					/*if(($path[0][0] == $path[$path_length-1][0])
					&& ($path[0][1] == $path[$path_length-1][1]))
					{
						$links_list_length = count($links_list);
						$links_list[$links_list_length-1][next_link_distance] = 
							$bus_line_length
							- $links_list[$links_list_length-1][distance_from_first_vertex]
							+ $links_list[0][distance_from_first_vertex];
							
						$links_list[0][previous_link_distance] = 
							$links_list[$links_list_length-1][next_link_distance];
					}*/
					
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
		
		$links_by_bus_lines_db = null;
		$bus_lines_list_db = null;
		
		unset($bus_line_id, $bus_line_length, $distance_to_last_vertex);

	
		//last bus line links to save:
		//if the path of the previous bus line is a loop:
		if($bus_lines_list[$previous_bus_line_id][type] != 'mainLine'){
			/*if(($path[0][0] == $path[$path_length-1][0])
			&& ($path[0][1] == $path[$path_length-1][1])){
				$links_list_length = count($links_list);
				$links_list[$links_list_length-1][next_link_distance] = 
					$bus_line_length
					- $links_list[$links_list_length-1][distance_from_first_vertex]
					+ $links_list[0][distance_from_first_vertex];
					
				$links_list[0][previous_link_distance] = 
				$links_list[$links_list_length-1][next_link_distance];
			}*/
			//save the links list:
			$bus_lines_list[$previous_bus_line_id][links_list] = $links_list;
			$links_list = NULL;
		}
	}
	
function treatment($bus_line, $last_id){
		
	global $multipicador;
	global $grid_path;
	global $precision;
	global $bdd;
	global $path_to_save;
	
	$bus_line_part = new Bus_line_part($bus_line);
	$path = extract_path_from_string($bus_line[path_string]);
	$path_length = count($path);
	$next_index = 0;
	$previous_link = NULL;
	$next_link = NULL;
	$links_list_length = count($bus_line[links_list]);
	
	$areas_opposite = array();
	$area_opposite = new Enter_and_out;
	$area_opposite->enter = 0;
	$area_opposite->out = $path_length-1;
	$areas_opposite[] = clone $area_opposite;
	
	//if the line is a feeder:
	if($bus_line[type] == 'feeder'){
			
		//FOUND AREAS BETWEEN VERTEX OF THE LINE WHERE FOUND THE SQUARES:
			
		$bus_line[areaOnlyBusStations] = json_decode($bus_line[areaOnlyBusStations]);
		$areaOnlyBusStations_length = count($bus_line[areaOnlyBusStations]);
	
		//converte string to object in $bus_line[areaOnlyBusStations]:
		if ($areaOnlyBusStations_length > 0 ){
			//reinit $areas_opposite to remove the default value
			$areas_opposite = array();
				
			foreach ($bus_line[areaOnlyBusStations] as $key => $enter_and_out) 
			{
				$looking_for_vertex = new Vertex($enter_and_out->enter->lat, $enter_and_out->enter->lng);
				$bus_line[areaOnlyBusStations][$key]->enter = 
					found_vertex_index_from_coordinates(
					$looking_for_vertex,
					$path);
				
				$looking_for_vertex = new Vertex($enter_and_out->out->lat, $enter_and_out->out->lng);
				$bus_line[areaOnlyBusStations][$key]->out = 
					found_vertex_index_from_coordinates(
					$looking_for_vertex,
					$path);
					
				if(($key == $areaOnlyBusStations_length -1) && ( $bus_line[areaOnlyBusStations][$key]->out == 0 )){
					$bus_line[areaOnlyBusStations][$key]->out = $path_length - 1;
				}
			}
			
			//if a unique area only bus station and at the beginning of 
			//the path:
			if ( ($areaOnlyBusStations_length == 1) && ($bus_line[areaOnlyBusStations][0]->enter == 0)){
				$area_opposite->enter = $bus_line[areaOnlyBusStations][0]->out;
				$area_opposite->out = $path_length-1;
				$areas_opposite[] = clone $area_opposite;
			}
			else{
				foreach ($bus_line[areaOnlyBusStations] as $key => $area_only_bus_station) 
				{
					if($key == 0){
						if($bus_line[areaOnlyBusStations][0]->enter == 0)
						{
							$area_opposite->enter = $area_only_bus_station->out;
							continue;
						}
						else{
							$area_opposite->enter = 0;
						}
					}
					
					$area_opposite->out = $area_only_bus_station->enter;
					
					$areas_opposite[] = clone $area_opposite;
					$area_opposite->enter = $area_only_bus_station->out;
									
					if (($key == $areaOnlyBusStations_length - 1)
					&& ($bus_line[areaOnlyBusStations][$key]->out != $path_length - 1 ))
					{
						$area_opposite->out = $path_length - 1;
						$areas_opposite[] = clone $area_opposite;
					}
				}
			}
		}
	}			
				//END FOUND AREAS BETWEEN VERTEX OF THE LINE WHERE FOUND THE SQUARES:
			
			 //TODO   IMPORTANT  this part will not work with multiple flows
			 // => done to work with multiple flows, have to be tested
			 
	//set the previous and next current link:
	$flows_list = explode(" ", $bus_line[flows]);
	$vertex_is_link = false;
	
	
	$to_squares = array();
	$from_squares = array();
	//$current_next_link_index = 0;
	
	foreach ($areas_opposite as $area_opposite) {
		
	//create the paths to get the previous link and
	// the next link from the first vertex of the area
		
		//find the index in link_list of the next_link
		$next_link_index = 0;
		
		do {
			$next_link_index++;
		}while( 
		($next_link_index < $links_list_length)
		&& ($bus_line[links_list][$next_link_index][prevIndex] < $area_opposite->enter )
		);
		
		//is there a link on the index $area_opposite->enter:
		$area_opposite_is_a_link = false;
		
		if (($bus_line[links_list][$next_link_index - 1][distanceToPrevIndex] == 0) &&
		($area_opposite->enter == $bus_line[links_list][$next_link_index - 1][prevIndex]))
		{
			$area_opposite_is_a_link = true;
		}
		
		//from the previous link:
		$previous_link = $bus_line[links_list][$next_link_index - 1];
		$previous_link[vertex] = new Vertex($previous_link[lat], $previous_link[lng]);
		
		$path_to_next_link = array();
		$path_from_previous_link = array();
		
		//if $area_opposite->enter is a link:
		if ($area_opposite_is_a_link == true){
			//set the path from previous link
			$path_from_previous_link[path] = array($previous_link[vertex]);
			$path_from_previous_link[link_lat] = $previous_link[vertex]->lat;
			$path_from_previous_link[link_lng] = $previous_link[vertex]->lng;
			$path_from_previous_link[from_index] = null;
			$path_from_previous_link[to_index] = null;
			
			//to the next link wihch is the same as previous link:
			$next_link = $previous_link;
			$path_to_next_link[path] = $path_from_previous_link[path];
			$path_to_next_link[link_lat] = $next_link[vertex]->lat;
			$path_to_next_link[link_lng] = $next_link[vertex]->lng;
			$path_to_next_link[from_index] = null;
			$path_to_next_link[to_index] = null;
			
			//set $current_next_link_index:
			$current_next_link_index = $next_link_index;
		}
		else{
			//we enter in an area so the current index is = $area_opposite->enter
			//set the path from previous link
			$extract_lenght = $area_opposite->enter - $previous_link[prevIndex];
			$path_from_previous_link[path] = array_merge(
				array($previous_link[vertex]),
				array_slice($path, $previous_link[prevIndex] + 1, $extract_lenght)
			);
			$path_from_previous_link[link_lat] = $previous_link[vertex]->lat;
			$path_from_previous_link[link_lng] = $previous_link[vertex]->lng;
			$path_from_previous_link[from_index] = $previous_link[prevIndex];
			$path_from_previous_link[to_index] = $area_opposite->enter;
		
			// to next link
			$next_link = $bus_line[links_list][$next_link_index];
			$next_link[vertex] = new Vertex($next_link[lat], $next_link[lng]);
		
			//set the path of next link
			$extract_lenght = $next_link[prevIndex] - $area_opposite->enter;
			$path_to_next_link[path] = array_merge(
				array_slice($path, $area_opposite->enter+1, $extract_lenght),
				array($next_link[vertex])
			);
			$path_to_next_link[link_lat] = $next_link[vertex]->lat;
			$path_to_next_link[link_lng] = $next_link[vertex]->lng;
			$path_to_next_link[from_index] = $area_opposite->enter;
			$path_to_next_link[to_index] = $next_link[prevIndex];
			//set $current_next_link_index:
			$current_next_link_index = $next_link_index;
		}
		
		//init
		$next_vertex = $path[$area_opposite->enter];
		$current_square = found_main_square_coords_of_vertex($next_vertex);
		$bus_line_part->go_in = clone $next_vertex;
		
		$current_vertex = $path[$area_opposite->enter];
				
		//for each next vertex of the path in the opposite area:
		for ($index = $area_opposite->enter; $index < $area_opposite->out; $index++) {
			
			$next_index = $index + 1;
			
			if ($next_index >= $path_length){
				exit('index > path length');
			}
			
			$next_vertex = $path[$next_index];
			$square_of_next_vertex = found_main_square_coords_of_vertex($next_vertex);
		
			/*if($next_index == $area_opposite->enter){
			//add the first vertex as the in:
				$bus_line_part->go_in = clone $next_vertex;
				// add the vertex coordinates
				//array_push($bus_line_part->vertex_list, $next_vertex);
			}
			//if the next vertex is inside the same square than the current one:
			else
			*/if(is_vertex_equal($square_of_next_vertex, $current_square) == true){
				// add the vertex coordinates
				//array_push($bus_line_part->vertex_list, $next_vertex);
			}
			//if the vertex is in a new square:
			//handling the previous vertex and the bus line part up to 
			//the current vertex
			else{
				$loop_qte = 0;
				do{
					//calculate the out coordinates of the current square:
					$out_coords = found_out_point($current_vertex, $next_vertex, $current_square);
					$bus_line_part->go_out = $out_coords[intersection];
					
					$lat = strval(abs(bcdiv($current_square->lat,$grid_path)));
					$lng = strval(abs(bcdiv($current_square->lng,$grid_path)));
					$square_to_save = array();
					$square_to_save[bus_line_id] = $bus_line_part->id;
					$square_to_save[bus_line_name] = $bus_line_part->name;
					$square_to_save[lat] = $lat;
					$square_to_save[lng] = $lng;
		
		////////////////////////////////////////////////////////////////////
		//to save square and path from previous link
		
					//handling of previous and next links:
					$bus_line_part->previous_link = $previous_link;
					$bus_line_part->path_to_previous_link = $path_from_previous_link[path];

					$square_to_save_path = $path_from_previous_link[path];
					
					//add 'go in' to the end of the path:
					//path from the square to the previous link
					if ($path_from_previous_link[path] != $path_to_next_link[path]){
						$square_to_save_path[] = clone $bus_line_part->go_in;
					}
						
					//convert lat and lng of each vertex to real value:
					foreach ($square_to_save_path as $key => $vertex) {
						$lat_vertex = bcdiv($square_to_save_path[$key]->lat, $multipicador, 8);
						$lng_vertex = bcdiv($square_to_save_path[$key]->lng, $multipicador, 8);
						$square_to_save_path[$key] = array();
						$square_to_save_path[$key][lat] = $lat_vertex;
						$square_to_save_path[$key][lng] = $lng_vertex;
					}
					//calculate the length of the path:
					$square_to_save[length] = real_path_length($square_to_save_path);
					
					//rq: the path is at the opposite way for 'from_square' datas
					$square_to_save_path_reverse = array_reverse($square_to_save_path);
					
					//TODO: modify to work with changing flows: DONE but must be verified
					
					if(($flows_list[0] == 'normal') || 
					(($flows_list[0] == 'both') && ($path_from_previous_link[path] != $path_to_next_link[path]))){
					//save square with path from previous link to the square
						
						//create data to save in "to_square" db
						$square_to_save[go_in_point_lat] = $bus_line_part->go_in->lat;
						$square_to_save[go_in_point_lng] = $bus_line_part->go_in->lng;
						$square_to_save[id_of_bus_station_linked] = $previous_link[busStationId];
						$square_to_save[path] = json_encode($square_to_save_path);
						
						$square_to_save[from_index] = $path_from_previous_link[from_index];
						$square_to_save[to_index] = $path_from_previous_link[to_index];
						$square_to_save[from_link_lat] = $path_from_previous_link[link_lat];
						$square_to_save[from_link_lng] = $path_from_previous_link[link_lng];
						
						$to_squares[] = $square_to_save;
						
						unset($square_to_save[from_link_lat]);
						unset($square_to_save[from_link_lng]);
						unset($square_to_save[go_in_point_lat]);
						unset($square_to_save[go_in_point_lng]);
					}
					
					if(($flows_list[0] == 'reverse') ||
					(($flows_list[0] == 'both') && ($path_from_previous_link[path] != $path_to_next_link[path]))){
					//save square with path from square to previous link (the flow is reversed)
							
						//create data to save in "from_square" db
						$square_to_save[go_out_point_lat] = $bus_line_part->go_in->lat;
						$square_to_save[go_out_point_lng] = $bus_line_part->go_in->lng;
						$square_to_save[id_of_bus_station_linked] = $previous_link[busStationId];
						$square_to_save[path] = json_encode($square_to_save_path_reverse);
						
						$square_to_save[from_index] = $path_from_previous_link[to_index];
						$square_to_save[to_index] = $path_from_previous_link[from_index];
						$square_to_save[to_link_lat] = $path_from_previous_link[link_lat];
						$square_to_save[to_link_lng] = $path_from_previous_link[link_lng];
						
						$from_squares[] = $square_to_save;	
						
						unset($square_to_save[to_link_lat]);
						unset($square_to_save[to_link_lng]);
						unset($square_to_save[go_out_point_lat]);
						unset($square_to_save[go_out_point_lng]);
					}

		////////////////////////////////////////////////////////////////////
		//to save square and path to next link
		
					$bus_line_part->next_link = $next_link;
					//$extract_lenght = $next_link[prevIndex] - $next_index + 1;
					$bus_line_part->path_to_next_link = $path_to_next_link[path];
					
					$square_to_save_path = $path_to_next_link[path];
				
					//add 'go out' to the begin of the path:
					if ($path_from_previous_link[path] != $path_to_next_link[path]){
						array_unshift(
							$square_to_save_path, clone $bus_line_part->go_out);
					}
					
						//rq: the path is at the oposite way for 'to_square' datas
				
					//convert lat and lng of each vertex to real value:
					foreach ($square_to_save_path as $key => $vertex) {
						$lat_vertex = bcdiv($square_to_save_path[$key]->lat, $multipicador, 8);
						$lng_vertex = bcdiv($square_to_save_path[$key]->lng, $multipicador, 8);
						$square_to_save_path[$key] = array();
						$square_to_save_path[$key][lat] = $lat_vertex;
						$square_to_save_path[$key][lng] = $lng_vertex;
					}
					
					//calculate the length of the path:
					$square_to_save[length] = real_path_length($square_to_save_path);
					
					//rq: the path is at the opposite way for 'from_square' datas
					$square_to_save_path_reverse = array_reverse($square_to_save_path);
						
					//TODO: modify to work with changing flows:
					if(($flows_list[0] == 'normal') ||
					(($flows_list[0] == 'both') && ($path_from_previous_link[path] != $path_to_next_link[path]))){
					//save square with path from square to next link
					
						//create data to save in "from_square" db
						$square_to_save[go_out_point_lat] = $bus_line_part->go_out->lat;
						$square_to_save[go_out_point_lng] = $bus_line_part->go_out->lng;
						$square_to_save[id_of_bus_station_linked] = $next_link[busStationId];
						$square_to_save[path] = json_encode($square_to_save_path);
						
						$square_to_save[from_index] = $path_to_next_link[from_index];
						$square_to_save[to_index] = $path_to_next_link[to_index];
						$square_to_save[to_link_lat] = $path_to_next_link[link_lat];
						$square_to_save[to_link_lng] = $path_to_next_link[link_lng];
						
						$from_squares[] = $square_to_save;	

						unset($square_to_save[to_link_lat]);
						unset($square_to_save[to_link_lng]);
						unset($square_to_save[go_out_point_lat]);
						unset($square_to_save[go_out_point_lng]);
					}
					
					if(($flows_list[0] == 'reverse') || 
					(($flows_list[0] == 'both') && ($path_from_previous_link[path] != $path_to_next_link[path]))){
					//save square with path from next link to square (the flow is reversed)
					
						//create data to save in "to_square" db
						$square_to_save[go_in_point_lat] = $bus_line_part->go_out->lat;
						$square_to_save[go_in_point_lng] = $bus_line_part->go_out->lng;
						$square_to_save[id_of_bus_station_linked] = $next_link[busStationId];
						$square_to_save[path] = json_encode($square_to_save_path_reverse);
						
						$square_to_save[from_index] = $path_to_next_link[to_index];
						$square_to_save[to_index] = $path_to_next_link[from_index];
						$square_to_save[from_link_lat] = $path_to_next_link[link_lat];
						$square_to_save[from_link_lng] = $path_to_next_link[link_lng];
						
						$to_squares[] = $square_to_save;
					}

	//////////////////////////////////////////////////////////////////////////////				
	///////////////  PREPARATION OF NEXT LOOP ///////////////////////////////////
					
					
		//////////////////////////////////////////////////////////////////
		// reinit $bus_line_part
					$bus_line_part = new Bus_line_part($bus_line);
					$bus_line_part->go_in = $out_coords[intersection];
					$bus_line_part->go_out = null;
					//$bus_line_part->vertex_list = array();
				
					$loop_qte++;
					if($loop_qte > 1000){
						echo "\n over than 1000 loops";
						echo "\nindex: ".$next_index;
						return "over than 1000 loops";
					}
				
					//coordinates of next square:
					$next_square = $out_coords[next_square];
				
					//EVOLUTION OF PREVIOUS AND NEXT LINKS

					//is link in next square:
					
					if($previous_link == $next_link){
						
						//next link must be the first link once going out from the current square
						do {
							//todebug
							$previous_next_link = $next_link;
							
							//end to debug
							$next_link = $bus_line[links_list][$current_next_link_index];
							$current_next_link_index++;
							if($next_link == null){
								echo "ERROR:\n\tdoes not find the next link\n";
							}
							$next_link[vertex] = new Vertex($next_link[lat], $next_link[lng]);
						} while(is_link_in_square($next_link, $current_square) == true);
						
						$extract_lenght = $next_link[prevIndex] - $next_index + 1;
			
						$path_to_next_link[path] = array_merge(
						///////////////////possibliy $next index +1 instead of $next_index:
							array_slice($path, $next_index, $extract_lenght),
							array($next_link[vertex])
						);
						$path_to_next_link[link_lat] = $next_link[vertex]->lat;
						$path_to_next_link[link_lng] = $next_link[vertex]->lng;
						$path_to_next_link[from_index] = $next_index;
						$path_to_next_link[to_index] = $next_link[prevIndex];
						
						//previous link must be the last link in the next square before to go out
						//which is the link before $next_link
						$previous_link = $bus_line[links_list][$current_next_link_index - 2];
						$previous_link[vertex] = new Vertex($previous_link[lat], $previous_link[lng]);
						$path_from_previous_link[path] = array($previous_link[vertex]);
						$path_from_previous_link[link_lat] = $previous_link[vertex]->lat;
						$path_from_previous_link[link_lng] = $previous_link[vertex]->lng;
						$path_from_previous_link[from_index] = null;
						$path_from_previous_link[to_index] = null;
						
						if(is_link_in_square($next_link, $next_square) == true){
							$previous_link = $next_link;
						}
						
					}
					//else if the next link is in the next square
					//and 
					elseif(is_link_in_square($next_link, $next_square) == true)	{
						
						//if exists vertex between the vertex design by the index 
						//and the link
						if($next_link[prevIndex] > $index){
							
							//get the right value for $last_index_in_square
							$last_index_in_square = $next_index;
							
							//todo : use is_vertex_in_square instead of found_main_square_coords_of_vertex
							do {
								$last_index_in_square++;
							} while (found_main_square_coords_of_vertex($last_index_in_square) == $next_square);
							
							$last_index_in_square--;
							
							//if all the vertex to go to the link are inside the next square
							if ($last_index_in_square >= $next_link[prevIndex]){
								//modify the previous link and the next link:
								
								//$current_next_link_index++;
								
								$previous_link = $next_link;
								
								//set the path from previous link
								$path_from_previous_link[path] = array($previous_link[vertex]);
								$path_from_previous_link[link_lat] = $previous_link[vertex]->lat;
								$path_from_previous_link[link_lng] = $previous_link[vertex]->lng;
								$path_from_previous_link[from_index] = null;
								$path_from_previous_link[to_index] = null;
								
								//to the next link:
								$path_to_next_link[path] = $path_from_previous_link[path];
								$path_to_next_link[link_lat] = $next_link[vertex]->lat;
								$path_to_next_link[link_lng] = $next_link[vertex]->lng;
								$path_to_next_link[from_index] = null;
								$path_to_next_link[to_index] = null;
								
								//reinit of index
								//to get the good index after the $index++ of the beginning of the loop
								$index = $last_index_in_square - 1;
								//$next_index = $index + 1;
								
								//$next_vertex = $path[$next_index];
								//$square_of_next_vertex = found_main_square_coords_of_vertex($next_vertex);
								
								break;
							}
							//else do nothing
						
						}
						else{
							
							//$current_next_link_index++;
							
							$previous_link = $next_link;
							
							//set the path from previous link
							$path_from_previous_link[path] = array($previous_link[vertex]);
							$path_from_previous_link[link_lat] = $previous_link[vertex]->lat;
							$path_from_previous_link[link_lng] = $previous_link[vertex]->lng;
							$path_from_previous_link[from_index] = null;
							$path_from_previous_link[to_index] = null;
							
							//to the next link:
							$path_to_next_link[path] = $path_from_previous_link[path];
							$path_to_next_link[link_lat] = $next_link[vertex]->lat;
							$path_to_next_link[link_lng] = $next_link[vertex]->lng;
							$path_to_next_link[from_index] = null;
							$path_to_next_link[to_index] = null;
						}
					}
					
					$current_square = $next_square;
					//END EVOLUTION OF PREVIOUS AND NEXT LINKS
							
				} while(($next_square->lat != $square_of_next_vertex->lat) 
				|| ($next_square->lng != $square_of_next_vertex->lng));
					
					//TODO HANDLING THE END VERY IMPORTANT: NOT SURE
					//$square_list[$lat][$lng][] =  //clone $bus_line_part;
				if($next_index == $path_length - 1){
					
					//TODO make a verification of concordance between last index coord and 
					//last link
					$last_link = $bus_line[links_list][count($bus_line[links_list]) - 1];
					$last_link[vertex] = new Vertex($last_link[lat], $last_link[lng]);
					$lat = strval(abs(bcdiv($current_square->lat,$grid_path)));
					$lng = strval(abs(bcdiv($current_square->lng,$grid_path)));
					
					$square_to_save = array();
					$square_to_save[bus_line_id] = $bus_line_part->id;
					$square_to_save[bus_line_name] = $bus_line_part->name;
					$square_to_save[lat] = $lat;
					$square_to_save[lng] = $lng;
					$square_to_save[length] = 0;
					$square_to_save[id_of_bus_station_linked] = $last_link[busStationId];
					$square_to_save[path][0][lat] = $last_link[lat]; //verify
					$square_to_save[path][0][lng] = $last_link[lng]; //verify
					
					$square_to_save[path] = json_encode($square_to_save[path]);
					//from_index not used
					//to_index not used
					
					//go_in_point_lat not used
					//go_in_point_ln not used
					$square_to_save[from_link_lat] = $last_link[vertex]->lat;
					$square_to_save[from_link_lng] = $last_link[vertex]->lng;
					$to_squares[to_link_lng] = $square_to_save;
					
					$square_to_save[to_link_lat] = $square_to_save[from_link_lat];
					$square_to_save[to_link_lng] = $square_to_save[from_link_lng];
					unset ($square_to_save[from_link_lat]);
					unset ($square_to_save[from_link_lng]);
					//go_out_point_lat not used
					//go_out_point_ln not used
					
					$from_squares[] = $square_to_save;
					
				}
			}
			//TODO verify if it is at the right place to do that:
			//if the current index = the next link = the previous link
			if($next_link != $previous_link){
				//if next link == index == previous link
				array_splice($path_to_next_link[path], 0, 1);
				$path_from_previous_link[path][] = $next_vertex;
			}
			/*else{
				echo "is it working? \n";
			}*/
			$current_vertex = $next_vertex;
			$current_square = $square_of_next_vertex;
		}
	}
	if(count($to_squares) != count($from_squares)){
		exit("to square and from square do not have the same size");
	}
	
	//verify_squares($to_square, $from_squares);
	
	//to bebug removing saving:
/*	if ($current_next_link_index != count($bus_line[links_list]) - 1){
		echo("\nERROR ERROR \n all the links not used\n\n");
	}*/
	

//save paths in file:
	foreach( $to_squares as &$to_square){
		$last_id++;
		$to_square[id] = $last_id;
		//create directories:
		//create folder if do not exists:
		$lat_directory = "$path_to_save/$to_square[lat]";
		if(!is_dir($lat_directory)){
			if (!mkdir($lat_directory)) {
	   			die('error to create folders\n');
			}
		}
		$lng_directory = "$lat_directory/$to_square[lng]";
		if(!is_dir($lng_directory)){
			if (!mkdir($lng_directory)) {
	   			die('error to create folders\n');
			}
		}
		
		//create file and save path in file
		$file_to_save = "$lng_directory/$to_square[id]";
		$fh = fopen($file_to_save, 'w') or die("can't open file\n");
		fwrite($fh, $to_square[path]);
		fclose($fh);
		
		unset($to_square[path]);
	}
	
	foreach( $from_squares as &$from_square){
		$last_id++;
		$from_square[id] = $last_id;
		//create directories:
		//create folder if do not exists:
		$lat_directory = "$path_to_save/$from_square[lat]";
		if(!is_dir($lat_directory)){
			if (!mkdir($lat_directory)) {
	   			die('error to create folders\n');
			}
		}
		$lng_directory = "$lat_directory/$from_square[lng]";
		if(!is_dir($lng_directory)){
			if (!mkdir($lng_directory)) {
	   			die('error to create folders\n');
			}
		}
		
		//create file and save path in file
		$file_to_save = "$lng_directory/$from_square[id]";
		$fh = fopen($file_to_save, 'w') or die("can't open file\n");
		fwrite($fh, $from_square[path]);
		fclose($fh);
		
		unset($from_square[path]);
	}
	
	
	saveToDb($to_squares, 'to_square');
	saveToDb($from_squares, 'from_square');
	

	
	
	
	/*unset($to_square, $from_square);
	$to_square = array();
	$from_square = array();*/
	
	echo("to_squares and from_squares created \n");
	return $last_id;
}

function is_link_in_square($link, $square){
	global $grid_path;
	if((( $square->lat - $grid_path ) < $link[vertex]->lat) 
	&& ($link[vertex]->lat <= $square->lat)
	&& (( $square->lng - $grid_path ) < $link[vertex]->lng) 
	&& ($link[vertex]->lng <= $square->lng))
	{
		return true;
	}
	else{
		return false;
	}
}

function is_vertex_in_square($vertex, $square){
	global $grid_path;
	if((( $square->lat - $grid_path ) < $vertex->lat) 
	&& ($vertex->lat <= $square->lat)
	&& (( $square->lng - $grid_path ) < $vertex->lng) 
	&& ($vertex->lng <= $square->lng))
	{
		return true;
	}
	else{
		return false;
	}
}

function verify_squares($to_square_list, $from_square_list){
	$compt = 0;
	foreach ($to_square_list as $key => $to_square) {
		$from_square = $from_square_list[$key];
		
		$from_square_path = json_decode($from_square[path]);
		$length_of_from_square_path = count($from_square_path);
		$to_square_path = json_decode($to_square[path]);
		
		$previous_link = $to_square_path[0];
		$next_link = $from_square_path[$length_of_from_square_path - 1];
		
		if(($previous_previous_link != null)
		&& (!Vertex::are_egal($previous_link, $next_link))){
			
			if(!Vertex::are_egal($previous_previous_link, $previous_link)){
				echo "error on previous_link \n";
				$error = true;
			}
			if(!Vertex::are_egal($previous_next_link, $next_link)){
				echo "error on next link \n";
				$error = true;
			}
			if($error == true){
				exit();
			}
			
			$compt = 0;
			
		}
		else{
			$compt++;
			echo "compt = $compt \n";
		}
		
		if(!Vertex::are_egal($previous_link, $next_link)){
			$previous_previous_link = $previous_link;
			$previous_next_link = $next_link;
		}
		else{
			($previous_previous_link != null);
		}
	}
	
}


	/*$bus_stations_list = $bdd->query("
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
			$bus_station_coords = new Vertex($bus_station->lat . " " . $bus_station->lng, NULL);
			$bus_station_square =  found_main_square_coords_of_vertex($bus_station_coords);
			
			$lat = strval(abs(bcdiv($bus_station_square->lat,$grid_path)));
			$lng = strval(abs(bcdiv($bus_station_square->lng,$grid_path)));
					
			$square_list[$lat][$lng][bus_stations][]= $bus_station;
		}*/
		
	//store the results:
	//id, lat, lng, type: busline o bus station, element_id, name, next_link, path_to_next_link, previous_link, path_to_previous_link, go in, go out, vertex_list

	/*
	$bdd->query("TRUNCATE TABLE to_square");
	saveToDb($to_square, 'to_square');

	$bdd->query("TRUNCATE TABLE from_square");
	saveToDb($from_square, 'from_square');
	*/


?>





