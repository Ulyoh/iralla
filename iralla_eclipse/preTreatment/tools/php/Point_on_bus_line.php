<?php
class Point_on_bus_line extends Point{
	public $bus_line;
	public $previous_index;
	public $distance_from_previous_index;
	public $bus_station;
	
	public function __construct(
			$x,
			$y,
			$bus_line,
			$previous_index,
			$distance_from_previous_index,
			$bus_station = "none"){
		
		parent::__construct($x,$y);
		
		$this->bus_line = $bus_line;
		$this->previous_index = $previous_index;
		$this->distance_from_previous_index = $distance_from_previous_index;
		$this->bus_station = $bus_station;	
	}
}
