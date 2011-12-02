<?php
require_once 'access_to_db.php';
require_once 'tools.php';

$multipicador = 10000000;
$denominator_to_get_real_values = 10000000;
$grid_path = 0.001;
$grid_path_mult = bcmul($multipicador, $grid_path);
$foot_speed = 0.7; //0.7 m/s ~2.5km/h
$bus_speed = 13; //13 m/s ~30km/h
$path_of_roads = "d:/roads/";

$max_time_lost_whitout_changing_bus_line = 600;

class My_square {
	public $bus_line_id;
	public $name;
	public $path;
	public $time;
	
	public function __construct($bus_line_id,$name, $path, $time){
		$this->bus_line_id = $bus_line_id;
		$this->name = $name;
		$this->path = $path;
		$this->time = $time;
	}
}

class Shortest_road{
	public $bs2bss;
	public $total_time;
	public $first_bus_line_part;
	public $end_bus_line_part;
	public $from_square;
	public $to_square;
	public $merged_last_bus_line_part_with_to_square;
	public $merged_first_bus_line_part_with_from_square;
	
	public function __construct(){
		$this->total_time = INF;
	}
}

$request = $_POST['q'];
//$request ='{"start":{"lat":-2.0907472653611823,"lng":-79.94669189453127},"end":{"lat":-2.1210250353406597,"lng":-79.95574703216555}}';
//$request = '{"start":{"lat":-2.076423017151715,"lng":-79.91639366149904},"end":{"lat":-2.0957221234194163,"lng":-79.91124382019045}}';
//$request = '{"start":{"lat":-2.106701064525724,"lng":-79.97518768310545},"end":{"lat":-2.125914025005733,"lng":-79.85502471923826}}';
$request = json_decode($request);
//TO DEBUG
/*
$request = array();
$request[start] = array();
$request[start][lat] = -2.1949;
$request[start][lng] = -79.89035;

$request[end] = array();
$request[end][lat] = -2.2325;
$request[end][lng] = -79.890;*/
//END TO DEBUG

$start[lat] = $request->start->lat;
$start[lng] = $request->start->lng;
$end[lat] = $request->end->lat;
$end[lng] = $request->end->lng;

//find nearst bus stations :
$interval = 0.005;

$start_nearest_bus_stations = nearest_bus_stations($start, $interval, "bus_stations");
$end_nearest_bus_stations = nearest_bus_stations($end, $interval, "bus_stations");
//end find nearest bus stations

//change start and end calcul to fit with to square and end square coordinates
$start[lat] =abs( bcmul($request->start->lat, $grid_path_mult));
$start[lng] =abs( bcmul($request->start->lng, $grid_path_mult));
$end[lat] =abs( bcmul($request->end->lat, $grid_path_mult));
$end[lng] =abs( bcmul($request->end->lng, $grid_path_mult));

//from square and to square
$interval = 50;
$ecart_min_between_d_min_and_d_max = 6;
$max_group_size = 15;

$start_squares = nearest_points($start, $interval, "from_square", $ecart_min_between_d_min_and_d_max, $max_group_size);
$end_squares = nearest_points($end, $interval, "to_square", $ecart_min_between_d_min_and_d_max, $max_group_size);

//create false start square of length = 0
add_bus_stations_to_end_start_squares(&$start_squares, $start_nearest_bus_stations);
add_bus_stations_to_end_start_squares(&$end_squares, $end_nearest_bus_stations);	

//end of creation of false  start and end square

$first = true;
foreach ($start_squares as $key => $start_square){
	if($first){
		$start_bus_stations_string = "( start_bus_station_id = ?";
		$first = false;
	}
	else{
		$start_bus_stations_string .= " OR start_bus_station_id = ?" ;
	}
	$values_for_mysql[] = $key;
	$bus_stations_from_square[] = $key;
}
$start_bus_stations_string .= ")";

$first = true;
foreach ($end_squares as  $key =>  $end_square){
	if($first){
		$end_bus_stations_string = "( end_bus_station_id = ?";
		$first = false;
	}
	else {
		$end_bus_stations_string .= " OR end_bus_station_id = ?" ;
	}
	$values_for_mysql[] = $key;
	$bus_stations_to_square[] = $key;
}
$end_bus_stations_string .= ")";


