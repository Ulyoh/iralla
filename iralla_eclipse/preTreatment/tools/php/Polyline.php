<?php

class Polyline{
	private $points_array;
	private $length;
	private $closed;
	
	public function get_points(){
		return $this->points_array;
	}
	
	/**
	 * 
	 * @param int $first_index
	 * @param int $last_index
	 * @return array / false
	 */
	public function get_points_between(int $first_index,int $last_index){
		if($first_index <= $last_index){
			return array_slice($this->points_array, $first_index, $last_index-$first_index);
		}
		else if ($this->closed === true){
			return array_merge(array_slice($this->points_array, $last_index), array_slice($this->points_array, 0, $first_index + 1));
		}
		else{
			return false;
		}
	}
	
	public function point_projected(Point $pt){
		return $pt->projection_on_polyline($this);
	}
	
	public function point_projected_on_polyline_between(Point $pt, int $first_index, int $last_index){
		return $pt->projection_on_polyline_between($this, $first_index, $last_index);
	}
	
	public function __construct(array $points_array, bool $closed = false) {
		$this->points_array = $points_array;
		$this->length = count($points_array);
    	$this->closed = $closed;
	}
}