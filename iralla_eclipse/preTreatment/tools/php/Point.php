<?php
include 'Segment.php';
include 'Vector.php';

class Point{
	public $x;
	public $y;
	
	public function set_x(){
		return $this->x;
	}
	
	public function set_y(){
		return $this->y;
	}
	
	public function get_x(){
		return $this->x;
	}
	
	public function get_y(){
		return $this->y;
	}
	
	public static function segment_intersection($pt1, $pt2, $pt3, $pt4, 
	$return_value_if_colinear = false, $suggest_point_if_colinear = null){
	
		//test if they are four points:
		if(!($pt1 instanceof Point) || !($pt2 instanceof Point) 
		|| !($pt3 instanceof Point) || !($pt4 instanceof Point)){
			$result[0] = false;
			$result[1] = null;
			return  $result;
		}
		
		
		$v1 = new Vector($pt1, $pt2);
		$v2 = new Vector($pt3, $pt4);
		
		$det = $v1->get_x() * $v2->get_y() - $v1->get_y() * $v2->get_x();
		
		if($det == 0){
			//vectors are colinear
			if($return_value_if_colinear == true){
				return Point::intersection_point_of_colinears_segments($pt1, $pt2, $pt3, $pt4);
			}
			$result[0] = 'colinear';
			$result[1] = null;
			return $result;
		}
		
		$a = $pt3->x - $pt1->x;
		$b = $pt3->y - $pt1->y;
		
		$t = ($a * $v2->get_y() - $b * $v2->get_x()) / $det;
		$s = ($a * $v1->get_y() - $b * $v1->get_x()) / $det;
	
	    if ($t < 0 || $t > 1 || $s < 0 || $s > 1)
	    {
	        //segments don't intersect
	        $result[0] = false;
			$result[1] = null;
			return $result;
	    }

	    $result[0] = true;
	    $result[1] = new Point();
	    $result[1]->x = $pt1->x + $v1->get_x() * $t;
	    $result[1]->y = $pt1->y + $v1->get_y() * $t;
	    
	    return $result;
	}
	
	private static function intersection_point_of_colinears_segments($pt1, $pt2, $pt3, $pt4){
		
	}
	
	public function is_egal_to($other_point){
		if(($this->x == $other_point->x) && ($this->y == $other_point->y)) {
			return true;
		}
		else{
			return false;
		}
	}
	
	public function __construct($x = 0, $y = 0){
		$this->x = $x;
		$this->y = $y;
	}
}


//tests:
//test of intersection:

echo"*****************************************\n";
echo "test of 2 intersect segments: \n";
echo "\t result should be (0.5,1): \n";
$p1 = new Point(0,0);
$p2 = new Point(1,2);
$p3 = new Point(0,2);
$p4 = new Point(1,0);
var_dump(Point::segment_intersection($p1, $p2, $p3, $p4));
echo "\n";
echo"*****************************************\n";
echo "\n";

echo"*****************************************\n";
echo "test of 2 parallels segments: \n";
echo "\t horizontals: \n";
$p1 = new Point(0,0);
$p2 = new Point(5,0);
$p3 = new Point(0,1);
$p4 = new Point(5,1);
var_dump(Point::segment_intersection($p1, $p2, $p3, $p4));
echo "\n";
echo "\n";

echo "\t verticals: \n";
$p1 = new Point(0,5);
$p2 = new Point(0,0);
$p3 = new Point(1,5);
$p4 = new Point(1,0);
var_dump(Point::segment_intersection($p1, $p2, $p3, $p4));
echo "\n";
echo "\n";

echo "\t other type: \n";
$p1 = new Point(0,0);
$p2 = new Point(1,2);
$p3 = new Point(1,0);
$p4 = new Point(2,4);
var_dump(Point::segment_intersection($p1, $p2, $p3, $p4));
echo "\n";
echo"*****************************************\n";
echo "\n";

echo"*****************************************\n";
echo "test with one commun point: \n";
echo "\t result should be (0,0): \n";
$p1 = new Point(0,0);
$p2 = new Point(1,2);
$p3 = new Point(0,0);
$p4 = new Point(1,0);
var_dump(Point::segment_intersection($p1, $p2, $p3, $p4));
echo "\n";
echo "\n";

echo "test of 2 parallels segments with one point en commun: \n";
echo "\t result should be (0,0): \n";
$p1 = new Point(0,0);
$p2 = new Point(1,2);
$p3 = new Point(0,0);
$p4 = new Point(2,4);
var_dump(Point::segment_intersection($p1, $p2, $p3, $p4));
echo "\n";
echo "\n";

echo "test of 2 verticals segments with one point en commun: \n";
echo "\t result should be (0,0): \n";
$p1 = new Point(0,0);
$p2 = new Point(0,1);
$p3 = new Point(0,0);
$p4 = new Point(0,5);
var_dump(Point::segment_intersection($p1, $p2, $p3, $p4));
echo "\n";
echo "\n";

echo "test of 2 horizontal segments with one point en commun: \n";
echo "\t result should be colinear: \n";
$p1 = new Point(0,0);
$p2 = new Point(5,0);
$p3 = new Point(0,0);
$p4 = new Point(6,0);
var_dump(Point::segment_intersection($p1, $p2, $p3, $p4));
echo "\n";
echo"*****************************************\n";
echo "\n";

echo"*****************************************\n";
echo "test of 2 colinear segments: \n";
echo "\t identicals: \n";
$p1 = new Point(0,0);
$p2 = new Point(5,0);
$p3 = new Point(0,0);
$p4 = new Point(5,0);
var_dump(Point::segment_intersection($p1, $p2, $p3, $p4));
echo "\n";
echo "\n";

echo "\t verticals: \n";
$p1 = new Point(0,5);
$p2 = new Point(0,0);
$p3 = new Point(0,1);
$p4 = new Point(0,2);
var_dump(Point::segment_intersection($p1, $p2, $p3, $p4));
echo "\n";
echo "\n";

echo "\t other type: \n";
$p1 = new Point(0,0);
$p2 = new Point(1,2);
$p3 = new Point(0,0);
$p4 = new Point(2,4);
var_dump(Point::segment_intersection($p1, $p2, $p3, $p4));
echo "\n";
echo "\n";;
