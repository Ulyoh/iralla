<?php
include_once 'Geometry.php';
include_once 'Segment.php';

class Vector{
	private $x;
	private $y;
	
	public function get_x(){
		return $this->x;
	}
	
	public function get_y(){
		return $this->y;
	}
	
	public function __construct($var_1, $var_2 = false){
		
		$n = get_class($var_1);
		
		switch ($n){
			case 'Point':
				if (!($var_2 instanceof Point)){
					throw new Exception('ERROR when creating a vector');
				}
				$this->x = $var_2->get_x()- $var_1->get_x();
				$this->y = $var_2->get_y()- $var_1->get_y();
			break;
			
			case 'Segment':
				if ($var_2 !== false){
					throw new Exception('ERROR when creating a vector');
				}
				
				$pt1 = $var_1->get_pt1();
				$pt2 = $var_1->get_pt2();
				
				$this->x = $pt2->get_x()- $pt1->get_x();
				$this->y = $pt2->get_y()- $pt1->get_y();
			break;
			
			default:
				throw new Exception('ERROR when creating a vector');
		}
	}
}



