<?php

//L_2_l : Link to link
class Li2li{
	public $id;
	public $length;
	public $time;
	public $type;
	public $start_link;
	public $start_bus_station;
	public $end_bus_station;
	public $end_link;
	public $bus_line;
	public $flow;
	//public $next_Li2lis;
	public $path = array();
	public $sub_red;
	
	public function store(){
		$this->sub_red->add_li2li($this);
	}
	
	public function find_flow(){
		//determinate the flow:
		if($this->start_link->previous_vertex_index < $this->end_link->previous_vertex_index ){
			$this->flow = 'normal';
		}
		elseif($this->start_link->previous_vertex_index > $this->end_link->previous_vertex_index ){
			$this->flow = 'opposite';
		}
		else{
			if($this->start_link->distance_from_previous_vertex < $this->end_link->distance_from_previous_vertex ){
				$this->flow = 'normal';
			}
			else if($this->start_link->distance_from_previous_vertex > $this->end_link->distance_from_previous_vertex ){
				$this->flow = 'opposite';
			}
			else{
				exit("can not determinate the flow \n");
			}
		}
	}
	
	private function set_path($path_as_json){

		$start_previous_vertex = $this->start_link->previous_vertex_index;
		$end_previous_vertex = $this->end_link->previous_vertex_index;
		
		if($start_previous_vertex != $end_previous_vertex){
			/*//extract the path of the line:
			$couple_of_coordinates = array();
			$couple_of_coordinates = explode(",", $path_as_string);
				
			foreach ($couple_of_coordinates as $coordCouple){
				$lat_lng_array = explode(" ", $coordCouple);
				$lat_lng = new Lat_lng($lat_lng_array[0], $lat_lng_array[1]);
				array_push($this->path, $lat_lng);
			}*/
			
			$this->path = json_decode($path_as_json);
			
			
			//if the flow is  in the same way as the vertex order
			if($start_previous_vertex < $end_previous_vertex){
				//remove the following part of the bus line:
				array_splice($this->path, $end_previous_vertex + 1);
				
				//remove the previous part of the path:
				array_splice($this->path, 0, $start_previous_vertex + 1);
			}
			//else
			else if($start_previous_vertex > $end_previous_vertex){
				//remove the previous part of the path:
				//(end of the polyline)
				array_splice($this->path, $start_previous_vertex + 1);
				
				//remove the folllowing part of the bus line:
				//(beginning of the polyline)
				array_splice($this->path, 0, $end_previous_vertex + 1);
				
				//change the order of the vertex:
				$this->path = array_reverse($this->path);
			}
		}
	}
	
	public function extend_with(Li2li $other_li2li){
		//extend the path:
		//if ($this->flow == 'normal'){
			$this->path = array_merge($this->path, $other_li2li->path);
		/*}
		else if ($this->flow == 'opposite'){
			$this->path = array_merge($other_li2li->path, $this->path);
		}*/
		
		/*//calculate the new length
		$this->length += $other_li2li->length;*/
		
		//calculate the new time
		$this->time += $other_li2li->time;
		
		//reinit the end_*
		$this->end_link = clone $other_li2li->end_link;
		$this->end_bus_station = clone $other_li2li->end_bus_station;
		
		return $this;
	}

	/*public function __clone(){
		$paths_clone = array();
		foreach ($this->paths as $li2li) {
			$li2lis_clone[] = clone $li2li;
		}
		$this->li2lis = $li2lis_clone;
		
	}*/
	
	public function __construct($connection, Sub_red $sub_red){
		$this->id = $connection[id];
		$this->length = $connection[length];
		$this->type = $connection[busLineType];
		$this->time = (integer)($this->length / $sub_red->speeds[$this->type]);
		$this->sub_red = $sub_red;
		$this->start_link = new Link(
			$connection[linkIdDeparture],
			$connection[linkPrevIndexDeparture],
			$connection[linkDistanceToPrevIndexDeparture],
			$connection[linkLatDeparture],
			$connection[linkLngDeparture],
			$connection[busStationIdDeparture]
			);
		$this->start_bus_station = new Bus_station(
			$connection[busStationIdDeparture],
			$connection[busStationNameDeparture],
			$connection[busStationLatDeparture],
			$connection[busStationLngDeparture],
			$connection[busStationTypeDeparture]
			);
		$this->end_bus_station = new Bus_station(
			$connection[nextBusStationId],
			$connection[nextBusStationName],
			$connection[nextBusStationLat],
			$connection[nextBusStationLng],
			$connection[nextBusStationType]
			);
		$this->end_link = new Link(
			$connection[nextLinkId],
			$connection[nextLinkPrevIndex],
			$connection[nextLinkDistanceToPrevIndex],
			$connection[nextLinkLat],
			$connection[nextLinkLng],
			$connection[nextBusStationId]
			);
		$this->bus_line = new Bus_line(
			$connection[busLineId],
			$connection[busLineName],
			$connection[busLinePath]
			);
		
		$this->set_path($connection[busLinePath]);
		//$this->next_Li2lis = array();
		
		if($this->type != "by_foot"){
			$this->find_flow();
		}
		
		$this->store();
	}
	
	public function __clone(){
		$this->start_link = clone $this->start_link;
		$this->start_bus_station = clone $this->start_bus_station;
		$this->end_bus_station = clone $this->end_bus_station;
		$this->end_link = clone $this->end_link;
		$this->bus_line = clone $this->bus_line;
	}
}

?>
















