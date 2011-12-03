<?php

//Bs2bs : Bus_station_to_bus_station
class Bs2bs{
	public $start_bus_station;
	public $end_bus_station;
	public $li2lis = array();
	public $sub_red;
	//public $shortest_length;
	public $shortest_time;
	
	//non static mehtodes:
	public function add_a_li2li(Li2li $new_li2li){
		$this->li2lis[] = $new_li2li;
		/*if($this->shortest_length > $new_li2li->length){
			$this->shortest_length = $new_li2li->length;
		}*/
		if($this->shortest_time > $new_li2li->time){
			$this->shortest_time = $new_li2li->time;
		}
	}
	
/*	public function get_shortest_length(){
		return $this->shortest_length;
	}*/

	public function get_shortest_time(){
		return $this->shortest_time;
	}
	
/*	public function reinit_shortest_length(){
		$this->shortest_length = -log(0); //INF
		foreach($this->li2lis as $li2li){
			if($li2li->length < $this->shortest_length){
				$this->shortest_length = $li2li->length;
			}
		}
	}*/

	public function reinit_shortest_time(){
		$this->shortest_time = INF;
		foreach($this->li2lis as $li2li){
			if($li2li->time < $this->shortest_time){
				$this->shortest_time = $li2li->time;
			}
		}
	}

	//actualize the end_bus_station:
	public function update_end_bus_station(){
		$this->end_bus_station = clone $this->li2lis[0]->end_bus_station;
	}		
				
