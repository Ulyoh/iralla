<?php

class Polyline{
	private $points_array;
	private $length;
	private $closed;
	
	public function get_points(){
		return $this->points_array;
	}
	
	public function __construct(array $points_array, bool $closed = false) {
		$this->points_array = $points_array;
		$this->length = count($points_array);
    	$this->closed = $closed;
	}
}