<?php
include_once 'intersections.php';
//include_once 'error.php';

/*$seg_A = new Segment( 1, 9, 3, 11 );
$seg_A->name = "A";
$seg_B = new Segment( 2, 10, 13, 6 );
$seg_B->name = "B";*/
$seg_C = new Segment( 4, 2, 11, 9 );
$seg_C->name = "C";
$seg_D = new Segment( 6, 3, 8, 6 );
$seg_D->name = "D";
$seg_E = new Segment( 6, 5.5, 11, 5.5 );
$seg_E->name = "E";
$seg_F = new Segment( 8, 6, 8, 9 );
$seg_F->name = "F";
/*$seg_G = new Segment( 8, 9, 11, 1 );
$seg_G->name = "G";
$seg_H = new Segment( 11, 1, 11, 5 );
$seg_H->name = "H";
$seg_I = new Segment( 11, 6, 11, 8 );
$seg_I->name = "I";*/

//$segs = array($seg_A, $seg_B, $seg_C, $seg_D, $seg_E, $seg_F, $seg_G, $seg_H,$seg_I);
$segs = array($seg_C, $seg_D, $seg_F, $seg_E);
$output_list = inter_segments($segs);

print_r($output_list);

