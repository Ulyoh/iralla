<?php
//todebug
require_once 'access_to_db.php';
$beginning_time = time();
//end todebug

require_once 'saveToDb.php';
require_once 'Bs2bs.php';
require_once 'Bus_line.php';
require_once 'Bus_station.php';
require_once 'Lat_lng.php';
require_once 'Li2li.php';
require_once 'Link.php';
require_once 'Road.php';
require_once 'Sub_red.php';
require_once 'tools.php';

ini_set('memory_limit', '2000M');
set_time_limit ( 100000 );
$path_to_save = "c:/roads3";   //todebug 35

if (!is_dir($path_to_save)){
	if(!mkdir($path_to_save)){
		die("path to save could not be created");
	}
}

function bus_stations_to_bus_station2($first_id_of_bus_station_to_do, $nbr_of_bus_station_to_do,$path_to_save){
	echo "running bus station to bus station \n";
	//extract all the base nearest_connected_bus_stations
	global $bdd;

	$request = $bdd->query("
							SELECT 	
								*
							FROM  nearest_connected_bus_stations
							ORDER BY  busStationIdDeparture");
	
	//creation of a sub_red:
	$sub_red = new Sub_red($path_to_save);
	
	
	//$nbr_of_connections_done = 0;
	//create all the Links from the datas:
	while( $connection = $request->fetch()){
		if($connection['nextBusStationId'] != $connection['busStationIdDeparture']){
			$one_li2li = new Li2li($connection, $sub_red);
			//$nbr_of_connections_done++;
			//echo "$nbr_of_connections_done conections done\n";
		}
	}
	
	$sub_red->generate_roads_from_bs2bss();
	//to test if size as previous:
	//$sub_red->sub_red_json_started = json_encode($sub_red);
	
	$sub_red->find_all_roads_for_each_bus_stations($first_id_of_bus_station_to_do, $nbr_of_bus_station_to_do);


	//return false if the last bus station start is done:
	if(($first_id_of_bus_station_to_do + $nbr_of_bus_station_to_do) <= max(array_keys($sub_red->roads_of_one_bs2bs_by_start_bs_id))){
		return true;
	}
	else{
		return false;
	}
}

/* Vérification de la connexion */
if (mysqli_connect_errno()) {
	printf("échec de la connexion : %s\n", mysqli_connect_error());
	exit();
}

//to debug

$count_destruct = 0;
//end to debug

$count = 0;

//$bdd->query("TRUNCATE TABLE bus_stations_to_bus_stations");
$first_id_of_bus_station_to_do = 302;
$nbr_of_bus_station_to_do = 1;
$stop_when_count_egal =150 ;
//bus_stations_to_bus_station2($first_id_of_bus_station_to_do, $nbr_of_bus_station_to_do, $path_to_save);
while((bus_stations_to_bus_station2($first_id_of_bus_station_to_do, $nbr_of_bus_station_to_do, $path_to_save))
&&($stop_when_count_egal > $count)){
	$first_id_of_bus_station_to_do += $nbr_of_bus_station_to_do;
	gc_collect_cycles();
	$count++;
}
$total_time_min = (time() - $beginning_time) / 60;
echo "total time: $total_time_min minutes\n";
?>