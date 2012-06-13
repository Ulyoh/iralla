<?php

class Road_result{
	private $walk_speed;
	public $from;
	public $to;
	public $total_distance;
	public $total_bus_distance;
	public $total_main_bus_distance;
	public $total_walk_distance;
	public $total_time;
	public $road_parts;
	public $road_parts_length;
	
	public function add_one_busline($busline, $length, Point $start_point_on_bus_line, Point $end_point_on_bus_line){
	//adding the new mouvements points:
		//1) remove and save the last point:
		$end = array_pop($this->chgt_points);
		
		//2) add the start and end point on bus line:
		array_push($this->chgt_points, $start_point_on_bus_line, $end_point_on_bus_line, $end);
		
		//3 add the mouvement:
		array_push($this->chgt_points, $busline, "walk");
		
	}
	
	public function reset_walk_distance_and_time(){
		$previous_point = $this->from;
		for ($i = 0; $i < $this->road_parts->road_parts_length - 1; $i++){
			$road_part = $this->road_parts[$i];
			$next_point = $this->road_parts[$i+1]->end;
			if($road_part == "walk"){
				$road_part = stdClass();
				$road_part->type = "walk";
				$road_part->distance = $previous_point->earth_distance_to($next_point);
				$road_part->time = $road_part->distance / $this->walk_speed;
				$this->road_parts[$i] = $road_part;
				$previous_point = null;
				$next_point = null;
			}
			else{
				$previous_point = $this->road_parts[$i]->$end;
			}
		}
		
		//last road part:
		// $i is = last road part - 1
		if($this->road_parts[$i]->type == "walk"){
			$next_point = $this->to;
			$road_part = stdClass();
			$road_part->type = "walk";
			$road_part->distance = $previous_point->earth_distance_to($next_point);
			$road_part->time = $road_part->distance / $this->walk_speed;
			$this->road_parts[$i] = $road_part;
		}
	}
	
	function __construct(Point $start, Point $end, $walk_speed){
		//set to and from point:
		$this->from = $start;
		$this->to = $end;
		
		//set walk speed:
		$this->walk_speed = $walk_speed;
		
		//set $this->chgt_points:
		if(isset($start->type) && ($start->type == 'bus_line')){
			$this->chgt_points[] = $start;
		}
		else{
			$this->chgt_points[] = null;
		}
		

		if(isset($end->type) && ($end->type == 'bus_line')){
			$this->chgt_points[] = $end;
		}
		else{
			$this->chgt_points[] = null;
		}
		
		//set the mouvements:
		$this->mouvements[] = "walk";
	}
}
