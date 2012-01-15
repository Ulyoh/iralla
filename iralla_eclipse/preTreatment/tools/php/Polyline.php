<?php

class Polyline{
	private $points_array;
	private $length;
	
	public function get_points(){
		return $this->points_array;
	}
	
	public function __construct(array $points_array) {
		$this->points_array = $points_array;
		$this->length = count($points_array);
	}
}