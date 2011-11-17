<?php

//tests:
include_once 'Point.php';

echo "***************************************************\n";
echo "                __construct():\n";
echo "***************************************************\n";
/*echo "****** test without parameter: \n";
echo "show an exception message:\n";
try {
	$pt1 = new Point();
} catch (Exception $e) {
	echo 'exception message : '.$e->getMessage();
	echo "\n";
}
echo "\n";
echo "****** test with one parameter: \n";
echo "show an exception message:\n";
try {
	$pt1 = new Point(5);
} catch (Exception $e) {
	echo 'exception message : '.$e->getMessage();
	echo "\n";
}
echo "\n";*/
echo "****** test with two parameter (2.5, 60): \n";
try {
	$pt1 = new Point(2.5, 60);
} catch (Exception $e) {
	echo 'exception message : '.$e->getMessage();
	echo "\n";
}
echo "works\n";
echo "showing the point:\n";
print_r($pt1);
echo "\n";
echo "****** get $pt1->x directly: \n";
echo "$pt1->x\n";
echo "****** get $pt1->y directly: \n";
echo "$pt1->y\n";

echo "***************************************************\n";
echo "              end __construct():\n";
echo "***************************************************\n";

echo "***************************************************\n";
echo "              setters/getters:\n";
echo "***************************************************\n";
echo "****** set previous point to (-2.36;-658.1): \n";
$pt1->set_x(-2.36);
$pt1->set_y(-658.1);
echo "works\n";
echo "showing the point:\n";
print_r($pt1);
echo "\n";
echo "****** get $pt1->x by get_x(): \n";
echo $pt1->get_x()."\n";
echo "****** get $pt1->y by get_y(): \n";
echo $pt1->get_y()."\n";
echo "\n";

echo "***************************************************\n";
echo "            end setters/getters:\n";
echo "***************************************************\n";


echo "***************************************************\n";
echo "              isPartOfSegment():\n";
echo "***************************************************\n";

echo '******************* with $this = $pt2 = $pt3'."\n";
echo '****** with $this = (0,0)'."\n";
$pt1 = new Point(0, 0);
$pt2 = new Point(0, 0);
$pt3 = new Point(0, 0);
echo 'showing $pt1:'."\n";
print_r($pt1);
echo 'showing $pt2:'."\n";
print_r($pt2);
echo 'showing $pt3:'."\n";
print_r($pt3);
if ($pt1->isPartOfSegment($pt2, $pt3)){
	echo '$pt1->isPartOfSegment($pt2, $pt3) returned true '."\n";
}
else{
	exit('$pt1->isPartOfSegment($pt2, $pt3) returned false ERROR ');
}
echo "\n";
echo '****** with $this = (0.2354,226.36876)'."\n";
$pt1 = new Point(0.2354,226.36876);
$pt2 = clone $pt1;
$pt3 = clone $pt1;
echo 'showing $pt1:'."\n";
print_r($pt1);
echo 'showing $pt2:'."\n";
print_r($pt2);
echo 'showing $pt3:'."\n";
print_r($pt3);
if ($pt1->isPartOfSegment($pt2, $pt3)){
	echo '$pt1->isPartOfSegment($pt2, $pt3) returned true '."\n";
}
else{
	exit('$pt1->isPartOfSegment($pt2, $pt3) returned false ERROR ');
}
echo "\n";


echo '******************* with $pt2 = $pt3 != $this'."\n";
$pt1 = new Point(0,0);
echo 'showing $pt1:'."\n";
print_r($pt1);
echo 'showing $pt2:'."\n";
print_r($pt2);
echo 'showing $pt3:'."\n";
print_r($pt3);
try {
	$pt1->isPartOfSegment($pt2, $pt3);
} catch (Exception $e) {
	echo 'exception message : '.$e->getMessage();
	echo "\n";
}

echo "\n";

echo '******************* with $scale passed as argument'."\n";
echo '*********** with $this = (0,0)'."\n";
echo "****** horizontal case: \n";
$scale = 10;
$pt2 = new Point(0, 2.65464);
$pt3 = new Point(0, -0.36498468);
if ($pt1->isPartOfSegment($pt2, $pt3, $scale)){
	echo '$pt1->isPartOfSegment($pt2, $pt3, $scale) returned true '."\n";
}
else{
	exit('$pt1->isPartOfSegment($pt2, $pt3, $scale) returned false ERROR ');
}
echo "****** vertical case: \n";
$pt2 = new Point(2.65464, 0);
$pt3 = new Point(-0.36498468, 0);
if ($pt1->isPartOfSegment($pt2, $pt3, $scale)){
	echo '$pt1->isPartOfSegment($pt2, $pt3, $scale) returned true '."\n";
}
else{
	exit('$pt1->isPartOfSegment($pt2, $pt3, $scale) returned false ERROR ');
}
echo "****** other case 1: \n";
$pt2 = new Point(21.63, 130.604);
$pt3 = new Point(-2.1, -12.68);
if ($pt1->isPartOfSegment($pt2, $pt3, $scale)){
	echo '$pt1->isPartOfSegment($pt2, $pt3, $scale) returned true '."\n";
}
else{
	exit('$pt1->isPartOfSegment($pt2, $pt3, $scale) returned false ERROR ');
}

echo "****** other case 2: \n";
$pt2 = new Point(4.973661, 30.0314388);
$pt3 = new Point(-2.1, -12.68);
if ($pt1->isPartOfSegment($pt2, $pt3, $scale)){
	echo '$pt1->isPartOfSegment($pt2, $pt3, $scale) returned true '."\n";
}
else{
	exit('$pt1->isPartOfSegment($pt2, $pt3, $scale) returned false ERROR ');
}

