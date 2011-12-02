<?php
require_once 'access_to_db.php';

$multipicador = 10000000;
$grid_path = 0.001;
$grid_path_mult = bcmul($multipicador, $grid_path);

$request = $_POST['q'];
//$request = '{"start":{"lat":-2.1893502,"lng":-79.8890996},"end":{"lat":-2.2148137,"lng":-79.8882131}}';
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

$result;

$start[lat] = $request->start->lat;
$start[lng] = $request->start->lng;
$end[lat] = $request->end->lat;
$end[lng] = $request->end->lng;

$interval = 20 * $grid_path;

$start_nearest_bus_station = nearest_square($start, $interval, "bus_stations");
$start_nearest_bus_station[distance] *= $grid_path_mult;
$end_nearest_bus_station = nearest_square($end, $interval, "bus_stations");
$end_nearest_bus_station[distance] *= $grid_path_mult;

$start[lat] =abs( bcmul($request->start->lat, $grid_path_mult));
$start[lng] =abs( bcmul($request->start->lng, $grid_path_mult));
$end[lat] =abs( bcmul($request->end->lat, $grid_path_mult));
$end[lng] =abs( bcmul($request->end->lng, $grid_path_mult));

$interval = 50;

$start_square = nearest_square($start, $interval, "from_square");
$end_square = nearest_square($end, $interval, "to_square");

//TODO: modify to find the shortest way:
$from_square = null;
if($start_square[distance] < $start_nearest_bus_station[distance]){
	$start_bus_station_id = $start_square[0][id_of_bus_station_linked];
	$from_square = $start_square[0][path];
	
}else{
	$start_bus_station_id = $start_nearest_bus_station[0][id];
}

$to_square = null;
if($end_square[distance] < $end_nearest_bus_station[distance]){
	$end_bus_station_id = $end_square[0][id_of_bus_station_linked];
	$to_square = $end_square[0][path];
}else{
	$end_bus_station_id = $end_nearest_bus_station[0][id];
}

if ($start_bus_station_id != $end_bus_station_id){
	$req = $bdd->prepare('
	SELECT *
	FROM bus_stations_to_bus_stations
	WHERE start_bus_station_id = ?
	AND end_bus_station_id = ?'
	);
		
	$req->execute(array($start_bus_station_id, $end_bus_station_id));
	
	$bs2bs = $req->fetch();
	$result = json_decode($bs2bs[road_datas]);
}
else{
	
	
	$req = $bdd->prepare('
		SELECT id, type, name, lat, lng
		FROM bus_stations
		WHERE bus_stations.id = ?
	');
	$req->execute(array($start_bus_station_id));
	$result->bus_stations = array();
	$result->bus_stations[0] = $req->fetch();
}

if($result->bus_lines_parts){
	foreach($result->bus_lines_parts as $key => $bus_line_part){
		if(is_array($result->bus_lines_parts[$key])){
			$result->bus_lines_parts[$key] = $bus_line_part[0];
		}
	}
}
else{
	$result->bus_lines_parts = array();
}

if($from_square){
	$bus_line_part = new stdClass;
	$bus_line_part->path = json_decode($from_square);
array_unshift($result->bus_lines_parts, $bus_line_part);
}

if($to_square){
	$bus_line_part = new stdClass;
	$bus_line_part->path = json_decode($to_square);
	$result->bus_lines_parts[] = $bus_line_part;
}
/*
$result->from_square = json_decode($from_square);
$result->to_square = json_decode($to_square);
*/

echo json_encode($result);

function nearest_square($lat_lng, $interval, $table_name){
	global $bdd;
	
	$values[0] = $lat_lng[lat] - $interval;
	$values[1] = $lat_lng[lat] + $interval;
	$values[2] = $lat_lng[lng] - $interval;
	$values[3] = $lat_lng[lng] + $interval;
	
	$req = $bdd->prepare('
		SELECT * 
		FROM ' . $table_name . ' 
		WHERE ' . $table_name. '.lat BETWEEN ? AND ?
		AND	' . $table_name . '.lng BETWEEN ? AND ?
	');
			
	$nearest_square = array();
	while(count($nearest_square) == 0){

		$req->execute($values);
		$shortest_distance = +INF;
		
		while($square = $req->fetch()){
			$distance = sqrt(pow($square[lat] - $lat_lng[lat], 2) + pow($square[lng] - $lat_lng[lng], 2));
			
			if ($distance < $shortest_distance){
				$nearest_square = array();
				$shortest_distance = $distance;
				$nearest_square[] = $square;
			}
			elseif ($distance == $shortest_distance){
				$nearest_square[] = $square;
			}
		}
		$interval *= 2;
		$values[0] -= $interval;
		$values[1] += $interval;
		$values[2] -= $interval;
		$values[3] += $interval;
	}
	$nearest_square[distance] = $shortest_distance;
	return $nearest_square;
}



?>
