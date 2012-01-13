<?php
include_once 'Geometry.php';
include_once 'Segment.php';
include_once 'Vector.php';

class Point {
	private $x;
	private $y;
	/*
	public function set_x($x) {
		$this->x = $x;
	}
	
	public function set_y($y) {
		$this->y = $y;
	}
	*/
	public function get_x() {
		return $this->x;
	}
	
	public function get_y() {
		return $this->y;
	}
	
	public function same_coord_as(Point $other_pt){
		if(($this->x == $other_pt->x) && ($this->y == $other_pt->y)){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function distance_to(Point $other_pt){
		return sqrt(pow($other_pt->x - $this->x, 2) + pow($other_pt->y - $this->y, 2));
	}
	/**
	 * 
	 * Return the intersection point between two segments if exists
	 * else return false.
	 * 
	 * If the two segements are merged and
	 * $return_value_if_merged == true
	 * will return the array with the extremities points which are parts
	 * of the two segments:
	 * array("merged", array(1=>pta, 2=>ptb, 4=>ptd))
	 * with key depending of the point:
	 * 1:pt1, 2:pt2, 3:pt3 and 4:pt4 
	 * 
	 * @param Point		$pt1
	 * @param Point		$pt2
	 * @param Point		$pt3
	 * @param Point		$pt4
	 * @param bool		$return_value_if_merged
	 * 
	 */
	public static function segment_intersection(Point $pt1, Point $pt2, Point $pt3, Point $pt4, $return_value_if_merged = false) {
		
		//throw exception if they are not segments:
		if (($pt1 == $pt2) || ($pt3 == $pt4)) {
			throw new Exception ( "At least one of the segment is a point" );
		}
		
		//test if segments are colinears:
		$v1 = new Vector ( $pt1, $pt2 );
		$v2 = new Vector ( $pt3, $pt4 );
		
		$det = $v1->get_x () * $v2->get_y () - $v1->get_y () * $v2->get_x ();
		
		if ($det == 0) {
			//vectors v1 and v2 are colinears
			if ($return_value_if_merged == true) {
				return Point::intersection_point_of_colinears_segments ( $pt1, $pt2, $pt3, $pt4 );
			}
			$result [0] = 'colinear';
			$result [1] = null;
			return $result;
		}
		
		//test if segments intersects
		$a = $pt3->x - $pt1->x;
		$b = $pt3->y - $pt1->y;
		
		$t = ($a * $v2->get_y () - $b * $v2->get_x ()) / $det;
		$s = ($a * $v1->get_y () - $b * $v1->get_x ()) / $det;
		
		if ($t < 0 || $t > 1 || $s < 0 || $s > 1) {
			//segments don't intersect
			$result [0] = false;
			$result [1] = null;
			return $result;
		}
		
		$result [0] = true;
		$x = $pt1->x + $v1->get_x () * $t;
		$y = $pt1->y + $v1->get_y () * $t;
		$result [1] = new Point ($x, $y);
		
		return $result;
	}
	
	public function isPartOfSegment($var1, $var2 = null) {
		
		if ($var1 instanceof Segment) {
			if ($var2 == null) {
				$var2 = $var1->get_pt2 ();
				$var1 = $var1->get_pt1 ();
			}
			else {
				throw new Exception ( "ERROR of parameters 2\n" );
			}
		}
		
		if (! ($var1 instanceof Point) || ! ($var2 instanceof Point)) {
			throw new Exception ( "ERROR of parameters\n" );
		}
		
		if ($var1 == $var2) {
			throw new Exception ( "The segment is a point\n" );
		}
		
		if (($this == $var1) || ($this == $var2)) {
			return true;
		}
		
		//are the 3 points aligned:
		if ($this->isAlignedWith ( $var1, $var2 ) === true) {
			//is the point part of the segment :
			if (((($var1->x <= $this->x) && ($this->x <= $var2->x)) || (($var2->x <= $this->x) && ($this->x <= $var1->x))) && ((($var1->y <= $this->y) && ($this->y <= $var2->y)) || (($var2->y <= $this->y) && ($this->y <= $var1->y)))) {
				return true;
			}
		}
		return false;
	}
	
	public function isAlignedWith($pt1, $pt2) {
		$scale = Geometry::bcscale_max ( array ($this->x, $this->y, $pt1->x, $pt1->y, $pt2->x, $pt2->y ) ) + 1;
		//cross product = 0
		if (abs ( bcmul ( ($pt2->x - $this->x), ($pt1->y - $this->y), $scale ) - bcmul ( ($pt1->x - $this->x), ($pt2->y - $this->y), $scale ) ) <= (bcpow ( 10, - $scale, $scale ))) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * return the distance and the coordinates of the point projection
	 * on a segement as :
	 * array('distance' => float $value, 'to' => Point $value)
	 * 
	 */
	public function projection_on_segment(Segment $seg){
		if(($x1 == $x2) && ($y1 == $y2)){
			return $this->distance_to($seg->get_pt1());
		}
		else{
			$Dx = $x2 - $x1;
			$Dy = $y2 - $y1;
			$ratio = (($this->x - $x1) * $Dx + ($this->y - $y1) * $Dy) / ($Dx * $Dx + $Dy * $Dy);
		    if ($ratio < 0){
		    	return  array(
		    		'distance'=> $this->distance_to($seg->get_pt1()),
		    		'to'=>$seg->get_pt1()
		    	);
		    }
		    else if ($ratio > 1){
		    	return  array(
		    		'distance'=> $this->distance_to($seg->get_pt2()),
		    		'to'=>$seg->get_pt2()
		    	);
		    }
		    else{
				$x1 = $seg->get_pt1()->x;
				$y1 = $seg->get_pt1()->y;
				$x2 = $seg->get_pt2()->x;
				$y2 = $seg->get_pt2()->y;
				$pt = Point((1 - $ratio) * x1 + $ratio * x2,
		       				(1 - $ratio) * y1 + $ratio * y2);
		       	return  array(
		    		'distance'=> $this->distance_to($pt),
		    		'to'=>$pt
		    	);
		    }
		}
	}
	
	/**
	 * return the distance and the coordinates of the point projection
	 * on a polyline as :
	 * array('distance' => float $value, 'to' => Point $value)
	 * 
	 */
	public function projection_on_polyline(Polyline $p){
		$pts = $p->get_points();
		$result = array('distance' => INF);
		
		for($i = 1; $i < $p->length; $i++){
			$seg_result = $this->projection_on_segment(Segment($pts[$i - 1], $$pts[$i]));
			if($seg_result['distance'] < $result['distance']){
				$result = $seg_result;
				$result['index'] = $i - 1;
			}
		}
		return $result;
	}
	
	private static function intersection_point_of_colinears_segments(Point $pt1, Point $pt2, Point $pt3, Point $pt4) {
		
		//throw exception if they are not segments:
		if (($pt1 == $pt2) || ($pt3 == $pt4)) {
			throw new Exception ( "At least one of the segment is a point" );
		}
		
		//if deux egal points:
		if ((($pt1 == $pt3) && ($pt2 == $pt4)) || (($pt1 == $pt3) && ($pt2 == $pt4))) {
			return array ("same", array (1 => $pt1, 2 => $pt2 ) );
		}
		
		//if the segments are not on the same line:
		if (($pt1->isAlignedWith ( $pt3, $pt4 ) == false) || ($pt2->isAlignedWith ( $pt3, $pt4 ) == false)) {
			return array (false, null );
		}
		
		//if segments are verticals:
		if ($pt1->x == $pt2->x) {
			//if they are not on the same line:
			if ($pt1->x != $pt3->x) {
				return array (false, null );
			}
			
			//test if they have a common part:
			$y_list_of_points = array (array ("line A", $pt1, 1 ), array ("line A", $pt2, 2 ), array ("line B", $pt3, 3 ), array ("line B", $pt4, 4 ) );
			
			usort ( $y_list_of_points, "Point::cmp_y" );
			
			//if they do not intersect:
			if (($y_list_of_points [0] [0]) == ($y_list_of_points [1] [0])) {
				return array (false, null );
			}
			
			//return the 2 points which are in the middle of $y_list_of_points
			$pts_to_return = array ($y_list_of_points [1] [1] => $y_list_of_points [1] [1], $y_list_of_points [2] [2] => $y_list_of_points [2] [1] );
			return array ("merged", $pts_to_return );
		}
		
		//test if they have a common part:
		$x_list_of_points = array (array ("line A", $pt1, 1 ), array ("line A", $pt2, 2 ), array ("line B", $pt3, 3 ), array ("line B", $pt4, 4 ) );
		
		usort ( $x_list_of_points, "Point::cmp_x" );
		
		//if they do not intersect:
		if (($x_list_of_points [0] [0]) == ($x_list_of_points [1] [0])) {
			return array (false, null );
		}
		
		//if they have a common part:
		//return the 2 points which are in the middle of $y_list_of_points
		$pts_to_return = array ($x_list_of_points [1] [2] => $x_list_of_points [1] [1], $x_list_of_points [2] [2] => $x_list_of_points [2] [1] );
		return array ("merged", $pts_to_return );
	
	}
	
	private static function cmp_x($a, $b) {
		if ($a [1]->x == $b [1]->x) {
			throw new Exception ( "Error: two identicals points\n" );
		}
		return ($a [1]->x < $b [1]->x) ? - 1 : 1;
	}
	
	private static function cmp_y($a, $b) {
		if ($a [1]->y == $b [1]->y) {
			throw new Exception ( "Error: two identicals points\n" );
		}
		return ($a [1]->y < $b [1]->y) ? - 1 : 1;
	}
	
	public function __construct($x, $y) {
		if (is_numeric ( $x ) && is_numeric ( $y )) {
			$this->x = $x;
			$this->y = $y;
		}
		else {
			throw new Exception ( 'one or more parameters not valid' );
		}
		$this->x = $x;
		$this->y = $y;
	}

}