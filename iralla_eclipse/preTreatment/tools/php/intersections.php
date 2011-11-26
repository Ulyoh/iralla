<?php
include_once 'Point.php';
include_once 'Segment.php';
include_once 'Maillon.php';
//the duplicate segment must be removed before treatment


function inter_segments(array $segments) {
	$output_list = array();
	
	//make a list of ends points with reference to each segment endpoints
	$events_to_treat = array();
	foreach ( $segments as $segment ) {
		$events_to_treat = array_merge( $events_to_treat, $segment->get_pts_as_array() );
	}
	
	//order the endpoints_to_treat by x-axis, y-axis value of end point
	function cmp_local($pt1, $pt2) {
		if ($pt1->same_coord_as( $pt2 )) {
			//if one is right
			if ($pt1->position == "right") {
				return - 1;
			}
			elseif ($pt2->position == "right") {
				return 1;
			}
			//then the two are left:
			//order them by slope of the segment:
			elseif ($pt1->segment->get_slope() < $pt2->segment->get_slope()) {
				return - 1;
			}
			else {
				return 1;
			}
		
		}
		if (($pt1->x < $pt2->x) || (($pt1->x == $pt2->x) && ($pt1->y < $pt2->y))) {
			return - 1;
		}
		else {
			return 1;
		}
	}
	
	usort( $events_to_treat, "cmp_local" );
	
	$intersections_list = array();
	$intersections_pts = array();
	$sweep_line_beginning = null;
	//while $intersections_list is not empty
	while ( isset( $events_to_treat[0] ) == true ) {
		//determine the next event point in $end_pts_to_treat
		$event = array_shift( $events_to_treat );
		$seg_s_event = $event->segment;
		$x_position = $seg_s_event->get_left_pt()->x;
		
		//if a vertical segment:
		//found all intersection with it in the chain and the events_to_treat
		if ($event->segment->get_slope() === INF) {
			treatment_of_vertical_segment( $event, $sweep_line_beginning, $events_to_treat, $x_position, &$output_list );
			continue;
		}
		
		//determine if the current event is the last one for its coordinates
		if (isset( $events_to_treat[0] ) && ($event->same_coord_as( $events_to_treat[0] ))) {
			$last_event_at_coord = false;
		}
		else {
			$last_event_at_coord = true;
		}
		
		if (isset( $event->position ) && ($event->position == 'left')) {
			
			//insert the segment in the list depending of its y position:
			if (! isset( $sweep_line_beginning )) {
				$new_maillon = new Maillon();
				$new_maillon->segment = $seg_s_event;
				$new_maillon->y_position = $seg_s_event->get_left_pt()->y;
				$new_maillon->x_position = $x_position;
				$seg_s_event->maillon = $new_maillon;
				$sweep_line_beginning = $new_maillon;
			}
			else {
				$new_maillon = new Maillon();
				$new_maillon->segment = $seg_s_event;
				$new_maillon->y_position = $seg_s_event->get_left_pt()->y;
				$new_maillon->x_position = $x_position;
				$seg_s_event->maillon = $new_maillon;
				
				//add the segment in a new maillon in y up order
				if ($new_maillon->y_position <
				 y_position_of( $sweep_line_beginning, $x_position )) {
					Maillon::add_before( $new_maillon, $sweep_line_beginning );
				}
				else {
					$cur_maillon = $sweep_line_beginning;
					while ( ($cur_maillon != null) &&
					 ($new_maillon->y_position >= y_position_of( $cur_maillon, $x_position )) ) {
						if ($new_maillon->y_position > y_position_of( $cur_maillon, $x_position )) {
							$add_after_this_maillon = $cur_maillon;
						}
						elseif ($new_maillon->y_position == $cur_maillon->y_position) {
							if ($new_maillon->segment->get_slope() >
							 $cur_maillon->segment->get_slope()) {
								break;
							}
							elseif ($new_maillon->segment->get_slope() ==
							 $cur_maillon->segment->get_slope()) {
								//TODO : case of merged segment
							}
							
							else {
								$add_after_this_maillon = $cur_maillon;
							}
						}
						$cur_maillon = $cur_maillon->next;
					}
					Maillon::add_after( $new_maillon, $add_after_this_maillon );
				}
			}
		}
		elseif (isset( $event->position ) && ($event->position == 'right')) {
			$seg_s_event = $event->segment;
			
			//get the above and below segment:
			$above_seg = get_above_segment( $seg_s_event, $x_position );
			$below_seg = get_below_segment( $seg_s_event, $x_position );
			
			//if merged segment found
			//link them to the point
			//TODO
			
			//remove $seg_s_event from $sweep_line:
			$seg_s_event->maillon->remove();
			
			//if above and below intersect not already found
			treat_intersection_possibility( $below_seg, $above_seg, &$events_to_treat, &$intersections_pts );
		}
		else { //intersection event
			$output_list[ ] = $event;
			
			$segments = $event->segment;
			$first_maillon = order_maillon_by_slope_at_one_point( $segments, $event );
			if ($first_maillon->previous == null) {
				$sweep_line_beginning = $first_maillon;
			}
		}
		
		//find the 2 segments below and above of $lower and $higher segment
		//at this point
		//if there are not other events at the same point
		if (($last_event_at_coord == true) &&
		 ! (isset( $event->position ) && ($event->position == 'right'))) {
			$segment = $event->segment;
			
			$above_seg = get_above_segment( $segment, $x_position );
			$below_seg = get_below_segment( $segment, $x_position );
			
			//if merged segment found
			//link them to the point
			//TODO
			
			if ($above_seg != null) {
				$higher_segment = $above_seg->maillon->previous->segment;
				treat_intersection_possibility( $higher_segment, $above_seg, &$events_to_treat, &$intersections_pts );
			}
			
			if ($below_seg != null) {
				$lower_segment = $below_seg->maillon->next->segment;
				treat_intersection_possibility( $below_seg, $lower_segment, &$events_to_treat, &$intersections_pts );
			}
		}
	}
	return $output_list;
}

