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
$pt1 = new Point ( 2.5, 60 );
if ($pt1->x != 2.5 || $pt1->y != 60)
	return die ( "ERROR 10" );
echo "works\n";
echo "showing the point:\n";
print_r ( $pt1 );
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
$pt1->set_x ( - 2.36 );
$pt1->set_y ( - 658.1 );
if ($pt1->x != - 2.36 || $pt1->y != - 658.1)
	return die ( "ERROR 20" );
echo "works\n";
echo "showing the point:\n";
print_r ( $pt1 );
echo "\n";
echo "****** get $pt1->x by get_x(): \n";
echo $pt1->get_x () . "\n";
echo "****** get $pt1->y by get_y(): \n";
echo $pt1->get_y () . "\n";
echo "\n";
if ($pt1->get_x () != - 2.36 || $pt1->get_y () != - 658.1)
	return die ( "ERROR 30" );

echo "***************************************************\n";
echo "            end setters/getters:\n";
echo "***************************************************\n";

echo "***************************************************\n";
echo "              isPartOfSegment():\n";
echo "***************************************************\n";

echo '******************* with $this = $pt2 = $pt3' . "\n";
echo '****** with $this = (0,0)' . "\n";
$pt1 = new Point ( 0, 0 );
$pt2 = new Point ( 0, 0 );
$pt3 = new Point ( 0, 0 );
echo 'showing $pt1:' . "\n";
print_r ( $pt1 );
echo 'showing $pt2:' . "\n";
print_r ( $pt2 );
echo 'showing $pt3:' . "\n";
print_r ( $pt3 );
try {
	//should throw an exception
	$pt1->isPartOfSegment ( $pt2, $pt3 );
	return die ( "ERROR 40" );
}
catch ( Exception $e ) {
	echo 'exception message : ' . $e->getMessage ();
	echo "\n";
}

echo "\n";
echo '****** with $this = (0.2354,226.36876)' . "\n";
$pt1 = new Point ( 0.2354, 226.36876 );
$pt2 = clone $pt1;
$pt3 = clone $pt1;
echo 'showing $pt1:' . "\n";
print_r ( $pt1 );
echo 'showing $pt2:' . "\n";
print_r ( $pt2 );
echo 'showing $pt3:' . "\n";
print_r ( $pt3 );
try {
	//should throw an exception
	$pt1->isPartOfSegment ( $pt2, $pt3 );
	return die ( "ERROR 50" );
}
catch ( Exception $e ) {
	echo 'exception message : ' . $e->getMessage ();
	echo "\n";
}
echo "\n";

echo '******************* with $pt2 = $pt3 != $this' . "\n";
$pt1 = new Point ( 0, 0 );
echo 'showing $pt1:' . "\n";
print_r ( $pt1 );
echo 'showing $pt2:' . "\n";
print_r ( $pt2 );
echo 'showing $pt3:' . "\n";
print_r ( $pt3 );
try {
	//should throw an exception
	$pt1->isPartOfSegment ( $pt2, $pt3 );
	return die ( "ERROR 60" );
}
catch ( Exception $e ) {
	echo 'exception message : ' . $e->getMessage ();
	echo "\n";
}

echo "\n";

echo '*******************  with $this = (0,0)' . "\n";
echo "****** horizontal case: \n";
$pt2 = new Point ( 0, 2.65464 );
$pt3 = new Point ( 0, - 0.36498468 );
if ($pt1->isPartOfSegment ( $pt2, $pt3 )) {
	echo '$pt1->isPartOfSegment($pt2, $pt3 returned true ' . "\n";
}
else {
	die ( 'ERROR 70 $pt1->isPartOfSegment($pt2, $pt3 returned false' );
}
echo "****** vertical case: \n";
$pt2 = new Point ( 2.65464, 0 );
$pt3 = new Point ( - 0.36498468, 0 );
if ($pt1->isPartOfSegment ( $pt2, $pt3 )) {
	echo 'ERROR 80 $pt1->isPartOfSegment($pt2, $pt3 returned true ' . "\n";
}
else {
	die ( '$pt1->isPartOfSegment($pt2, $pt3 returned false' );
}
echo "****** other case 1: \n";
$pt2 = new Point ( 21.63, 130.604 );
$pt3 = new Point ( - 2.1, - 12.68 );
if ($pt1->isPartOfSegment ( $pt2, $pt3 )) {
	echo '$pt1->isPartOfSegment($pt2, $pt3 returned true ' . "\n";
}
else {
	die ( 'ERROR 90 $pt1->isPartOfSegment($pt2, $pt3) returned false' );
}

