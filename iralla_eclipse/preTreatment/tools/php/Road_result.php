<?php

class Road_result{
	public $from;
	public $to;
	public $total_distance;
	public $total_bus_distance;
	public $total_main_bus_distance;
	public $total_walk_distance;
	public $total_time;
	public $chgt_points;
	public $mouvements;
	
	public function add_one_busline($busline, $length, Point $start_point_on_bus_line, Point $end_point_on_bus_line){
	//adding the new mouvements points:
		//1) remove and save the last point:
		$end = array_pop($this->chgt_points);
		
		//2) add the start and end point on bus line:
		array_push($this->chgt_points, $start_point_on_bus_line, $end_point_on_bus_line, $end);
		
		//3 add the mouvement:
		array_push($this->chgt_points, $busline, "walk");
		
	}
	
	function __construct(Point $start, Point $end){
		//set to and from point:
		$this->from = $start;
		$this->to = $end;
		
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