	//static methodes:
	public static function format_datas_to_save(array $bs2bss){
		
		//to debug
		$end_bs_id = $bs2bss[count($bs2bss) - 1]->end_bus_station->id;
		
		
		
		//end to debug
		
		
		if(count($bs2bss) > 0){
			//extract all bus stations:
			$bss_to_save = array();
			$bus_lines_parts  = array();
			$datas_to_mysql = array();
			$part_lines_to_mysql = array();
			$first_and_last_bstobss_to_mysql = array();
			$bs2bss_length = count($bs2bss);
			$compt = 0;
			
			foreach($bs2bss as $bs2bss_key => $bs2bs){
				$compt++;
				//save each bus station which has a name:
				$bs = $bs2bs->start_bus_station;
				
				if($bs->name != null){
					$bs_to_save = new stdClass();
					$bs_to_save->name = $bs->name;
					$bs_to_save->lat = $bs->lat;
					$bs_to_save->lng = $bs->lng;
					$bss_to_save[] = $bs_to_save;
				}
				else{
					$bss_to_save[] = null;
				}
				
				//save each lines:
				$lines['steps'] = array();
				$part_lines = array();
				
				//init
				$start_bs_coord = new Lat_lng($bs2bs->start_bus_station->lat, $bs2bs->start_bus_station->lng);
				$end_bs_coord = new Lat_lng($bs2bs->end_bus_station->lat, $bs2bs->end_bus_station->lng);
				$i_max = 0;
				
				foreach ($bs2bs->li2lis as $li2li) {
					//determine the flow of the li2li:
					$start_previous_vertex = $li2li->start_link->previous_vertex_index;
					$end_previous_vertex = $li2li->end_link->previous_vertex_index;
					$flow = $end_previous_vertex - $start_previous_vertex;
					
					$part_line = new stdClass();
					$part_line->name = $li2li->bus_line->name;
					$part_line->path = $li2li->path;
					//$part_line->length = $li2li->length;
					$part_line->time = $li2li->time;
					
					//add to the path the points of the start and
					//end links:
					$start_link_coord = new Lat_lng($li2li->start_link->lat, $li2li->start_link->lng);
					$end_link_coord = new Lat_lng($li2li->end_link->lat, $li2li->end_link->lng);

					/*$part_line->length += real_distance_between_2_vertex($start_link_coord, $part_line->path[0]);
					array_unshift($part_line->path, $start_link_coord);
					$part_line->length += real_distance_between_2_vertex($part_line->path[count($part_line->path)-1], $end_link_coord);
					array_push($part_line->path, $end_link_coord);
					*/
					
					
					if(isset($part_line->path[0])){
						$part_line->time += real_distance_between_2_vertex($start_link_coord, $part_line->path[0]) / $li2li->sub_red->speeds[$li2li->type];
						$part_line->time += real_distance_between_2_vertex($part_line->path[count($part_line->path)-1], $end_link_coord) / $li2li->sub_red->speeds[$li2li->type];
					}
					else{
						$part_line->time += real_distance_between_2_vertex($start_link_coord, $end_link_coord) / $li2li->sub_red->speeds[$li2li->type];
					}
					array_unshift($part_line->path, $start_link_coord);
					array_push($part_line->path, $end_link_coord);
					$part_line->time = (int)$part_line->time;
					
					//add to the path the points of the start and
					//end bus_station:
					//RQ : NOT TAKE TO CALCULATE THE LENGTH OF THE ROAD 
			/*		array_unshift($part_line->path, $start_bs_coord);
					array_push($part_line->path, $end_bs_coord);*/

					///////////////////////////////////////////////////////////////////////////
					///					FORMAT THE WAY TO KEEP THE DATAS					///
					///////////////////////////////////////////////////////////////////////////
					
					//save ordered by length the parts lines :
					//save ordered by time the parts lines :
					$i = $i_max;
					if($i_max > 0){
						//while(($part_line->length < $part_lines[$i-1]->length)
						while(( $i > 0) && ($part_line->time < $part_lines[$i-1]->time)){
							$part_lines[$i] = $part_lines[$i-1];
							$i--;
						}
					}
					$part_lines[$i] = $part_line;
					$i_max++;
				}
				
				$part_lines_to_mysql = array();
				//if the first $bs2bs or the last one:
				if(($bs2bss_key == 0) || ($bs2bss_length == $compt)){
					
					foreach ($part_lines as $key => $part_line){
						//removed the longest ones:
						//if a length is higher of 500m
						//if ($part_line->length > ($part_lines[0]->length + 500)){
						//if a time is higher of 5 minutes: 300 s
						if ($part_line->time > ($part_lines[0]->time + 300)){
							array_splice($part_lines, $key);
						}
						//else keep the part_line in part_line_to_mysql whithout the path:
						else{
							$part_line_to_mysql = clone $part_line;
							unset($part_line_to_mysql->path);
							$part_lines_to_mysql[] = $part_line_to_mysql;
						}
					}
					$first_and_last_bstobss_to_mysql[] = $part_lines_to_mysql;
				}
				else{
					//keep only the shortest bus station:
					$part_lines = $part_lines[0];
				}
				
				$bus_lines_parts[] = $part_lines;
			}
			
			//record the last bus station:
			$bs = $bs2bs->end_bus_station;
			if($bs->name != null){
				$bs_to_save = new stdClass();
				$bs_to_save->name = $bs->name;
				$bs_to_save->lat = $bs->lat;
				$bs_to_save->lng = $bs->lng;
				$bss_to_save[] = $bs_to_save;
			}
			else{
				$bss_to_save[] = null;
			}
		}
		else{
			$bss_to_save = array();
			$bus_lines_parts = array();
		}
		
		$to_mysql = new stdClass();
		$to_mysql->bus_stations = $bss_to_save;
		$to_mysql->first_and_last_bstobss_to_mysql = $first_and_last_bstobss_to_mysql;
		
		$datas_to_save = new stdClass();
		$datas_to_save->to_mysql = json_encode($to_mysql);
		/*if(is_nan($bus_lines_parts)){
			exit("bus_lines_parts is not a member");
		}*/
		
		//to debug
		//verify if there are NANs in $bus_lines_parts:
		$length = count($bus_lines_parts);
		for ($j = 0; $j < $length; $j++) {
			if(($j == 0) || ($j == $length - 1)){
				foreach ($bus_lines_parts[$j] as $part) {
					
					if(!isset($part->time)){
						exit("length not set");
					}
					
					if(is_nan($part->time)){
						exit("nan detected");
					}
					
					foreach ($part->path as $lat_lng) {
						if ((is_nan($lat_lng->lat)) || (is_nan($lat_lng->lng))) {
							exit("nan detected");
						}
					}
				}
			}
			else{
				if(is_nan($bus_lines_parts[$j]->time)){
					exit("nan detected");
				}
				
				foreach ($bus_lines_parts[$j]->path as $lat_lng) {
					if ((is_nan($lat_lng->lat)) || (is_nan($lat_lng->lng))) {
						exit("nan detected");
					}
				}	
			}
		}
		//end to debug
		$datas_to_save->to_file = json_encode($bus_lines_parts);
		
		return $datas_to_save;
	}

	//construct mehtode:
	public function __construct(Li2li $li2li, Sub_red $sub_red){
		$this->start_bus_station = $li2li->start_bus_station;
		$this->end_bus_station = $li2li->end_bus_station;
		$this->li2lis[] = $li2li;
		//$this->shortest_length = $li2li->length;
		$this->shortest_time = $li2li->time;
		$this->sub_red = $sub_red;
	}

	public function __clone(){
		$li2lis_clone = array();
		foreach ($this->li2lis as $li2li) {
			$li2lis_clone[] = clone $li2li;
		}
		$this->li2lis = $li2lis_clone;
	}
}
?>