echo "****** other case 2: \n";
$pt2 = new Point ( 4.973661, 30.0314388 );
$pt3 = new Point ( - 2.1, - 12.68 );
if ($pt1->isPartOfSegment ( $pt2, $pt3 )) {
	echo '$pt1->isPartOfSegment($pt2, $pt3, $scale) returned true ' . "\n";
}
else {
	die ( 'ERROR 100 $pt1->isPartOfSegment($pt2, $pt3) returned false' );
}

echo "***************************************************\n";
echo "            end isPartOfSegment():\n";
echo "***************************************************\n";

echo "***************************************************\n";
echo "                ==, ===, !=, !==:\n";
echo "***************************************************\n";
echo "****** test with == between 2 differents point withs same coordinates: \n";
$pt1 = new Point ( 2.5, 60 );
echo 'showing $pt1:' . "\n";
print_r ( $pt1 );
$pt2 = new Point ( 2.5, 60 );
echo 'showing $pt2:' . "\n";
print_r ( $pt2 );
if ($pt1 == $pt2) {
	echo '$pt1 == $pt2 works' . "\n";
}
else {
	die ( 'ERROR 110 $pt1 == $pt2 does not work' . "\n" );
}

if ($pt1 === $pt2) {
	die ( 'ERROR 120 $pt1 === $pt2 return true' . "\n" );
}
else {
	echo '$pt1 === $pt2 return false' . "\n";
}

if ($pt1 === $pt1) {
	echo '$pt1 === $pt1 return true' . "\n";
}
else {
	die ( 'ERROR 130 $pt1 === $pt1 return false' . "\n" );
}

echo "\n";
$pt2 = new Point ( 3, 60 );
echo 'showing new $pt2:' . "\n";
print_r ( $pt2 );

if ($pt1 != $pt2) {
	echo '$pt1 != $pt2 return true' . "\n";
}
else {
	die ( 'ERROR 140 $pt1 != $pt2 return false' . "\n" );
}

if ($pt1 !== $pt2) {
	echo '$pt1 !== $pt2 return true' . "\n";
}
else {
	die ( 'ERROR 150 $pt1 !== $pt2 return false' . "\n" );
}

$pt1 = $pt2;
echo '$pt1 = $pt2' . "\n";
if ($pt1 !== $pt2) {
	die ( 'ERROR 160 $pt1 !== $pt2 return true' . "\n" );
}
else {
	echo '$pt1 !== $pt2 return false' . "\n";
}

echo "***************************************************\n";
echo "             end ==, ===, !=, !==:\n";
echo "***************************************************\n";

echo "***************************************************\n";
echo "              Point::segment_intersection():\n";
echo "***************************************************\n";

echo "*****************test of 2 identicals segments: \n";
echo "***** vertical 1\n";
$pt1 = new Point ( 0, 5 );
$pt2 = new Point ( 0, 2 );
$pt3 = $pt1;
$pt4 = $pt2;
try {
	$result = Point::segment_intersection ( $pt1, $pt2, $pt3, $pt4, true );
	var_dump ( $result );
	if ($result != array ("same", array (1 => $pt1, 2 => $pt2 ) ))
		die ( "ERROR 170" );
}
catch ( Exception $e ) {
	echo 'exception message : ' . $e->getMessage ();
	die ( "ERROR 171" );
}

echo "\n";
echo "\n";

