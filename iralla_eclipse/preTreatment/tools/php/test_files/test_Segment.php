<?php

//tests

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

