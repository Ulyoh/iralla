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
	
	public function is_egal_to($other_vector){
		if(($this->x == $other_vector->x) && ($this->y == $other_vector->y)) {
			return true;
		}
		else{
			return false;
		}
	}
	
	public function __construct($var_1, $var_2 = false,  $scale = null){
		
		if ($scale === null) {
			$scale = Geometry::bcscale_value();
		}
		
		$n = get_class($var_1);
		
		switch ($n){
			case 'Point':
				if (!($var_2 instanceof Point)){
					throw new Exception('ERROR when creating a vector');
				}
				$this->x = bcsub($var_1->get_x(), $var_2->get_x(), $scale);
				$this->y = bcsub($var_1->get_y(), $var_2->get_y(), $scale);
			break;
			
			case 'Segment':
				if ($var_2 !== false){
					throw new Exception('ERROR when creating a vector');
				}
				
				$pt1 = $var_1->get_pt1();
				$pt2 = $var_1->get_pt2();
				
				$this->x = bcsub($pt1->get_x(), $pt2->get_x(), $scale);
				$this->y = bcsub($pt1->get_y(), $pt2->get_y(), $scale);
			break;
			
			default:
				throw new Exception('ERROR when creating a vector');
		}
	}
}


//tests
/*
include_once 'Point.php';
include_once 'Segment.php';

$pt1 = new Point(5.9, 6.3);
$pt2 = new Point(6, 9.2);

$vector1 = new Vector(2, $pt1, $pt2);
echo 'vector1 : ';
print_r($vector1);
echo "\n";

$pt2 = 3;

try {
	$vector2 = new Vector(2, $pt1, $pt2);
	echo 'ERROR : vector2 created'."\n";
}
catch (Exception $e) {
	echo 'exception message : '.$e->getMessage();
	echo "\n";
}

$pt2 = new Point(6, 9.2);
$segment1 = new Segments($pt1, $pt2);
$vector3 = new Vector(2, $segment1);
echo 'vector3 : ';
print_r($vector3);
echo "\n";
*/



