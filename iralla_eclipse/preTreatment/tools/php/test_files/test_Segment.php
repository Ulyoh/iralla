<?php
include_once 'Segment.php';

//tests
echo "***************************************************\n";
echo "              test __construct():\n";
echo "***************************************************\n";
echo "\n";
echo "****** with valids points:\n";
echo "\n";
$pt1 = new Point(5.9, 6.3);
$pt2 = new Point(6, 9.2);
$segment1 = new Segments($pt1, $pt2);
print_r($segment1);
echo "\n";
echo"****** with an unvalid point:\n";
echo "\n";
$pt3 = "a string\n";
try {
	$segment2 = new Segments($pt1, $pt3);
	echo 'segment2 : ';
	print_r($segment2);
	echo "\n";
} catch (Exception $e) {
	echo 'exception message : '.$e->getMessage();
}

echo "\n";
echo "\n";
echo "***************************************************\n";
echo "           end test __construct():\n";
echo "***************************************************\n";
