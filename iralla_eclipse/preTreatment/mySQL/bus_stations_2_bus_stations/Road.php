<?php


/*class Road
 * ->bus_stations_list = array( BusStation, ..., BusStation)
 * ->bus_lines_part_list = array(, ...)
 * 
 * 
 * 
 * 
 * 
 */

class Road{
	public $start_bus_station;
	public $end_bus_station;
	public $bs2bss = array();
	//public $shortest_length;
	public $shortest_time;
	//public $path = array();
	
	//non-static methodes:
	
	//have BUG:
	public function replace_end_bus_station(Bus_station $end_bus_station){
		//TODO : verify if the last bus station of $this->bus_stations_list
		//is also modify
		$this->end_bus_station = clone $end_bus_station;
	}
	
	public function add_bus_station(Bus_station $bus_station){
		//TODO: verify that the last value of $this->bus_stations_list
		//do not change at the same time:
		$this->end_bus_station = clone $bus_station;
		array_push($this->bus_stations_list, $this->end_bus_station);
	}
	
	public function update_end_bus_station(){
		$this->end_bus_station = $this->bs2bss[count($this->bs2bss)-1]->end_bus_station;
	}
	
	public function extend_with(Road $other_road, Sub_red $sub_red){
		
		//if the first bus station of the other road
		//is the same as the last one of $this:
		if($other_road->start_bus_station->id == $this->end_bus_station->id){
			//clone $this to have the "shape" to create the new road:
			$road_shape = clone $this;
			$key_of_li2li_without_extension = array();
			$at_least_one_extension_done = false;
			$bs2bs_last_key = count($road_shape->bs2bss) - 1;
			//$li2li_last_key = count($road_shape->bs2bss[$bs2bs_last_key]->li2lis) - 1;
			
			//find which $li2li of the last $bs2bs of $road_shape 
			//can be extend with the first bs2bs of $road:
			//=>which have the same busline:
			foreach ($road_shape->bs2bss[$bs2bs_last_key]->li2lis as $this_key => $this_li2li) {
				foreach ($other_road->bs2bss[0]->li2lis as $other_li2li) {
					if ($this_li2li->bus_line->id == $other_li2li->bus_line->id){
						$this_li2li->extend_with($other_li2li);
						$at_least_one_extension_done = true;
						continue 2;
					}
				}
				//if there is not any extension found:
				//store the key:
				$key_of_li2li_without_extension[] = $this_key;
			}
			
			//if it was possible to add at least one extension
			if($at_least_one_extension_done === true){
				//remove the $li2li without extension:
				//previous step before to remove corresponding key
				//invert $key_of_li2li_without_extension to have the higher key removed first
				$key_of_li2li_without_extension = array_reverse($key_of_li2li_without_extension);
				foreach($key_of_li2li_without_extension as $key){
					array_splice($road_shape->bs2bss[$bs2bs_last_key]->li2lis, $key, 1);
				}
				
				//update the end_bus_station of bs2bs modified:
				$road_shape->bs2bss[$bs2bs_last_key]->update_end_bus_station();
				
				//add the next bs2bs of the other road to the shape road:
				$road_shape->bs2bss = array_merge(
					$road_shape->bs2bss,
					array_slice($other_road->bs2bss, 1)
				);
				
				////calculate the new shortest length of the $bs2bs modified:
				$road_shape->bs2bss[$bs2bs_last_key]->reinit_shortest_time();
				
			}
			//if it was not possible to add any extension
			else{
				//add the whole bs2bss of the new road to the shape road:
				$road_shape->bs2bss = array_merge($road_shape->bs2bss, $other_road->bs2bss);
			}
			
			//reinit the value of the last bus station:
			$road_shape->update_end_bus_station();
			
			//calculate the new shortest time:
			$road_shape->reinit_shortest_time();

			//$sub_red->add_road($road_shape);
			
			return $road_shape;
		}
		else{
			return false;
		}
	}
	
	public function extend_with_each_of(array $roads, Sub_red $sub_red){
		$new_roads = array();
		$start_bs = $this->start_bus_station->id;
		foreach ($roads as $road) {
			//if $roads->bs end is not the first of $this:
			if/*(*/($road->end_bus_station->id != $start_bs)/*
			//and a road as not been created with the start bus station of this
			//and the end bus station proposal:
			&&((array_key_exists($road->end_bus_station->id, $sub_red->roads_by_start_bs_id[$start_bs])) === false) )*/{
				$new_road =  $this->extend_with($road, $sub_red);
				if($new_road != false){
					$new_roads[] = $new_road;
				}
			}
		}
		return $new_roads;
	}
	
/*	public function reinit_shortest_length(){
		$shortest_length = 0;
		foreach($this->bs2bss as $bs2bs){
			$shortest_length += $bs2bs->get_shortest_length();
		}
		$this->shortest_length = $shortest_length;
		return $shortest_length;
	}*/
	
