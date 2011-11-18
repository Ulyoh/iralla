<?php
include_once 'Geometry.php';

//test:
include_once 'Point.php';

echo "simple test 1 \n";
$pt1 = new Point(0,2.5);
$pt2 = new Point(4,1);
echo "should give 2 has result:\n";
echo Geometry::y_intercept_of_line_passing_by($pt1, $pt2);
echo "\n";
echo "\n";
echo "********************************************************";
echo "\n";

echo "simple test 2: \n";
$pt1 = new Point(0,2.5);
$pt2 = new Point(4,1);
echo "should give 2.50 has result:\n";
echo Geometry::y_intercept_of_line_passing_by($pt1, $pt2);
echo "\n";
echo "\n";
echo "********************************************************";
echo "\n";
/*
echo "simple test with non Point as parameter: \n";
$pt1 = 2;
$pt2 = new Point(4,2);
echo "should give fatal error:\n";
echo Geometry::y_intercept_of_line_passing_by($pt1, "test");
echo "\n";
echo "\n";
echo "********************************************************";
echo "\n";
*/
echo"testo of Geometry::bcscale_value():";
echo "\n";
$scale = Geometry::bcscale_value();
echo"current scale:".$scale.'(0)';
echo "\n";
if($scale != 0){
	die("ERROR 50");
}
bcscale(12);
$scale = Geometry::bcscale_value();
echo"current scale:".$scale.'(12)';
echo "\n";
if($scale != 12){
	die("ERROR 60");
}
bcscale(-5);
$scale = Geometry::bcscale_value();
echo"current scale:".$scale.'(0)';
echo "\n";
if($scale != 0){
	die("ERROR 70");
}
echo "\n";
echo "\n";
echo "********************************************************";

echo"testo of Geometry::bcscale_max 1:";
echo "\n";
echo"should return 19";
$x = '3216.6846516843346646534';
$scale = Geometry::bcscale_max(array($x, 568, 68146161.32));
echo "result: $scale";
if ($scale != 19)
	die("ERROR 80");
	
echo"testo of Geometry::bcscale_max 1:";
echo "\n";
echo"should return 19";
$x = 3216.6846516;
$scale = Geometry::bcscale_max(array($x, 568, 68146161.32));
echo "result: $scale";
if ($scale != 7)
	die("ERROR 90");
echo"***************************************************\n";
echo"***************************************************\n\n";
echo"            test ended, no error found\n\n";
echo"***************************************************\n";
echo"***************************************************\n";

