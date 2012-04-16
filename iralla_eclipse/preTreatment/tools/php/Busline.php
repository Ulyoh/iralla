<?php
require_once 'Polyline.php';
class Busline extends Polyline{
	private $name;
	
	public function get_name(){
		return $this->name;
	}
	
	public function __construct(array $points_array, $name, bool $closed = null) {
		if ($closed == null) {
			$closed = false;
		}
        parent::__construct($points_array, $closed); 
    	$this->name = $name;
	}
	
}