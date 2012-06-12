<?php
require_once 'Point.php';

class Polyline{
	private $points_array;
	private $length;
	private $closed;
	
	public function get_points(){
		return $this->points_array;
	}
	
	public function get_length(){
		return $this->length;
	}
	
	public function get_point_at($index){
		if(!is_int($index)){
			exit("Point->get_point_at() -> Argument must be integer");
		}
		if($index < $this->length){
			return $this->points_array[$index];
		}
		else{
			return null;
		}
	}
	
	/**
	 * 
	 * @param int $first_index
	 * @param int $last_index
	 * @return array / false
	 */
	public function get_points_between($first_index, $last_index){
		
		if(!is_int($first_index) || (!is_int($last_index))){
			exit("Point->get_points_between() -> Arguments 1 and 2 must be integer");
		}
		
		if($first_index <= $last_index){
			return array_slice($this->points_array, $first_index, $last_index - $first_index + 1);
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
	
	public function point_projected_on_polyline_between(Point $pt, $first_index, $last_index){
		if(!is_int($first_index) || (!is_int($last_index))){
			exit("Point->point_projected_on_polyline_between() -> Arguments 1 and 2 must be integer");
		}
		return $pt->projection_on_polyline_between($this, $first_index, $last_index);
	}
	
	public function point_projected_on_polyline_between_on_earth(Point $pt, $first_index, $last_index){
		if(!is_int($first_index) || (!is_int($last_index))){
			exit("Point->point_projected_on_polyline_between_on_earth() -> Arguments 2 and 3 must be integer");
		}
		return $pt->projection_on_polyline_between_on_earth($this, $first_index, $last_index);
	}
	
	public function __construct(array $points_array,/*bool*/ $closed = null) {
		if ($closed == null) {
			$closed = false;
		}
		$this->points_array = $points_array;
		$this->length = count($points_array);
    	$this->closed = $closed;
	}
}