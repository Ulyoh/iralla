<?php

class Segments{
	private $pt1;
	private $pt2;
	
	public function get_pt1(){
		return $this->pt1;
	}
	
	public function get_pt2(){
		return $this->pt2;
	}
	/*
	public function set_pt1($pt1){
		if ($pt1 instanceof Point){
			$this->pt1 = $pt1;
		}
		else{
			throw new Exception('$pt1 is not a Point Object');
		}
	}

	public function set_pt2($pt2){
		if ($pt2 instanceof Point){
			$this->pt2 = $pt2;
		}
		else{
			throw new Exception('$pt2 is not a Point Object');
		}
	}
	*/
	public function find_intersection_with($other_segment){
		return Point::segment_intersection(
		$this->pt1, $this->pt2, $other_segment->pt1, $other_segment->pt2);
	}
	
	public function __construct($pt1, $pt2){
		if(($pt1 instanceof Point) && ($pt2 instanceof Point)){
			$this->pt1 = $pt1;
			$this->pt2 = $pt2;
		}
		else{
			throw new Exception('$pt1 or $pt2 is not a Point Object');
		}
	}
}


//tests
/*
include_once 'Point.php';

$pt1 = new Point(5.9, 6.3);
$pt2 = new Point(6, 9.2);

$segment1 = new Segments($pt1, $pt2);
echo 'segment1 : ';
print_r($segment1);
echo "\n";

$pt3 = "a string";
try {
	$segment2 = new Segments($pt1, $pt3);
	echo 'segment2 : ';
	print_r($segment2);
	echo "\n";
} catch (Exception $e) {
	echo 'exception message : '.$e->getMessage();
}
*/