function y_position_of(Maillon $maillon, $at_x) {
	$seg = $maillon->segment;
	if (! isset( $seg->x_position ) || ($seg->x_position != $at_x)) {
		$seg->y_position = $seg->get_slope() * $at_x + $seg->get_y_intercept();
		$seg->x_position = $at_x;
	}
	$maillon->y_position = $seg->y_position;
	return $seg->y_position;
}

function treatment_of_vertical_segment($event, $sweep_line_beginning, $events_to_treat, $x_position, &$output_list) {
	$cur_maillon = $sweep_line_beginning;
	$y_max = $event->segment->get_right()->y;
	$y_min = $event->y;
	while ( ($cur_maillon != null) && (y_position_of( $cur_maillon, $x_position ) <= $y_max) ) {
		$y_position = y_position_of( $cur_maillon, $x_position );
		//if the segment intersect the vertical segment:
		if ($y_position >= $y_min) {
			$new_intersection = new Point( $x_position, $y_position );
			$new_intersection->segment[ ] = $cur_maillon->segment;
			$output_list[ ] = $new_intersection;
		}
		$cur_maillon = $cur_maillon->next;
	}
	
	//check the event to see if other intersections possible
	foreach ( $events_to_treat as $value ) {
		if ($value->y > $y_max) {
			break;
		}
		if ($value->y >= $y_min) {
			$new_intersection = clone $value;
			$new_intersection->segment[ ] = $value->segment;
			$output_list[ ] = $new_intersection;
		}
	}
}

