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
	 */
	public static function y_intercept_of_line_passing_by(Point $pt1, Point $pt2){
		$scale = Geometry::bcscale_max(array($pt1->x, $pt1->y, $pt2->x, $pt2->y)) + 2;

		if($pt1->x == $pt2->x){
			return false;
		}
		return bcsub($pt1->y, bcmul(bcdiv(bcsub($pt2->y, $pt1->y, $scale), bcsub($pt2->x, $pt1->x, $scale), $scale), $pt1->x, $scale), $scale);

	}
	
	public static function bcscale_value(){
		$string_length = strlen ( bcdiv ( 1, 3 ) );
		return ($string_length == 1) ? 0 : $string_length - 2;
	}
	
	public static function bcscale_max(array $array_of_values){
		$scale_max = 0;
		foreach ($array_of_values as $value) {
			$tab = explode('.', (string)$value);
			$scale = isset($tab[1]) ? strlen($tab[1]): 0;
			if ($scale > $scale_max ){
				$scale_max = strlen($tab[1]);
			}
		}
		return $scale_max;
	}
}

