<?php
include_once 'Point.php';
include_once 'Segment.php';

//TODO : gestion du premier mailllon de la chaine 


//TODO : add maillon at the end of the chain not managed

function inter_segments(array $segments) {
	$segment = new Segment ();
	
	$output_list = array ();
	
	//make a list of ends points with reference to each segment endpoints
	$endpoints_to_treat = array ();
	foreach ( $segments as $segment ) {
		array_merge ( $endpoints_to_treat, $segment::pts_as_array () );
	}
	
	//order the endpoints_to_treat by x-axis, y-axis value of end point
	function cmp_local($pt1, $pt2) {
		if ($pt1 == $pt2) {
			return 0;
		}
		if (($pt1->x < $p2->x) || (($pt1->x == $pt2) && ($pt1->y < $pt2 > y))) {
			return - 1;
		}
		else {
			return 1;
		}
	}
	usort ( $end_pts_to_treat, "cmp_local" );
	
	$sweep_line = array ();
	$intersections_list = array ();
	$first_maillon = null;
	//while $intersections_list is not empty
	while ( isset ( $endpoints_to_treat [0] ) == true ) {
		//determine the next event point in $end_pts_to_treat
		$event = array_shift ( $endpoints_to_treat );
		
		if ($event->position == 'left') {
			
			$seg_s_event = $event->segment;
			$y_postion = $seg_s_event->get_left_pt ()->y;
			$seg_s_event_and_y = array ("seg" => $seg_s_event, "y" => $y_postion );
			
			//insert the segment in the list depending of its y position:
			if (! isset ( $first_maillon )) {
				$new_maillon = new Maillon ();
				$new_maillon->cur_seg = $seg_s_event;
				$new_maillon->y = $y_postion;
				$seg_s_event->maillon = $new_maillon;
				$sweep_line_beginning = $new_maillon;
			}
			else {
				$new_maillon = new Maillon ();
				$new_maillon->cur_seg = $seg_s_event;
				$new_maillon->y = $y_postion;
				$seg_s_event->maillon = $new_maillon;
				
				$cur_maillon = $sweep_line_beginning;
				while ( $new_maillon->y < $cur_maillon->y ) {
					$cur_maillon = $cur_maillon->next;
				}
				Maillon::add_before ( $new_maillon, $cur_maillon );
				
				//get the above and below segment:
				$above_seg = get_above_segment ( $new_maillon ); //TODO verifiy if error of selection above below?????
				$below_seg = get_below_segment ( $new_maillon );
				
				//calcul intersections with $seg_s_event if any:
				treat_intersection_possibility ( $below_seg, $seg_s_event );
				treat_intersection_possibility ( $seg_s_event, $above_seg );
			}
		}
		elseif ($event->position == 'right') {
			$seg_s_event = $event->segment;
			
			//get the above and below segment:
			$above_seg = $seg_s_event->next->segment; //TODO verify if error of selection above below?????
			$below_seg = get_below_segment ( $new_maillon );
			
			//remove $seg_s_event from $sweep_line:
			$seg_s_event->maillon->remove ();
			
			//if above and below intersect not already found
			treat_intersection_possibility ( $below_seg, $above_seg );
		}
		else { //intersection event
			$output_list [] = $event;
			$seg1 = $event->segment [0];
			$seg2 = $event->segment [1];
			
			//find the 2 segments below and above of $seg 1 and 2
			$below_seg = $seg1->previous;
			$above_seg = $seg2->next;
			
			//swap theirs positions:
			$seg1->maillon->swap_with ( $seg2->maillon );
			
			//if $seg2 and $below_seg intersect not already found
			treat_intersection_possibility ( $below_seg, $seg1 );
			treat_intersection_possibility ( $seg2, $above_seg );
		}
	}
}

function get_above_segment($cur_maillon) {
	return ($new_maillon->next != null) ? $new_maillon->next->segment : null;
}
function get_below_segment($cur_maillon) {
	$maillon_test = $cur_maillon->prev->segment;
	while ( ($maillon_test != null) && ($cur_maillon->y == $maillon_test->y) ) {
		$maillon_test = $maillon_test->previous;
	}
	if ($maillon_test == null) {
		return null;
	}
	else {
		return $maillon_test->segment;
	}
}

function treat_intersection_possibility($below_seg, $above_seg, $endpoints_to_treat) {
	if (($below_seg == null) || ($above_seg == null)) {
		return null;
	}
	if (in_array ( $below_seg, $above_seg->intersect_with )) {
		return;
	}
	
	$intersect_result = $below_seg->find_intersection_with ( $above_seg, true );
	if ($intersect_result [0] == true) {
		$intersect_result [1]->segment [] = $below_seg;
		$intersect_result [1]->segment [] = $above_seg;
		insert_intersect_point_in_array ( $intersect_result, $endpoints_to_treat );
		add_links_of_intersect_segments ( $below_seg, $above_seg );
	}
	elseif ($intersect_result [0] == "merged") {
		//TODO
	}
	elseif ($intersect_result [0] == "same") {
		//TODO
	}
}

function add_links_of_intersect_segments($seg1, $seg2) {
	$seg1->intersect_with [] = $seg2;
	$seg2->intersect_with [] = $seg1;
}

function insert_intersect_point_in_array($intersect_result, $endpoints_to_treat) {
	$offset = 0;
	foreach ( $endpoints_to_treat as $value ) {
		if ($intersect_result [1]->x < $value->x) {
			break;
		}
		$offset ++;
	}
	$endpoints_to_treat [$offset] = $intersect_result [1];
}

function get_seg_s_event_key($sweep_line, $seg2) {
	foreach ( $sweep_line as $key => $value ) {
		if ($value ["seg"] == $seg_s_event) {
			return $key;
		}
	}
	die ( "ERROR 4392" );
}		
				