echo "***** vertical 2\n";
$pt1 = new Point ( - 2.3649, 654964 );
$pt2 = new Point ( - 2.3649, 0.321984 );
$pt3 = $pt1;
$pt4 = $pt2;
try {
	$result = Point::segment_intersection ( $pt1, $pt2, $pt3, $pt4, true );
	var_dump ( $result );
	if ($result != array ("same", array (1 => $pt1, 2 => $pt2 ) ))
		die ( "ERROR 180" );
}
catch ( Exception $e ) {
	echo 'exception message : ' . $e->getMessage ();
	die ( "ERROR 181" );
}

echo "\n";
echo "\n";

echo "***** horizontal 1 \n";
$pt1 = new Point ( 5.64361, 0 );
$pt2 = new Point ( - 49461, 0 );
$pt3 = $pt1;
$pt4 = $pt2;
try {
	$result = Point::segment_intersection ( $pt1, $pt2, $pt3, $pt4, true );
	var_dump ( $result );
	if ($result != array ("same", array (1 => $pt1, 2 => $pt2 ) ))
		die ( "ERROR 190" );
}
catch ( Exception $e ) {
	echo 'exception message : ' . $e->getMessage ();
	die ( "ERROR 191" );
}

echo "\n";
echo "\n";

echo "***** horizontal 2 \n";
$pt1 = new Point ( 5.64361, 645, 9846156564 );
$pt2 = new Point ( - 49461, 645, 9846156564 );
$pt3 = $pt1;
$pt4 = $pt2;
try {
	$result = Point::segment_intersection ( $pt1, $pt2, $pt3, $pt4, true );
	var_dump ( $result );
	if ($result != array ("same", array (1 => $pt1, 2 => $pt2 ) ))
		die ( "ERROR 200" );
}
catch ( Exception $e ) {
	echo 'exception message : ' . $e->getMessage ();
	die ( "ERROR 201" );
}

echo "\n";
echo "\n";

echo "***** other \n";
$pt1 = new Point ( 2.369, 7.348201 );
$pt2 = new Point ( - 0.34698, 0.47948758 );
$pt3 = $pt1;
$pt4 = $pt2;
try {
	$result = Point::segment_intersection ( $pt1, $pt2, $pt3, $pt4, true );
	var_dump ( $result );
	if ($result != array ("same", array (1 => $pt1, 2 => $pt2 ) ))
		die ( "ERROR 210" );
}
catch ( Exception $e ) {
	echo 'exception message : ' . $e->getMessage ();
	die ( "ERROR 211" );
}

echo "\n";
echo "\n";

echo "****************************************************";
echo "*****************test of 2 paralleles segments: \n";
echo "***** vertical 1 \n";
$pt1 = new Point ( 0, 5.6846146846164 );
$pt2 = new Point ( 0, 2.64944946831642 );
$pt3 = new Point ( 1.684319664, 5.6846146846164 );
$pt4 = new Point ( 1.684319664, 2.64944946831642 );
try {
	$result = Point::segment_intersection ( $pt1, $pt2, $pt3, $pt4, true );
	var_dump ( $result );
	if ($result != array(false, null))
		die ( "ERROR 220" );
}
catch ( Exception $e ) {
	echo 'exception message : ' . $e->getMessage ();
	die ( "ERROR 221" );
}

echo "\n";
echo "\n";

echo "***** vertical 2 \n";
$pt1 = new Point ( 5464.649399783246719, 5.6846146846164 );
$pt2 = new Point ( 5464.649399783246719, 2.64944946831642 );
$pt3 = new Point ( 1.684319664, 5.6846146846164 );
$pt4 = new Point ( 1.684319664, 2.64944946831642 );
try {
	$result = Point::segment_intersection ( $pt1, $pt2, $pt3, $pt4, true );
	var_dump ( $result );
	if ($result != array(false, null))
		die ( "ERROR 230" );
}
catch ( Exception $e ) {
	echo 'exception message : ' . $e->getMessage ();
	die ( "ERROR 231" );
}

echo "\n";
echo "\n";