function get_above_segment($segment, $x_position) {
	//return ($cur_maillon->next != null) ? $cur_maillon->next->segment : null;
	$maillon_test = $segment->maillon->next;
	$cur_maillon = $segment->maillon;
	while ( ($maillon_test != null) &&
	 (y_position_of( $cur_maillon, $x_position ) == y_position_of( $maillon_test, $x_position )) ) {
		$maillon_test = $maillon_test->next;
	}
	if ($maillon_test == null) {
		return null;
	}
	else {
		return $maillon_test->segment;
	}
}

function get_below_segment($segment, $x_position) {
	//$maillon_test = ($cur_maillon->previous != null) ? $cur_maillon->previous->segment : null;
	$maillon_test = $segment->maillon->previous;
	$cur_maillon = $segment->maillon;
	while ( ($maillon_test != null) &&
	 (y_position_of( $cur_maillon, $x_position ) == y_position_of( $maillon_test, $x_position )) ) {
		$maillon_test = $maillon_test->previous;
	}
	if ($maillon_test == null) {
		return null;
	}
	else {
		return $maillon_test->segment;
	}
}

function treat_intersection_possibility($below_seg, $above_seg, $events_to_treat, $intersections_pts) {
	if (($below_seg == null) || ($above_seg == null)) {
		return;
	}
	if ((isset( $above_seg->intersect_with )) && (in_array( $below_seg, $above_seg->intersect_with ))) {
		return;
	}
	
	$intersect_result = $below_seg->find_intersection_with( $above_seg, true );
	if ($intersect_result[0] === true) {
		//is an intersect point exist with this coordinates
		$result = is_point_in_array( $intersect_result[1], $intersections_pts );
		$result->segment[ ] = $below_seg;
		$result->segment[ ] = $above_seg;
		insert_intersect_point_in_array( $result, &$events_to_treat );
		add_links_of_intersect_segments( $below_seg, $above_seg );
	}
	/*
	elseif ($intersect_result[0] == "merged") {
		//TODO
	}*/

//the duplicate segment must be removed before treatment
/*elseif ($intersect_result[0] == "same") {
		//TODO
	}*/
}

function is_point_in_array(Point $pt, $array) {
	foreach ( $array as $value ) {
		if ($pt->same_coord_as( $value ))
			return $value;
	}
	return $pt;
}

function add_links_of_intersect_segments($seg1, $seg2) {
	$seg1->intersect_with[ ] = $seg2;
	$seg2->intersect_with[ ] = $seg1;
}

//the point must be insert in x order
//then in y order
//if already points with same coordinate
// the point will be insert after the right end points 
// and before the left end point
function insert_intersect_point_in_array(Point $intersect_result, $events_to_treat) {
	$offset = 0;
	foreach ( $events_to_treat as $value ) {
		if ($intersect_result->x < $value->x) {
			break;
		}
		else if ($intersect_result->x == $value->x) {
			if ($intersect_result->y < $value->y) {
				break;
			}
			elseif ($intersect_result->y == $value->y) {
				if ($value->position == "left") {
					break;
				}
			}
		}
		$offset ++;
	}
	array_splice( $events_to_treat, $offset, 0, "empty" );
	$events_to_treat[$offset] = $intersect_result;
}

function order_maillon_by_slope_at_one_point($segments, $pt) {
	//order the segments at this point:
	foreach ( $segments as $value ) {
		if (isset( $value->maillon )) {
			$maillon = $value->maillon;
			break;
		}
	}
	$cur_maillon = $maillon;
	//find the maillon before the first segment in the chain link to $pt:
	while ( in_array( $cur_maillon->previous->segment, $segments ) ) {
		$cur_maillon = $cur_maillon->previous;
	}
	$first_maillon = $cur_maillon;
	
	//find the maillon after the last segment in the chain link to $pt:
	while ( in_array( $cur_maillon->next->segment, $segments ) ) {
		$cur_maillon = $cur_maillon->next;
	}
	$last_maillon = $cur_maillon;
	
	//invert all the maillon with one segment in  $segments:
	//previous_maillon:
	Maillon::invert_between( $first_maillon, $last_maillon );
	
	return $last_maillon;
}





				