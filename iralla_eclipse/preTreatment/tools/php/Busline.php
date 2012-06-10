<?php
require_once 'Polyline.php';
class Busline extends Polyline{
	private $name;
	
	public function get_name(){
		return $this->name;
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
	
	public function __construct(array $points_array, $name, bool $closed = null) {
		if ($closed == null) {
			$closed = false;
		}
        parent::__construct($points_array, $closed); 
    	$this->name = $name;
	}
	
}