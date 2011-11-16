<?php
global $scale_geometry;
$scale_geometry = 0;

class Geometry{
	/**
	 * 
	 * return the y-intercept of a line passing by the point $pt1 and $pt2
	 * return false if the line is vertical
	 * 
	 * @param Point $pt1
	 * @param Point $pt2
	 * @param integer $scale
	 */
	public static function y_intercept_of_line_passing_by(Point $pt1, Point $pt2, $scale = null){
		if(($scale != null) && !(is_int($scale))){
			throw new Exception("ERROR : the scale is not an integer");
		}
		if($pt1->x == $pt2->x){
			return false;
		}
		if($scale){
			return bcsub($pt1->y, bcmul(bcdiv(bcsub($pt2->y, $pt1->y, $scale), bcsub($pt2->x, $pt1->x, $scale), $scale), $pt1->x, $scale), $scale);
		}
		else{
			return bcsub($pt1->y, bcmul(bcdiv(bcsub($pt2->y, $pt1->y), bcsub($pt2->x, $pt1->x)), $pt1->x));
		}
	}
}
/*
//test:
include_once 'Point.php';

echo "simple test without scale parameter: \n";
$pt1 = new Point(0,2.5);
$pt2 = new Point(4,1);
echo "should give 2 has result:\n";
echo Geometry::y_intercept_of_line_passing_by($pt1, $pt2);
echo "\n";
echo "\n";
echo "********************************************************";

echo "simple test with scale parameter = 2: \n";
$pt1 = new Point(0,2.5);
$pt2 = new Point(4,1);
echo "should give 2.50 has result:\n";
echo Geometry::y_intercept_of_line_passing_by($pt1, $pt2, 2);
echo "\n";
echo "\n";
echo "********************************************************";

echo "simple test with non Point as parameter: \n";
$pt1 = 2;
$pt2 = new Point(4,2);
echo "should give fatal error:\n";
echo Geometry::y_intercept_of_line_passing_by($pt1, $pt2, "test");
echo "\n";
echo "\n";
echo "********************************************************";

echo "simple test with non integer scale parameter: \n";
$pt1 = new Point(-2,-2);
$pt2 = new Point(4,2);
echo "should throw error:\n";
echo Geometry::y_intercept_of_line_passing_by($pt1, $pt2, "test");
echo "\n";
echo "\n";
echo "********************************************************";
*/
