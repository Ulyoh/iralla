<?php
include_once 'Segment.php';
//include_once 'error.php';


//tests
echo "***************************************************\n";
echo "              test __construct():\n";
echo "***************************************************\n";
echo "\n";
echo "****** with valids points:\n";
echo "\n";
$pt1 = new Point( 5.9, 32 );
$pt2 = new Point( 6, 9.2 );
$segment1 = new Segment( $pt1, $pt2 );
print_r( $segment1 );
echo "\n";
echo "****** with an unvalid point:\n";
echo "\n";
$pt3 = "a string\n";
try {
	$segment2 = new Segment( $pt1, $pt3 );
	echo 'segment2 : ';
	print_r( $segment2 );
	echo "\n";
}
catch ( Exception $e ) {
	echo 'exception message : ' . $e->getMessage();
}

echo "\n";
echo "****** with valids coordinates:\n";
echo "\n";
$segment1 = new Segment( 2.0, 5.3, 3, 9 );
print_r( $segment1 );
echo "\n";
echo "****** with only 3 coordinates:\n";
echo "\n";

try {
	$segment1 = new Segment( 2.0, 5.3, 3 );
	print_r( $segment1 );
	echo "\n";
}
catch ( Exception $e ) {
	echo 'exception message : ' . $e->getMessage();
}

echo "\n";
echo "\n";
echo "****** with vertical segment:\n";
$pt1 = new Point( 0, 0 );
$pt2 = new Point( 0, 9.2 );
$segment1 = new Segment( $pt1, $pt2 );
print_r( $segment1 );

echo "\n";
echo "\n";
echo "***************************************************\n";
echo "           end test __construct():\n";
echo "***************************************************\n";
echo "\n";
echo "\n";
echo "***************************************************\n";
echo "                  test getter:\n";
echo "***************************************************\n";
echo "\n";
echo "****** with stantard segment with pt1 left and pt2 right \n";
$pt1 = new Point( 5.9, 32 );
$pt2 = new Point( 6, 9.2 );
$segment1 = new Segment( $pt1, $pt2 );
if ($segment1->get_pt1()->same_coord_as( $pt1 )) {
	echo "get_pt1 works\n";
}
else {
	die( "error with get_pt1" );
}
if ($segment1->get_pt2()->same_coord_as( $pt2 )) {
	echo "get_pt2 works\n";
}
else {
	die( "error with get_pt2" );
}
if ($segment1->get_left_pt()->same_coord_as( $pt1 )) {
	echo "get_left_pt works\n";
}
else {
	die( "error with get_left_pt" );
}
if ($segment1->get_right_pt()->same_coord_as( $pt2 )) {
	echo "get_right_pt works\n";
}
else {
	die( "error with get_right_pt" );
}

echo "****** with stantard segment with pt1 right and pt2 left \n";
$pt1 = new Point( 8, 32 );
$pt2 = new Point( 6, 9.2 );
$segment1 = new Segment( $pt1, $pt2 );
if ($segment1->get_pt1()->same_coord_as( $pt1 )) {
	echo "get_pt1 works\n";
}
else {
	die( "error with get_pt1" );
}
if ($segment1->get_pt2()->same_coord_as( $pt2 )) {
	echo "get_pt2 works\n";
}
else {
	die( "error with get_pt2" );
}
if ($segment1->get_left_pt()->same_coord_as( $pt2 )) {
	echo "get_left_pt works\n";
}
else {
	die( "error with get_left_pt" );
}
if ($segment1->get_right_pt()->same_coord_as( $pt1 )) {
	echo "get_right_pt works\n";
}
else {
	die( "error with get_right_pt" );
}

echo "****** with vertical segment with pt1 higher than pt2 \n";
$pt1 = new Point( 8, 32 );
$pt2 = new Point( 8, 9.2 );
$segment1 = new Segment( $pt1, $pt2 );
if ($segment1->get_pt1()->same_coord_as( $pt1 )) {
	echo "get_pt1 works\n";
}
else {
	die( "error with get_pt1" );
}
if ($segment1->get_pt2()->same_coord_as( $pt2 )) {
	echo "get_pt2 works\n";
}
else {
	die( "error with get_pt2" );
}
if ($segment1->get_left_pt()->same_coord_as( $pt2 )) {
	echo "get_left_pt works\n";
}
else {
	die( "error with get_left_pt" );
}
if ($segment1->get_right_pt()->same_coord_as( $pt1 )) {
	echo "get_right_pt works\n";
}
else {
	die( "error with get_right_pt" );
}

echo "****** with vertical segment with pt1 lower than pt2 \n";
$pt1 = new Point( 8, 32 );
$pt2 = new Point( 8, 54 );
$segment1 = new Segment( $pt1, $pt2 );
if ($segment1->get_pt1()->same_coord_as( $pt1 )) {
	echo "get_pt1 works\n";
}
else {
	die( "error with get_pt1" );
}
if ($segment1->get_pt2()->same_coord_as( $pt2 )) {
	echo "get_pt2 works\n";
}
else {
	die( "error with get_pt2" );
}
if ($segment1->get_left_pt()->same_coord_as( $pt1 )) {
	echo "get_left_pt works\n";
}
else {
	die( "error with get_left_pt" );
}
if ($segment1->get_right_pt()->same_coord_as( $pt2 )) {
	echo "get_right_pt works\n";
}
else {
	die( "error with get_right_pt" );
}

echo "\n";
echo "\n";
echo "***************************************************\n";
echo "                end  test getter:\n";
echo "***************************************************\n";
echo "\n";
echo "\n";
echo "***************************************************\n";
echo "                test get_pts_as_array():\n";
echo "***************************************************\n";
$array = $segment1->get_pts_as_array();
if ( $array[0]->same_coord_as($pt1) &&  $array[1]->same_coord_as($pt2)){
	echo "get_pts_as_array works";
}
else {
	die( "ERROR: get_pts_as_array does not works" );
}
echo "\n";
echo "\n";
echo "***************************************************\n";
echo "             end  test get_pts_as_array:\n";
echo "***************************************************\n";
echo "\n";
echo "\n";
echo "***************************************************\n";
echo "***************************************************\n\n";
echo "            test ended, no error found\n\n";
echo "***************************************************\n";
echo "***************************************************\n";