//find all the bus station to bus station from $start_squares and $end_squares
$req = $bdd->prepare("
	SELECT *
	FROM bus_stations_to_bus_stations
	WHERE $start_bus_stations_string
	AND $end_bus_stations_string
	ORDER BY time
");

$req->execute($values_for_mysql);

$shortest_road_time = +INF;


foreach($start_squares as $key => $square){
	$square = new My_square($square[bus_line_id],$square[bus_line_name] , extract_path_from_string($square[path]), $square[time_to_bus_station]);
	$start_squares[$key] = $square;
}

foreach($end_squares as $key => $square){
	$square = new My_square($square[bus_line_id],$square[bus_line_name], extract_path_from_string($square[path]), $square[time_to_bus_station]);
	$end_squares[$key] = $square;
}

//todebug:
$count = 0;
//end to debug

$shortest_road = new Shortest_road();
//calculate the complete time of each possibility:
//and fusion the sames roads
while($bs2bss = $req->fetch()){
	//to debug
	$count++;
	//end to debug
	
	$road = json_decode($bs2bss[road_datas]);
	$bus_stations = /*&*/$road->bus_stations;
	
	//find the start square and the end square matching
	$from_square = $start_squares[$bs2bss[start_bus_station_id]];
	$to_square = $end_squares[$bs2bss[end_bus_station_id]];
	
	//look for same bus line in $bs2bss than in from square and to square
	//$bus_lines_parts_length = count($road->bus_lines_parts);
	
	$added_time_first_bus_line_part = 0;
	$first_bus_line_part_selected = $road->first_and_last_bstobss_to_mysql[0][0];
	$merged_first_bus_line_part_with_from_square = false;
	//from square
	if(is_array($road->first_and_last_bstobss_to_mysql[0])){
		if($road->first_and_last_bstobss_to_mysql[0][0] != null){
			foreach ($road->first_and_last_bstobss_to_mysql[0] as $key => $bus_line_part){
				if($bus_line_part->name == $from_square->name){
					//calculate the added time necesary than take the
					//shortest $bus_lin_part:
					$time_lost = $bus_line_part->time - $road->first_and_last_bstobss_to_mysql[0][0]->time;
					
					//if lost of time less than 10 minutes do not stay in the same bus
					//record the bus line part which can be join to from_square 
					//and how much time more than to use the shortest bus line part
					if($time_lost < $max_time_lost_whitout_changing_bus_line){
						$first_bus_line_part_selected = $bus_line_part;
						$added_time_first_bus_line_part = $time_lost;
						$merged_first_bus_line_part_with_from_square = true;
					}
					break;
				}
			}
		}
	}
	
	$added_time_last_bus_line_part = 0;
	$last_bus_line_part_selected = null;
	$merged_last_bus_line_part_with_to_square = false;
	//to square
	if(is_array($road->first_and_last_bstobss_to_mysql[0])){
		if($road->first_and_last_bstobss_to_mysql[1][0] != null){
			$first_or_last = 1;
		}
		else{
			$first_or_last = 0;
		}
		$last_bus_line_part_selected = $road->first_and_last_bstobss_to_mysql[$first_or_last][0];
			
		foreach ($road->first_and_last_bstobss_to_mysql[$first_or_last] as $key => $bus_line_part){
			if($bus_line_part->name == $from_square->name){
				//calculate the added time necesary than take the
				//shortest $bus_lin_part:
				$time_lost = $bus_line_part->time - $road->first_and_last_bstobss_to_mysql[$first_or_last][0]->time;
				
				//if lost of time less than 10 minutes do not stay in the same bus
				//record the bus line part which can be join to from_square 
				//and how much time more than to use the shortest bus line part
				if($time_lost < $max_time_lost_whitout_changing_bus_line){
					$last_bus_line_part_selected = $bus_line_part;
					$added_time_last_bus_line_part = $time_lost;
					$merged_last_bus_line_part_with_to_square = true;
				}
				break;
			}
		}
	}
	
	
	//if the last and first bus_line_part is the same one:
	if(($first_or_last == 0) && ($merged_first_bus_line_part_with_from_square == true)
	&& ($merged_last_bus_line_part_with_to_square == true)){
		//if the bus_line_part selected is not the same one :
		if($first_bus_line_part_selected->name != $last_bus_line_part_selected->name){
			//select the quicker one:
			if($added_time_first_bus_line_part < $added_time_last_bus_line_part){
				$last_bus_line_part = null;
				$added_time_last_bus_line_part = 0;
				$merged_last_bus_line_part_with_to_square = false;
			}
			else{
				$first_bus_line_part_selected = null;
				$added_time_first_bus_line_part = 0;
				$merged_first_bus_line_part_with_from_square = false;
			}
		}
	}
	
	//keep the shortest road:
	$total_time = $bs2bss[time] + $added_time_first_bus_line_part + $added_time_last_bus_line_part;
	if( $total_time < $shortest_road->total_time){
		$shortest_road->bs2bss = $bs2bss;
		$shortest_road->first_bus_line_part = $first_bus_line_part_selected;
		$shortest_road->end_bus_line_part = $last_bus_line_part;
		$shortest_road->total_time = $total_time;
		$shortest_road->from_square = $from_square;
		$shortest_road->to_square = $to_square;
		$shortest_road->merged_last_bus_line_part_with_to_square = $merged_last_bus_line_part_with_to_square;
		$shortest_road->merged_first_bus_line_part_with_from_square = $merged_first_bus_line_part_with_from_square;
	}
}


$road_to_send = $shortest_road->bs2bss;
$road_datas = json_decode($road_to_send[road_datas]);
$bus_stations = $road_datas->bus_stations;

//exctracts the complete datas of the selected road:
$file_to_open = $path_of_roads . "$road_to_send[start_bus_station_id]/$road_to_send[end_bus_station_id]";
/*$file = fopen($file_to_open, 'r') or die("can't open file\n");
$road = fread($file, 1000000);
fclose($file);*/
$road = file_get_contents($file_to_open) or die("can't open file\n");
$road = json_decode($road);

$nbr_of_bus_lines_part = count($road);

//if the first bus line part is selected
//(it s not only in case the road has only one bus line part
//and when the last bus line part is selected instead)
if($shortest_road->first_bus_line_part != null){
	///keep that one as the first bus line part:
	foreach ($road[0] as $bus_line_part) {
		if($bus_line_part->name == $shortest_road->first_bus_line_part->name){
			$road[0] = $bus_line_part;
			break;
		}
	}
}

//if the last bus line part is selected
//(it s not only in case the road has only one bus line part
//and when the first bus line part is selected instead)
if($shortest_road->last_bus_line_part != null){
	///keep that one as the last bus line part:
	foreach ($road[$nbr_of_bus_lines_part-1] as $bus_line_part) {
		if($bus_line_part->name == $shortest_road->first_bus_line_part->name){
			$road[$nbr_of_bus_lines_part-1] = $bus_line_part;
			break;
		}
	}
}

//add the from square and to square to the road:
$from_square = $shortest_road->from_square;
$to_square = $shortest_road->to_square;

divide_all_coordinates_of_path($from_square->path, $denominator_to_get_real_values);
divide_all_coordinates_of_path($to_square->path, $denominator_to_get_real_values);

if($shortest_road->merged_first_bus_line_part_with_from_square == true){
	//replace the first bus station by a temporary one:
	$bus_stations[0] = null;
	
	//merged the from square path with the first bus line:
	$road[0]->path = array_merge($from_square->path,$road[0]->path);
}
else{
	//add a tempory bus station:
	 array_unshift($bus_stations, null);
	 
	//add the from square as the first bus line:
	unset($from_square->bus_line_id);
	array_unshift($road, $from_square);
}

if($shortest_road->merged_last_bus_line_part_with_to_square == true){
	//replace the last bus station by a temporary one:
	$bus_stations[$nbr_of_bus_lines_part-1] = null;
	
	//merged the from square path with the first bus line:
	$road[$nbr_of_bus_lines_part-1]->path = 
		array_merge($to_square->path,$road[$nbr_of_bus_lines_part-1]->path);
}
else{
	//add a tempory bus station:
	 $bus_stations[] = null;
	 
	//add the to square as the first bus line:
	unset($to_square->bus_line_id);
	$road[] = $from_square;
}

$to_send->bus_stations = $bus_stations;
$to_send->bs2bss = $road;
$to_send->time = $shortest_road->total_time;

//to debug
$to_send->from_square = $from_square;
$to_send->to_square = $to_square;

//end to debug

echo json_encode($to_send);



function nearest_point($from_lat_lng, $interval, $table_name){
	global $bdd;
	
	$values[0] = $from_lat_lng[lat] - $interval;
	$values[1] = $from_lat_lng[lat] + $interval;
	$values[2] = $from_lat_lng[lng] - $interval;
	$values[3] = $from_lat_lng[lng] + $interval;
	
	$req = $bdd->prepare('
		SELECT * 
		FROM ' . $table_name . ' 
		WHERE ' . $table_name. '.lat BETWEEN ? AND ?
		AND	' . $table_name . '.lng BETWEEN ? AND ?
	');
			
	$nearest_point = array();
	while(count($nearest_point) == 0){

		$req->execute($values);
		$shortest_distance = +INF;
		
		while($square = $req->fetch()){
			
			$distance = real_distance_between_2_vertex($square, $from_lat_lng);
			
			if ($distance < $shortest_distance){
				$nearest_point = array();
				$shortest_distance = $distance;
				$nearest_point[] = $square;
			}
			elseif ($distance == $shortest_distance){
				$nearest_point[] = $square;
			}
		}
		$interval *= 2;
		$values[0] -= $interval;
		$values[1] += $interval;
		$values[2] -= $interval;
		$values[3] += $interval;
	}
	$nearest_point[distance] = $shortest_distance;
	return $nearest_point;
}



function nearest_points($from_lat_lng, $interval, $table_name, $ecart_min_between_d_min_and_d_max, $max_group_size){
	global $bdd;
	global $foot_speed;
	global $bus_speed;
	global $grid_path_mult;
	
	$values[0] = $from_lat_lng[lat] - $interval;
	$values[1] = $from_lat_lng[lat] + $interval;
	$values[2] = $from_lat_lng[lng] - $interval;
	$values[3] = $from_lat_lng[lng] + $interval;
	
	$req = $bdd->prepare('
		SELECT * 
		FROM ' . $table_name . ' 
		WHERE ' . $table_name. '.lat BETWEEN ? AND ?
		AND	' . $table_name . '.lng BETWEEN ? AND ?
		ORDER BY id
	');
	
	$nearest_point = array();
	$shortest_distance = INF;
	$further_distance = 0;
	
	do{

		$squares = array();
		$req->execute($values);
		
		while($square = $req->fetch()){
			
			//$distance = sqrt(pow($square[lat] - $from_lat_lng[lat], 2) + pow($square[lng] - $from_lat_lng[lng], 2));
			
			$vertex_1 = array();
			$vertex_2 = array();
			$vertex_1[lat] = $square[lat] / $grid_path_mult;
			$vertex_1[lng] = $square[lng] / $grid_path_mult;
			$vertex_2[lat] = $from_lat_lng[lat] / $grid_path_mult;
			$vertex_2[lng] = $from_lat_lng[lng] / $grid_path_mult;
			
			$distance = real_distance_between_2_vertex($vertex_1, $vertex_2);
			
			
			if ($distance < $shortest_distance){
				$shortest_distance = $distance;
			}
			if ($distance > $further_distance){
				$further_distance = $distance;
			}
			$square[distance] = $distance;
			$squares[] = $square;
		}
		
		if( $squares[0] == null){
			//if not any square found
			$interval *= 2;
			$values[0] -= $interval;
			$values[1] += $interval;
			$values[2] -= $interval;
			$values[3] += $interval;
		}
		else{
			//increase interval to get further_distance > shortest_distance + 300 metre environ
			//soit 15 square
			$values[0] -= $ecart_min_between_d_min_and_d_max + 2;
			$values[1] += $ecart_min_between_d_min_and_d_max + 2;
			$values[2] -= $ecart_min_between_d_min_and_d_max + 2;
			$values[3] += $ecart_min_between_d_min_and_d_max + 2;
		}
	}while(($ecart_min_between_d_min_and_d_max > ((int)$further_distance - (int)$shortest_distance) )
	||( $squares[0] == null));
	
	//create groups of squares of maximunm size = $max_group_size
	$nb_of_squares = count($squares);
	$i = 0;
	$squares_groups = array();
	$one_group = array();
	$group_size = 0;
	$previous_square = null;
	do {
		$square = $squares[$i];
		if(($previous_square == null) 
		|| ((($square[id] - $previous_square[id]) == 1)
		&& ($group_size < $max_group_size))){
			$one_group[] = $square;
			$group_size++;
		}
		else{
			$squares_groups[] = $one_group;
			$one_group = array();
			$one_group[] = $square;
			$group_size = 1;
		}
		$previous_square = $square;
		$i++;
	} while($i < $nb_of_squares);
	
	//save the last one:
	$squares_groups[] = $one_group;
	
	//for each group keep the nearest square to $from_lat_lng
	$nearest_squares = array();
	$nearest_squares_by_id_of_bus_station_linked = array();
	foreach ($squares_groups as $one_group) {
		$shortest_distance = INF;
		foreach($one_group as $square){
			if($square[distance]< $shortest_distance){
				$nearest_square = $square;
				$shortest_distance = $nearest_square[distance];
			}
		}
		$id_of_bus_station_linked = $nearest_square[id_of_bus_station_linked];
		
		$nearest_square[time_to_bus_station] = $nearest_square[distance] / $foot_speed + $nearest_square[length] / $bus_speed;
			
		//if the bus station linked already found from/to an other square
		if (array_key_exists($id_of_bus_station_linked, $nearest_squares_by_id_of_bus_station_linked)){
			///////////////////////////////////////////////////////
			//TODO get the real distance by foot to have a better result
			///////////////////////////////////////////////////////
			//keep the quickest way: 
			if($nearest_square[time_to_bus_station] < 
			$nearest_squares_by_id_of_bus_station_linked[$id_of_bus_station_linked][time_to_bus_station]){
				$nearest_squares_by_id_of_bus_station_linked[$id_of_bus_station_linked] = $nearest_square;
			}
		}
		else{
			$nearest_squares_by_id_of_bus_station_linked[$id_of_bus_station_linked] = $nearest_square;
		}
	}
	
	return $nearest_squares_by_id_of_bus_station_linked;
}


function nearest_bus_stations($from_lat_lng, $interval, $table_name){
	global $bdd;
	global $foot_speed;
	
	$values[0] = $from_lat_lng[lat] - $interval;
	$values[1] = $from_lat_lng[lat] + $interval;
	$values[2] = $from_lat_lng[lng] - $interval;
	$values[3] = $from_lat_lng[lng] + $interval;
	
	$req = $bdd->prepare('
		SELECT * 
		FROM ' . $table_name . ' 
		WHERE ' . $table_name. '.lat BETWEEN ? AND ?
		AND	' . $table_name . '.lng BETWEEN ? AND ?
		AND type != "invisble"
		AND type != "boundary"
	');
			
	$nearest_point = array();
	while(count($bus_stations) == 0){

		$req->execute($values);
		$shortest_distance = INF;
		
		while($bus_station = $req->fetch()){
			
			$bus_station[distance] = real_distance_between_2_vertex($from_lat_lng, $bus_station);
			$bus_station[time_to_bus_station] =  $bus_station[distance] / $foot_speed;
			$bus_stations[] = $bus_station;
		}
		$interval *= 2;
		$values[0] -= $interval;
		$values[1] += $interval;
		$values[2] -= $interval;
		$values[3] += $interval;
	}
	return $bus_stations;
}



// not used:
function nearest_point_from_array_to_point($array, $point){
	$shortest_distance = +INF;
	foreach ($array as $point_to_compare) {
		$distance = sqrt(pow($point[lat] - $point_to_compare[lat], 2) + pow($point[lng] - $point_to_compare[lng], 2));
		if ($distance < $shortest_distance){
			$nearest_point = $point_to_compare;
			$shortest_distance = $distance;
		}
	}
	$nearest_point[distance] = $shortest_distance * $grid_path_mult;
	
	return $nearest_point;
}
/*
function extract_path_from_string_2($path_as_string){
	
	if($path_as_string == null){
		return null;
	}
	
	$path = array();
	$path_lat_lngs = json_decode($path_as_string);
			
	foreach ($path_lat_lngs as $lat_lng){
		$vertex = new stdClass();
		$vertex->lat = $lat_lng->lat;
		$vertex->lng = $lat_lng->lng;
		$path[] = $vertex;
	}
	return $path;

}*/

function add_bus_stations_to_end_start_squares(&$start_or_end_squares, $start_nearest_bus_stations){
	foreach ($start_nearest_bus_stations as $start_bus_station) {
		foreach (array_keys($start_or_end_squares) as $bus_station_id_of_start_square) {
			if($bus_station_id_of_start_square == $start_bus_station[id]){
				if($start_bus_station[time_to_bus_station]
				   < $start_or_end_squares[$bus_station_id_of_start_square][time_to_bus_station]){
				   	//the time to go to the bus station by foot is quicker than by the "$start square"
					//create a false start square with the bus station:
					break;
				}
				else{
					//the time to go to the bus station by foot is quicker than by the "$start square"
					//keep the one from start square 
					continue 2;
				}
			}
		}
		//create a false start square with the bus station:
		$start_or_end_squares[$start_bus_station[id]]= array();
		$start_or_end_squares[$start_bus_station[id]][time_to_bus_station] = $start_bus_station[time_to_bus_station];
		$start_or_end_squares[$start_bus_station[id]][path] = json_encode(array());
		$start_or_end_squares[$start_bus_station[id]][bus_line_name] = null;
	}
}
?>
