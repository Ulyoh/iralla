<?php

class Enter_and_out{
	public $enter;
	public $out;
}

function find_areas_to_make_squares($bus_line){
	$path = $bus_line['path'];
	$path_length = $bus_line['path_length'];
	
	$areas_opposite = array();
	$area_opposite = new Enter_and_out;
	$area_opposite->enter = 0;
	$area_opposite->out = $path_length-1;
	$areas_opposite[] = clone $area_opposite;

	//if the line is a feeder:
	if($bus_line['type'] == 'feeder'){

		//FOUND AREAS BETWEEN VERTEX OF THE LINE WHERE FOUND THE SQUARES:

		$bus_line['areaOnlyBusStations'] = json_decode($bus_line['areaOnlyBusStations']);
		$areaOnlyBusStations_length = count($bus_line['areaOnlyBusStations']);

		//converte string to object in $bus_line['areaOnlyBusStations']:
		if ($areaOnlyBusStations_length > 0 ){
			//reinit $areas_opposite to remove the default value
			$areas_opposite = array();

			foreach ($bus_line['areaOnlyBusStations'] as $key => $enter_and_out)
			{
				$looking_for_vertex = new Vertex($enter_and_out->enter->lat, $enter_and_out->enter->lng);
				$bus_line['areaOnlyBusStations'][$key]->enter =
				found_vertex_index_from_coordinates(
						$looking_for_vertex,
						$path);

				$looking_for_vertex = new Vertex($enter_and_out->out->lat, $enter_and_out->out->lng);
				$bus_line['areaOnlyBusStations'][$key]->out =
				found_vertex_index_from_coordinates(
						$looking_for_vertex,
						$path);

				if(($key == $areaOnlyBusStations_length -1) && ( $bus_line['areaOnlyBusStations'][$key]->out == 0 )){
					$bus_line['areaOnlyBusStations'][$key]->out = $path_length - 1;
				}
			}

			//if a unique area only bus station and at the beginning of
			//the path:
			if ( ($areaOnlyBusStations_length == 1) && ($bus_line['areaOnlyBusStations'][0]->enter == 0)){
				$area_opposite->enter = $bus_line['areaOnlyBusStations'][0]->out;
				$area_opposite->out = $path_length-1;
				$areas_opposite[] = clone $area_opposite;
			}
			else{
				foreach ($bus_line['areaOnlyBusStations'] as $key => $area_only_bus_station)
				{
					if($key == 0){
						if($bus_line['areaOnlyBusStations'][0]->enter == 0)
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
							&& ($bus_line['areaOnlyBusStations'][$key]->out != $path_length - 1 ))
					{
						$area_opposite->out = $path_length - 1;
						$areas_opposite[] = clone $area_opposite;
					}
				}
			}
		}
	}
	//END FOUND AREAS BETWEEN VERTEX OF THE LINE WHERE FOUND THE SQUARES:

	return $areas_opposite;
}
