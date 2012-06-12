<?php
require_once 'Polyline.php';
class Busline extends Polyline{
	private $name;
	private $total_distance;
	
	public function get_name(){
		return $this->name;
	}
	
	public function get_total_distance(){
		return $this->total_distance;
	}
	
	public function get_points_between_start_and_end_index($start_index, $end_index){
		$flow = 'normal';
		if(isset($this->flow)){
			$flow = $this->flow;
		}
		
		$results = array();
		
		switch ($flow){
			
			case 'normal':
				$result = $this->get_points_between($start_index, $end_index);
				if ($result != false){
					$results[] = $result;
				}
				break;
				
			case 'reverse':
				$result = $this->get_points_between($end_index, $start_index);
				if ($result != false){
					$results[] = $result;
				}
				break;
				
			case 'both':
				$result = $this->get_points_between($start_index, $end_index);
				if ($result != false){
					$results[] = $result;
				}
				$result = $this->get_points_between($end_index, $start_index);
				if ($result != false){
					$results[] = $result;
				}
				break;
		}
		return $results;
	}
	/**
	 * 
	 * @param Point $start_point
	 * @param Point $end_point
	 * @return array
	 */

	
	public function get_points_between_start_and_end_pts_on_bl(Point $start_point, Point $end_point){
		
		if((isset($start_point->projection_infos) == null)
			|| (is_int($start_point->projection_infos['index']) == null)
			|| (isset($end_point->projection_infos) == null)
			|| (is_int($end_point->projection_infos['index']) == null)){
			
			die("Busline->get_points_between_start_and_end_pts_on_bl parameter do not have correct projection_infos values");
		}
		
		$start_previous_index = $start_point->projection_infos['index'];
		if($start_previous_index == $this->get_length() - 1){
			$start_next_index = $start_previous_index;
		}
		else{
			$start_next_index = $start_previous_index + 1; 
		} 
		
		$end_previous_index = $end_point->projection_infos['index'];
		if($end_previous_index == $this->get_length() - 1){
			$end_next_index = $end_previous_index;
		}
		else{
			$end_next_index = $end_previous_index + 1;
		}
		
		$flow = 'normal';
		if(isset($this->flow)){
			$flow = $this->flow;
		}
	
		$results = array();
	
		switch ($flow){
				
			case 'normal':
				$result = $this->get_points_between($start_next_index, $end_previous_index);
				if ($result != false){
					$results[] = $result;
				}
				break;
	
			case 'reverse':
				$result = $this->get_points_between($end_previous_index, $start_next_index);
				if ($result != false){
					$results[] = $result;
				}
				break;
	
			case 'both':
				if($start_next_index < $end_previous_index){
					$result = $this->get_points_between($start_next_index, $end_previous_index);
					if ($result != false){
						$results[] = $result;
					}
					$result = $this->get_points_between($end_previous_index, $start_next_index);
					if ($result != false){
						$results[] = $result;
					}
				}
				else{
					$result = $this->get_points_between($end_next_index, $start_previous_index);
					if ($result != false){
						$results[] = $result;
					}
					$result = $this->get_points_between($start_previous_index, $end_next_index);
					if ($result != false){
						$results[] = $result;
					}
				}
				break;
		}
		return $results;
	}
	
	//TODO : generate the total distance when creating the buslines database
	private function calculate_total_distance(){
		$previous_point = null;
		$total_distance = 0;
		$points_array = $this->get_points();
		foreach ($points_array as $point) {
			if ($previous_point == null){
				$previous_point = $point;
				continue;
			}
			$total_distance += $point->earth_distance_to($previous_point);
		}
		return $total_distance;
	}
	
	public function calculate_distance_from_first_vertex_to(int $index){
		$previous_point = null;
		$total_distance = 0;
		$points_array = $this->get_points();
		foreach ($points_array as $point) {
			if ($previous_point == null){
				$previous_point = $point;
				continue;
			}
			$total_distance += $point->earth_distance_to($previous_point);
			if( --$index <= 0){
				break;
			}
			$previous_point = $point;
		}
		return $total_distance;
	}
	
	public function calculate_distance_between_2_vertex(int $first_index, int $last_index){
		if($first_index >= $last_index){
			die("arguments not valid");
		}
		if($last_index >=$this->get_length()){
			die("arguments not valid");
		}
		$total_distance = 0;
		$points_array = $this->get_points();
		$previous_point = $points_array[$first_index];
		for($i = $first_index + 1; $i <= $last_index; $i++){
			$point = $points_array[$i];
			$total_distance += $point->earth_distance_to($previous_point);
			$previous_point = $point;
		}
		return $total_distance;
	}
	
	public function __construct(array $points_array, $name, bool $closed = null) {

        parent::__construct($points_array);

        if ($closed == null) {
        	//determine if the polyline is closed or not:
        	//if distance between first and last point is less than 50 m
        	//it is considereted as closed:
        	//$points_array[0]->distance_to(Point)
        	$closed = false;
        }    
        $this->closed = $closed;
    	$this->name = $name;
    	$this->total_distance = $this->calculate_total_distance();
	}
	
}