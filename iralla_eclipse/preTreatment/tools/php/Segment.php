<?php
include_once 'Point.php';

class Segment{
	private $pt1;
	private $pt2;
	private $left_pt;
	private $right_pt;
	
	public function get_pt1(){
		return $this->pt1;
	}
	
	public function get_pt2(){
		return $this->pt2;
	}
	
	public function get_left_pt(){
		return $this->$left_pt;
	}
	
	public function get_right_pt(){
		return $this->$right_pt;
	}
	
	public function pts_as_array(){
		return array($this->$left_pt, $this->$right_pt);
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
			$pt1 = clone $pt1;
			$pt2 = clone $pt2;
			//left and right point:
			if(($pt1->x < $pt2->x) || (($pt1->x == $pt2->x) && ($pt1->y > $pt2->y))){
				$pt1->position = "left";
				$pt2->position = "right";
				$this->left_pt = $pt1;
				$this->right_pt = $pt2;
			}
			else{
				$pt2->position = "left";
				$pt1->position = "right";
				$this->left_pt = $pt2;
				$this->right_pt = $pt1;
			}
			$this->pt1 = $pt1;
			$this->pt2 = $pt2;
		}
		else{
			throw new Exception('$pt1 or $pt2 is not a Point Object');
		}
	}
}

$pt1 = new Point(5.9, 6.3);
$pt2 = new Point(6, 9.2);
$seg = new Segment($pt1, $pt2);

$pt1->x = 20000;
echo $seg->get_pt1()->x;
