<?php
class Road_part{
	public $start;
	public $end;
	public $bus_line;
	public $distance;
	public $speed;
	public $time;
	public $bus_line_type;
	
	
	public static function create_road_part_from_points_out_of_bus_line(
			Point $start,
			$start_f_and_l_square,
			Point $end,
			$end_f_and_l_square,
			Busline $bl,
			$flow = "normal"){
		
		if(($bl->flow != $flow) && ($bl->flow != "both")){
			return false;
		}
		
		//calculate nearest point on the bus line from the start point
		$start_pt_on_bl = $start->projection_on_bus_line_with_f_and_l_square(
				$bl,
				$start_f_and_l_square);
		
		//calculate nearest point on the bus line from the end point
		$end_pt_on_bl = $end->projection_on_bus_line_with_f_and_l_square(
				$bl,
				$end_f_and_l_square);
		
		return new Road_part($start_pt_on_bl, $end_pt_on_bl);
	}
	
	public function __construct(
			Point_on_bus_line $start_point_on_bus_line, 
			Point_on_bus_line $end_point_on_bus_line,
			$flow = "normal",
			$speed){
	    
		$bl = $start_point_on_bus_line->bus_line;
				
		if(($bl->flow != $flow) && ($bl->flow != "both")){
			return false;
		}
		
		//TODO verify the bl of each point on bus line is the same
		//else return an error
		
		//TODO if bus line not closed verify if the start and end position
		//are coherents with the flow else return false
		
		//TODO verify if bus line as a type
		
		$this->start = $start_point_on_bus_line;
		$this->end = $end_point_on_bus_line;
		$this->bus_line = $bl;
		
		//distance:
		$diff_distance_from_first_vertex =
		$end_point_on_bus_line->distance_from_first_vertex
		-
		$start_point_on_bus_line->distance_from_first_vertex;
		
		if(($flow == "normal") && ($diff_distance_from_first_vertex >= 0)
			|| ($flow == "reverse") && ($diff_distance_from_first_vertex <= 0)){
			$this->distance = abs($diff_distance_from_first_vertex);
		}
		else{
			$this->distance = $bus_line->total_distance - abs($diff_distance_from_first_vertex);
		}
		
		$this->speed = $speed;
		$this->time = $this->distance / $speed;
		$this->bus_line_type = $bl->type;
		
	}
	
	
}