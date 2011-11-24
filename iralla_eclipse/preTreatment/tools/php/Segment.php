<?php
include_once 'Point.php';

class Segment {
	private $pt1;
	private $pt2;
	private $left_pt;
	private $right_pt;
	
	public function get_pt1() {
		return $this->pt1;
	}
	
	public function get_pt2() {
		return $this->pt2;
	}
	
	public function get_left_pt() {
		return $this->left_pt;
	}
	
	public function get_right_pt() {
		return $this->right_pt;
	}
	
	public function get_pts_as_array() {
		return array ($this->left_pt, $this->right_pt );
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
	public function find_intersection_with($other_segment, $return_value_if_merged = false) {
		return Point::segment_intersection ( $this->pt1, $this->pt2, $other_segment->pt1, $other_segment->pt2, $return_value_if_merged );
	}
	
	public function __construct($pt1_or_x1, $pt2_or_y1, $x2 = null, $y2 = null) {
		if ($x2 == null && $y2 == null) {
			$pt1 = $pt1_or_x1;
			$pt2 = $pt2_or_y1;
			if ($pt1 == $pt2) {
				die ( "null segment" );
			}
			if (($pt1 instanceof Point) && ($pt2 instanceof Point)) {
				$pt1 = clone $pt1;
				$pt2 = clone $pt2;
			}
			else {
				throw new Exception ( '$pt1 or $pt2 is not a Point Object' );
			}
		}
		else if (! is_numeric ( $pt1_or_x1 ) || ! is_numeric ( $pt2_or_y1 ) || ! is_numeric ( $x2 ) ||
				 ! is_numeric ( $y2 )) {
			throw new Exception ( 'one entry is not numeric' );
		}
		else {
			$pt1 = new Point ( $pt1_or_x1, $pt2_or_y1 );
			$pt2 = new Point ( $x2, $y2 );
		}
		if (($pt1->x < $pt2->x) || (($pt1->x == $pt2->x) && ($pt1->y < $pt2->y))) {
			$pt1->position = "left";
			$pt2->position = "right";
			$this->left_pt = $pt1;
			$this->right_pt = $pt2;
		}
		else {
		$pt2->position = "left";
		$pt1->position = "right";
			$this->left_pt = $pt2;
			$this->right_pt = $pt1;
		}
		$this->pt1 = $pt1;
		$this->pt2 = $pt2;
		$pt1->segment = $this;
		$pt2->segment = $this;
	}
}
	
		

