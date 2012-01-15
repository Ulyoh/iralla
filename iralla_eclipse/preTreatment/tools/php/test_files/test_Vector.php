<?php
//tests

include_once 'Vector.php';

$pt1 = new Point(5.9, 6.3);
$pt2 = new Point(6, 9.2);

$vector1 = new Vector($pt1, $pt2);
echo 'vector1 : ';
print_r($vector1);
echo "\n";

$pt2 = 3;

try {
	$vector2 = new Vector($pt1, $pt2);
	echo 'ERROR : vector2 created'."\n";
}
catch (Exception $e) {
	echo 'exception message : '.$e->getMessage();
	echo "\n";
}

$pt2 = new Point(6, 9.2);
$segment1 = new Segment($pt1, $pt2);
$vector3 = new Vector($segment1);
echo 'vector3 : ';
print_r($vector3);
echo "\n";
if($vector1 == $vector3){
	echo"vector 1 et 2 are egals";
}

else 
	 die("ERROR 50");
echo "\n";	 
echo"***************************************************\n";
echo"***************************************************\n\n";
echo"            test ended, no error found\n\n";
echo"***************************************************\n";
echo"***************************************************\n";
	 