	public function reinit_shortest_time(){
		$shortest_time = 0;
		foreach($this->bs2bss as $bs2bs){
			$shortest_time += $bs2bs->get_shortest_time();
			//todo:
			//to a mainLine : 4 minutes = 240s
			//else: 6 minutes = 360s
			
			//time to change bus:
			$shortest_time += 300;
		}
		$this->shortest_time = (integer)$shortest_time;
		return $this->shortest_time;
	}
	
	public function format_datas_to_save(){
		$road_to_save = array();
		$road_to_save['start_bus_station_id'] = $this->start_bus_station->id;
		$road_to_save['start_lat'] = $this->start_bus_station->lat;
		$road_to_save['start_lng'] = $this->start_bus_station->lng;
		$road_to_save['end_bus_station_id'] = $this->end_bus_station->id;
		$road_to_save['end_lat'] = $this->end_bus_station->lat;
		$road_to_save['end_lng'] = $this->end_bus_station->lng;
		//$road_to_save['length'] = $this->shortest_length;
		$road_to_save['time'] = $this->shortest_time;
/*		
		
		$road_to_save['first_bus_line']
		$road_to_save['end_bus_line']
		*/
		
		//debug
		/*echo 'saving data for road from '.$road_to_save['start_bus_station_id']."id \n";
		echo '                     to   '.$road_to_save['end_bus_station_id']."id \n\n";
		*/
		
		$datas_to_save = Bs2bs::format_datas_to_save($this->bs2bss);
		
		$road_to_save['bs2bss_to_record_in_file'] = $datas_to_save->to_file;
		$road_to_save['road_datas'] = $datas_to_save->to_mysql;
		//$road_datas
		
		return $road_to_save;
	}
	
	//static methodes:
/*	public static function return_shortest_road_key(array $roads){
		$shortest_length = -log(0);
		foreach($roads as $key => $road){
			if(	$road->shortest_length < $shortest_length){
				$selected_key = $key;
				$shortest_length = $road->shortest_length;
			}
		}
		return $selected_key;
	}*/
	
	public static function return_shortest_road_key(array $roads){
		$shortest_time = +INF;
		foreach($roads as $key => $road){
			if($road->shortest_time < $shortest_time) {
				$selected_key = $key;
				$shortest_time = $road->shortest_time;
				$shortest_bs2bss_length = count($road->bs2bss);
			}
			elseif ($road->shortest_time == $shortest_time){
				$bs2bss_length = count($road->bs2bss);
				
				if($bs2bss_length < $shortest_bs2bss_length){
					$selected_key = $key;
					$shortest_time = $road->shortest_time;
					$shortest_bs2bss_length = $bs2bss_length;
				}
			}
		}
		return $selected_key;
	}
	
	public static function extract_shortest_road(array &$roads){
		$key = self::return_shortest_road_key($roads);
		$array_of_one_road = array_splice($roads, $key, 1);
		return $array_of_one_road[0];
	}
	
	public static function  save_road($road_to_save){
		global $path_to_save;
		$start_bus_station_id = $road_to_save['start_bus_station_id'];
		$end_bus_station_id = $road_to_save['end_bus_station_id'];
		
		//create folder if do not exists:
		$directory_to_save_the_road = "$path_to_save/$start_bus_station_id";
		if(!is_dir($directory_to_save_the_road)){
			if (!mkdir($directory_to_save_the_road)) {
	   			die('error to create folders\n');
			}
		}
		
		//save the path
		$file_to_save = "$directory_to_save_the_road/$end_bus_station_id";
		$fh = fopen($file_to_save, 'w') or die("can't open file\n");
		fwrite($fh, $road_to_save['bs2bss_to_record_in_file']);
		fclose($fh);
				
		//save the road on the database
		unset($road_to_save['bs2bss_to_record_in_file']);
		saveToDb(array($road_to_save) , 'bus_stations_to_bus_stations');
		return;		
	}
	
	//construct only from one bs2bs
	public function __construct(Bs2bs $bs2bs){
		//save the first bus stations
		$this->start_bus_station = $bs2bs->start_bus_station;
		
		//add the last bus stations
		$this->end_bus_station = $bs2bs->end_bus_station;
		
	/*	//length of the road:
		$this->shortest_length = $bs2bs->get_shortest_length();
	*/	
		
		
		//////////////////////////////////////////////////////////////////////////////////////
		//todo:  change 300s depending of type of road:
		
		//time of the road:
		$this->shortest_time = $bs2bs->get_shortest_time() + 300;
		
		//save $bs2bs
		$this->bs2bss[] = $bs2bs;
	}
	
	public function __destruct(){
		global $count_destruct;
		$count_destruct++;
		unset($this->start_bus_station);
		unset($this->end_bus_station);
		//unset($this->shortest_length);
		unset($this->shortest_time);
		unset($this->bs2bss);
	}
	
	public function __clone(){
		$bs2bss_clone = array();
		foreach ($this->bs2bss as $bs2bs) {
			$bs2bss_clone[] = clone $bs2bs;
		}
		$this->bs2bss = $bs2bss_clone;
	}
}

?>