echo "****** other case 3: \n";
$pt2 = new Point(4.973661, 30.0314388);
$pt3 = new Point(-2.1, -12.68);
if ($pt1->isPartOfSegment($pt2, $pt3, 3)){
	echo '$pt1->isPartOfSegment($pt2, $pt3, 3) returned true '."\n";
}
else{
	exit('$pt1->isPartOfSegment($pt2, $pt3, 3) returned false ERROR ');
}

echo '******************* with $scale passed as global'."\n";
bcscale(0);
echo '*********** with $this = (0,0)'."\n";
echo "****** horizontal case: \n";
$scale = 50;
$pt2 = new Point(0, 2.65464);
$pt3 = new Point(0, -0.36498468);
if ($pt1->isPartOfSegment($pt2, $pt3)){
	echo '$pt1->isPartOfSegment($pt2, $pt3) returned true '."\n";
}
else{
	exit('$pt1->isPartOfSegment($pt2, $pt3) returned false ERROR ');
}
echo "****** vertical case: \n";
$pt2 = new Point(2.65464, 0);
$pt3 = new Point(-0.36498468, 0);
if ($pt1->isPartOfSegment($pt2, $pt3)){
	echo '$pt1->isPartOfSegment($pt2, $pt3) returned true '."\n";
}
else{
	exit('$pt1->isPartOfSegment($pt2, $pt3) returned false ERROR ');
}
echo "****** other case 1: \n";
$pt2 = new Point(21.63, 130.604);
$pt3 = new Point(-2.1, -12.68);
if ($pt1->isPartOfSegment($pt2, $pt3)){
	echo '$pt1->isPartOfSegment($pt2, $pt3) returned true '."\n";
}
else{
	exit('$pt1->isPartOfSegment($pt2, $pt3) returned false ERROR ');
}

echo "****** other case 2: \n";
$pt2 = new Point(4.973661, 30.0314388);
$pt3 = new Point(-2.1, -12.68);
if ($pt1->isPartOfSegment($pt2, $pt3)){
	echo '$pt1->isPartOfSegment($pt2, $pt3) returned true '."\n";
}
else{
	exit('$pt1->isPartOfSegment($pt2, $pt3) returned false ERROR ');
}

echo "***************************************************\n";
echo "            end isPartOfSegment():\n";
echo "***************************************************\n";


echo "***************************************************\n";
echo "                ==, ===, !=, !==:\n";
echo "***************************************************\n";
echo "****** test with == between 2 differents point withs same coordinates: \n";
$pt1 = new Point(2.5, 60);
echo 'showing $pt1:'."\n";
print_r($pt1);
$pt2 = new Point(2.5, 60);
echo 'showing $pt2:'."\n";
print_r($pt2);
if($pt1 == $pt2){
	echo '$pt1 == $pt2 works'."\n";
}
else{
	echo '$pt1 == $pt2 does not work'."\n";
}

if($pt1 === $pt2){
	echo '$pt1 === $pt2 return true ERROR'."\n";
}
else{
	echo '$pt1 === $pt2 return false'."\n";
}

if($pt1 === $pt1){
	echo '$pt1 === $pt1 return true'."\n";
}
else{
	echo '$pt1 === $pt1 return false ERROR'."\n";
}

echo "\n";
$pt2 = new Point(3, 60);
echo 'showing new $pt2:'."\n";
print_r($pt2);

if($pt1 != $pt2){
	echo '$pt1 != $pt2 return true'."\n";
}
else{
	echo '$pt1 != $pt2 return false ERROR'."\n";
}

if($pt1 !== $pt2){
	echo '$pt1 !== $pt2 return true'."\n";
}
else{
	echo '$pt1 !== $pt2 return false ERROR'."\n";
}

$pt1 = $pt2;
echo '$pt1 = $pt2'."\n";
if($pt1 !== $pt2){
	echo '$pt1 !== $pt2 return true ERROR'."\n";
}
else{
	echo '$pt1 !== $pt2 return false'."\n";
}


echo "***************************************************\n";
echo "             end ==, ===, !=, !==:\n";
echo "***************************************************\n";


echo "***************************************************\n";
echo "              Point::segment_intersection():\n";
echo "***************************************************\n";

echo "*****************************************\n";
echo "test of 2 identicals segments: \n";
echo "\t separated \n";
$p1 = new Point ( - 2, - 2 );
$p2 = new Point ( 0, 2 );
$p3 = $p1;
$p4 = $p2;
var_dump ( Point::segment_intersection ( $p1, $p2, $p3, $p4, true ) );
echo "\n";
echo "\n";

echo "*****************************************\n";
echo "test of 2 segments on the same line which intersect: \n";
echo "\t separated \n";
$p1 = new Point ( 2.369, 7.348201 );
$p2 = new Point ( -0.34698, -1436.048201);
$p3 = new Point ( -568.369, -3459.0737);
$p4 = new Point ( -1368.3, 0.47948758 );
var_dump ( Point::segment_intersection ( $p1, $p2, $p3, $p4, true ) );
echo "\n";
echo "\n";


echo"*****************************************\n";
echo "test of 2 intersect segments on the same line: \n";
echo "\t result should be: \n";
$p1 = new Point ( 2.369, 7.348201 );
$p2 = new Point ( -568.369, -3459.0737);
$p3 = new Point ( -0.34698, -1436.048201);
$p4 = new Point ( -1368.3, 0.47948758 );
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
echo "\t result should be colinear: \n";
$p1 = new Point(0,0);
$p2 = new Point(1,2);
$p3 = new Point(0,0);
$p4 = new Point(2,4);
var_dump(Point::segment_intersection($p1, $p2, $p3, $p4));
echo "\n";
echo "\n";

echo "test of 2 verticals segments with one point en commun: \n";
echo "\t result should be colinear: \n";
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
echo "\n";