echo "***** horizontale 1 \n";
$pt1 = new Point ( 5.6846146846164, 964.68746916165 );
$pt2 = new Point ( 2.64944946831642, 964.68746916165 );
$pt3 = new Point ( - 0.6874984164, 0 );
$pt4 = new Point ( 5798.36849631, 0 );
try {
	$result = Point::segment_intersection ( $pt1, $pt2, $pt3, $pt4, true );
	var_dump ( $result );
	if ($result != array(false, null))
		die ( "ERROR 240" );
}
catch ( Exception $e ) {
	echo 'exception message : ' . $e->getMessage ();
	die ( "ERROR 241" );
}

echo "\n";
echo "\n";

echo "***** horizontal 2 \n";
$pt1 = new Point ( 5.6846146846164, 564984.68433369774108 );
$pt2 = new Point ( 2.64944946831642, 564984.68433369774108 );
$pt3 = new Point ( - 0.6874984164, -2.87676987);
$pt4 = new Point ( 5798.36849631, -2.87676987 );
try {
	$result = Point::segment_intersection ( $pt1, $pt2, $pt3, $pt4, true );
	var_dump ( $result );
	if ($result != array(false, null))
		die ( "ERROR 250" );
}
catch ( Exception $e ) {
	echo 'exception message : ' . $e->getMessage ();
	die ( "ERROR 251" );
}

echo "\n";
echo "\n";

echo "***** other \n";
$pt1 = new Point ( 2, 369, 7, 348201 );
$pt2 = new Point ( - 0, 34698, 0, 47948758 );
$pt3 = new Point ( - 568, 369, - 1563, 086801 );
$pt4 = new Point ( - 1368, 3, - 3586, 1123 );
try {
	$result = Point::segment_intersection ( $pt1, $pt2, $pt3, $pt4, true );
	var_dump ( $result );
	if ($result != array(false, null))
		die ( "ERROR 260" );
}
catch ( Exception $e ) {
	echo 'exception message : ' . $e->getMessage ();
	die ( "ERROR 261" );
}

echo "\n";
echo "\n";

echo "*********************************************************\n";
echo "test of 2 segments on the same line which not intersect: \n";
echo "\t separated \n";
$pt1 = new Point ( 2.369, 7.348201 );
$pt2 = new Point ( - 0.34698, 0.47948758 );
$pt3 = new Point ( - 568.369, - 1436.048201 );
$pt4 = new Point ( - 1368.3, - 3459.0737 );

$result = Point::segment_intersection ( $pt1, $pt2, $pt3, $pt4, true );
var_dump ( $result );
	if ($result != array(false, null))
	die ( "ERROR 270" );
	
echo "\n";
echo "\n";

echo "*****************************************\n";
echo "test of 2 intersects segments on the same line: \n";
$pt1 = new Point ( 2.369, 7.348201 );
$pt2 = new Point ( - 568.369, - 1436.048201 );
$pt3 = new Point ( - 0.34698, 0.47948758 );
$pt4 = new Point ( - 1368.3, - 3459.0737 );

$result = Point::segment_intersection ( $pt1, $pt2, $pt3, $pt4, true );
var_dump ( $result );
if ($result != array("merged", array(2=>$pt2, 3=>$pt3)))
	die ( "ERROR 280" );
	
echo "\n";
echo "\n";

echo "*****************************************\n";
echo "test of 2 intersects segments 1 \n";
$pt1 = new Point ( 0, 8 );
$pt2 = new Point ( 0, 0 );
$pt3 = new Point ( -1, 2 );
$pt4 = new Point ( 2, 2 );

$result = Point::segment_intersection ( $pt1, $pt2, $pt3, $pt4, true );
var_dump ( $result );
if ($result[1]->isAlignedWith($pt1, $pt2) !== true && $result[1]->isAlignedWith($pt3, $pt4) !== true)
	die ( "ERROR 290" );
	
echo "\n";
echo "*****************************************\n";
echo "\n";

echo "*****************************************\n";
echo "test of 2 intersects segments 2 \n";
$pt1 = new Point ( 2.369, 7.348201 );
$pt2 = new Point ( - 568.369, - 1436.048201 );
$pt3 = new Point ( -65.94646564, 0.47948758 );
$pt4 = new Point (  1368.3, - 59.0731146690077 );

