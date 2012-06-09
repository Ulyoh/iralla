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
	
	public function __construct(array $points_array, $name, bool $closed = null) {
		if ($closed == null) {
			$closed = false;
		}
        parent::__construct($points_array, $closed); 
    	$this->name = $name;
	}
	
}