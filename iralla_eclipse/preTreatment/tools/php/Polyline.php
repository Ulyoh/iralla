<?php

class Polyline{
	private $points_array;
	private $length;
	private $closed;
	
	public function get_points(){
		return $this->points_array;
	}
	
	public function point_projected(Point $pt){
		$pt->projection_on_polyline($this);
	}
	
	public function __construct(array $points_array, bool $closed = false) {
		$this->points_array = $points_array;
		$this->length = count($points_array);
    	$this->closed = $closed;
	}
}