$result = Point::segment_intersection ( $pt1, $pt2, $pt3, $pt4, true );
var_dump ( $result );
if ($result[1]->isAlignedWith($pt1, $pt2) !== true && $result[1]->isAlignedWith($pt3, $pt4) !== true)
	die ( "ERROR 290" );
	
echo "\n";
echo "*****************************************\n";
echo "\n";

echo "test with one commun point: \n";
echo "\t result should be (0,0): \n";
$pt1 = new Point ( 0, 0 );
$pt2 = new Point ( 1, 2 );
$pt3 = new Point ( 0, 0 );
$pt4 = new Point ( 1, 0 );
$result = Point::segment_intersection ( $pt1, $pt2, $pt3, $pt4 );
var_dump ( $result );
if ($result[0] !== true || $result[1] != $pt1)
	die ( "ERROR 290" );
echo "\n";
echo "\n";

echo "test of 2 parallels segments with one point en commun: \n";
echo "\t result should be colinear: \n";
$pt1 = new Point ( 0, 0 );
$pt2 = new Point ( 1, 2 );
$pt3 = new Point ( 0, 0 );
$pt4 = new Point ( 2, 4 );
$result = Point::segment_intersection ( $pt1, $pt2, $pt3, $pt4 );
var_dump ( $result );
if ($result[0] != "colinear")
	die ( "ERROR 300" );
echo "\n";
echo "\n";

echo "test of 2 verticals segments with one point en commun: \n";
echo "\t result should be colinear: \n";
$pt1 = new Point ( 0, 0 );
$pt2 = new Point ( 0, 1 );
$pt3 = new Point ( 0, 0 );
$pt4 = new Point ( 0, 5 );
$result = Point::segment_intersection ( $pt1, $pt2, $pt3, $pt4 );
var_dump ( $result );
if ($result[0] != "colinear")
	die ( "ERROR 310" );
echo "\n";
echo "\n";

echo "test of 2 horizontal segments with one point en commun: \n";
echo "\t result should be colinear: \n";
$pt1 = new Point ( 0, 0 );
$pt2 = new Point ( 5, 0 );
$pt3 = new Point ( 0, 0 );
$pt4 = new Point ( 6, 0 );
$result = Point::segment_intersection ( $pt1, $pt2, $pt3, $pt4 );
var_dump ( $result );
if ($result[0] != "colinear")
	die ( "ERROR 320" );
echo "\n";
echo "*****************************************\n";
echo "\n";

echo "*****************************************\n";
echo "test of 2 colinear segments: \n";
echo "\t identicals: \n";
$pt1 = new Point ( 0, 0 );
$pt2 = new Point ( 5, 0 );
$pt3 = new Point ( 0, 0 );
$pt4 = new Point ( 5, 0 );
$result = Point::segment_intersection ( $pt1, $pt2, $pt3, $pt4 );
var_dump ( $result );
if ($result[0] != "colinear")
	die ( "ERROR 330" );
echo "\n";
echo "\n";

echo "\t verticals: \n";
$pt1 = new Point ( 0, 5 );
$pt2 = new Point ( 0, 0 );
$pt3 = new Point ( 0, 1 );
$pt4 = new Point ( 0, 2 );
$result = Point::segment_intersection ( $pt1, $pt2, $pt3, $pt4 );
var_dump ( $result );
if ($result[0] != "colinear")
	die ( "ERROR 340" );
echo "\n";
echo "\n";

echo "\t other type: \n";
$pt1 = new Point ( 0, 0 );
$pt2 = new Point ( 1, 2 );
$pt3 = new Point ( 0, 0 );
$pt4 = new Point ( 2, 4 );
$result = Point::segment_intersection ( $pt1, $pt2, $pt3, $pt4 );
var_dump ( $result );
if ($result[0] != "colinear")
	die ( "ERROR 350" );
echo "\n";
echo "\n";

echo"***************************************************\n";
echo"***************************************************\n\n";
echo"            test ended, no error found\n\n";
echo"***************************************************\n";
echo"***************************************************\n";