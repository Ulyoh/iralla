<?php
include_once 'Geometry.php';
include_once 'Segment.php';
include_once 'Vector.php';

class Point {
	public $x;
	public $y;
	
	public function set_x($x) {
		$this->x = $x;
	}
	
	public function set_y($y) {
		$this->y = $y;
	}
	
	public function get_x() {
		return $this->x;
	}
	
	public function get_y() {
		return $this->y;
	}
	
	/**
	 * 
	 * Return the intersection point between two segments if exists
	 * else return false.
	 * 
	 * If the two segements are merged and
	 * $return_value_if_merged == true
	 * will return the array:
	 * array("merged", array(pta, ptb))
	 * pta and ptb are the 2 points whitch are merged with the other 
	 * segment
	 * 
	 * @param Point		$pt1
	 * @param Point		$pt2
	 * @param Point		$pt3
	 * @param Point		$pt4
	 * @param bool		$return_value_if_merged
	 * 
	 */
	public static function segment_intersection(Point $pt1, Point $pt2, Point $pt3, Point $pt4, $return_value_if_merged = false) {
		
		//test if communs points between the segments:
		if ($pt1 == $pt3) {
			if ($pt2->isPartOfSegment ( $pt3, $pt4 )) {
				return array ("merged", array ($pt1, $pt2, $pt3 ) );
			} elseif ($pt4->isPartOfSegment ( $pt1, $pt2 )) {
				return array ("merged", array ($pt1, $pt3, $pt4 ) );
			}
		} elseif ($pt1 == $pt4) {
			
			if ($pt2->isPartOfSegment ( $pt3, $pt4 )) {
				return array ("merged", array ($pt1, $pt2, $pt4 ) );
			} elseif ($pt3->isPartOfSegment ( $pt1, $pt2 )) {
				return array ("merged", array ($pt1, $pt3, $pt4 ) );
			}
		
		} elseif ($pt2 == $pt3) {
			if ($pt1->isPartOfSegment ( $pt3, $pt4 )) {
				return array ("merged", array ($pt1, $pt2, $pt3 ) );
			} elseif ($pt4->isPartOfSegment ( $pt1, $pt2 )) {
				return array ("merged", array ($pt2, $pt3, $pt4 ) );
			}
		} elseif ($pt2 == $pt4) {
			if ($pt1->isPartOfSegment ( $pt3, $pt4 )) {
				return array ("merged", array ($pt1, $pt2, $pt4 ) );
			} elseif ($pt3->isPartOfSegment ( $pt1, $pt2 )) {
				return array ("merged", array ($pt2, $pt3, $pt4 ) );
			}
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
		$result [1] = new Point ();
		$result [1]->x = $pt1->x + $v1->get_x () * $t;
		$result [1]->y = $pt1->y + $v1->get_y () * $t;
		
		return $result;
	}
	
	public function isPartOfSegment($var1, $var2 = null, $scale = null) {
		
		if ($var1 instanceof Segment) {
			if ($var2 == null) {
				$var2 = $var1->get_pt2 ();
				$var1 = $var1->get_pt1 ();
			} else {
				throw new Exception ( "ERROR of parameters 2\n" );
			}
		}
		
		if (! ($var1 instanceof Point) || ! ($var2 instanceof Point)) {
			throw new Exception ( "ERROR of parameters\n" );
		}
		
		if(($this == $var1) || ($this == $var2)){
			return true;
		}
				
		if ($var1 == $var2){
			throw new Exception ( "The segment is a point\n" );
		}
		
		if ($scale === null) {
			$scale = Geometry::bcscale_value();
		}
		
		//are the 3 points aligned:
		//cross product = 0
		if (abs ( bcmul ( ($var2->x - $this->x), ($var1->y - $this->y), $scale) - bcmul ( ($var1->x - $this->x), ($var2->y - $this->y), $scale ) ) <= (bcpow ( 10, - $scale, $scale))) {
			//is the point part of the segment :
			if ( ( ( ($var1->x <= $this->x)  && ($this->x <= $var2->x) ) || ( ($var2->x <= $this->x)  && ($this->x <= $var1->x) ) )
			  && ( ( ($var1->y <= $this->y)  && ($this->y <= $var2->y) ) || ( ($var2->y <= $this->y)  && ($this->y <= $var1->y) ) ) ){
				return true;
			}
		}
		return false;
	}
	
	private static function intersection_point_of_colinears_segments(Point $pt1, Point $pt2, Point $pt3, Point $pt4) {
		
		$result [1] = null;
		//if one of the segments is a point:
		if ($pt1 == $pt2) {
			if($pt3 == $pt4){
				return array(false,null);
			}
			$result [0] = $pt1->isPartOfSegment ( new Segment ( $pt3, $pt4 ) );
			if ($result [0] == true) {
				$result [1] = $pt1;
			}
			return $result;
		} elseif ($pt3 == $pt4) {
			$result [0] = $pt3->isPartOfSegment ( new Segment ( $pt1, $pt2 ) );
			if ($result [0] == true) {
				$result [1] = $pt3;
			}
			return $result;
		}
		//if segments are verticals:
		if ($pt1->x == $pt2->x) {
			//if they are not on the same line:
			if ($pt1->x != $pt3->x) {
				return array (false, null );
			}
			
			//test if they have a common part:
			$y_list_of_points = array (array ("line A", $pt1 ), array ("line A", $pt2 ), array ("line B", $pt3 ), array ("line B", $pt4 ) );
			
			usort ( $y_list_of_points, "Point::cmp_y" );
			
			//if they do not intersect:
			if (($y_list_of_points [0] [0]) == ($y_list_of_points [1] [0])) {
				return array (false, null );
			}
			
			//return the 2 points which are in the middle of $y_list_of_points
			$pts_to_return = array ($y_list_of_points [1] [1], $y_list_of_points [2] [1] );
			return array ("merged", $pts_to_return );
		}
		
		//if the segments are not on the same line:
		$y_intercept_1 = Geometry::y_intercept_of_line_passing_by ( $pt1, $pt2 );
		$y_intercept_2 = Geometry::y_intercept_of_line_passing_by ( $pt3, $pt4 );
		
		if ($y_intercept_1 != $y_intercept_2) {
			return array (false, null );
		}
		
		//test if they have a common part:
		$x_list_of_points = array (array ("line A", $pt1 ), array ("line A", $pt2 ), array ("line B", $pt3 ), array ("line B", $pt4 ) );
		
		usort ( $x_list_of_points, "Point::cmp_x" );
		
		//if they do not intersect:
		if (($x_list_of_points [0] [0]) == ($x_list_of_points [1] [0])) {
			return array (false, null );
		}
		
		//if they have a common part:
		//return the 2 points which are in the middle of $y_list_of_points
		$pts_to_return = array ($x_list_of_points [1] [1], $x_list_of_points [2] [1] );
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
		if( is_numeric($x) && is_numeric($y) ){
			$this->x = $x;
			$this->y = $y;
		}
		else{
			throw new Exception('one or more parameters not valid');
		}
		$this->x = $x;
		$this->y = $y;
	}
	
}
