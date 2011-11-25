<?php
include_once 'Point.php';
include_once 'Segment.php';
include_once 'Maillon.php';

//TODO : gestion du premier mailllon de la chaine 


//TODO : add maillon at the end of the chain not managed


function inter_segments(array $segments) {
	$output_list = array();
	
	//make a list of ends points with reference to each segment endpoints
	$events_to_treat = array();
	foreach ( $segments as $segment ) {
		$events_to_treat = array_merge( $events_to_treat, $segment->get_pts_as_array() );
	}
	
	usort( $events_to_treat, "cmp_local" );
	
	$intersections_list = array();
	$sweep_line = array();
	//while $intersections_list is not empty
	while ( isset( $events_to_treat[0] ) == true ) {
		//determine the next event point in $end_pts_to_treat
		$event = array_shift( $events_to_treat );
		
		//$flag if other $event at same coordinates:
		if((isset($events_to_treat[0])) && ($event->same_coord_as($events_to_treat[0]))){
			$last_here = false;
		}
		else{
			$last_here = true;
		}
		
		if (isset( $event->position ) && ($event->position == 'left')) {
			
			$seg_s_event = $event->segment;
			$seg_s_event->y_postion = $seg_s_event->get_left_pt()->y;
			$x_sweep_line = $seg_s_event->get_left_pt()->x;
			
			//insert the segment in the list depending of its y position:
			if (! isset( $sweep_line[0] )) {
				$sweep_line[ ] = $seg_s_event;
			}
			else {
				//1)calculate y position on current sweepline of each segment
				array_walk( $sweep_line, "calculate_y_position", $x_sweep_line );
				
				//2)add to sweep line
				$sweep_line[ ] = $seg_s_event;
				
				//3)order
				usort( $sweep_line, "cmp_to_have_y_order" );

				//get the above and below segment:
				$above_seg = get_above_segment( $sweep_line, $seg_s_event );
				$below_seg = get_below_segment( $sweep_line, $seg_s_event );
				
				//calcul intersections with $seg_s_event if any:
				treat_intersection_possibility( $below_seg, $seg_s_event, &$events_to_treat );
				treat_intersection_possibility( $seg_s_event, $above_seg, &$events_to_treat );
			}
		}
		elseif (isset( $event->position ) && ($event->position == 'right')) {
			$seg_s_event = $event->segment;
			
			//get the above and below segment:
			$above_seg = get_above_segment( $sweep_line, $seg_s_event );
			$below_seg = get_below_segment( $sweep_line, $seg_s_event );
			
			//remove $seg_s_event from $sweep_line:
			$sweep_line[$seg_s_event->key] = null;
			
			//if above and below intersect not already found
			treat_intersection_possibility( $below_seg, $above_seg, &$events_to_treat );
		}
		else { //intersection event
			$output_list[ ] = $event;
			$seg1 = $event->segment[0];
			$seg2 = $event->segment[1];
			
			//find the 2 segments below and above of $seg 1 and 2
			$below_seg = get_above_segment( $sweep_line, $seg1 );
			$above_seg = get_below_segment( $sweep_line, $seg1 );
			
			//swap theirs positions:
			//$seg1->maillon->swap_with( $seg2->maillon, &$sweep_line_beginning );
			
			treat_intersection_possibility( $below_seg, $seg1, &$events_to_treat );
			treat_intersection_possibility( $seg2, $above_seg, &$events_to_treat );
		}
	}
	return $output_list;
}

//order the endpoints_to_treat by x-axis, y-axis value of end point
function cmp_local($pt1, $pt2) {
	if ($pt1->same_coord_as( $pt2 )) {
		if ($pt1->position == "left") {
			return - 1;
		}
		elseif ($pt2->position == "left") {
			return 1;
		}
		else {
			return 0;
		}
	}
	if (($pt1->x < $pt2->x) || (($pt1->x == $pt2->x) && ($pt1->y < $pt2->y))) {
		return - 1;
	}
	else {
		return 1;
	}
}

function calculate_y_position(Segment $item, $index, $x) {
	$item->y_position = $item->flope * $x + $item->y_intercept;
	$item->key = $index;
}
function cmp_to_have_y_order(Segment $seg1, Segment $seg2) {
	if ($seg1->y_position == $seg2->y_position) {
		return 0;
	}
	if ($seg1->y_position < $seg2->y_position) {
		return - 1;
	}
	else {
		return 1;
	}
}

function get_above_segment($array, $segment, $last_segment) {
	$origin_key = $segment->key;
	$cur_segment = $segment;
	for($i = $origin_key; ($cur_segment != $last_segment); $i ++) {
		$cur_segment = $array[$i];
		if ($cur_segment->y_position != $segment->y_position) {
			break;
		}
	}
	if ($cur_segment->y_position != $segment->y_position) {
		return $cur_segment;
	}
	else {
		return false;
	}
}
function get_below_segment($array, $segment, $first_segment) {
	$origin_key = $segment->key;
	$cur_segment = $segment;
	for($i = $origin_key; ($cur_segment != $first_segment); $i --) {
		$cur_segment = $array[$i];
		if ($cur_segment->y_position != $segment->y_position) {
			break;
		}
	}
	if ($cur_segment->y_position != $segment->y_position) {
		return $cur_segment;
	}
	else {
		return false;
	}
}

function treat_intersection_possibility($below_seg, $above_seg, $events_to_treat) {
	if (($below_seg == null) || ($above_seg == null)) {
		return;
	}
	if ((isset( $above_seg->intersect_with )) && (in_array( $below_seg, $above_seg->intersect_with ))) {
		return;
	}
	
	$intersect_result = $below_seg->find_intersection_with( $above_seg, true );
	if ($intersect_result[0] === true) {
		$intersect_result[1]->segment[ ] = $below_seg;
		$intersect_result[1]->segment[ ] = $above_seg;
		insert_intersect_point_in_array( $intersect_result, &$events_to_treat );
		add_links_of_intersect_segments( $below_seg, $above_seg );
	}
	elseif ($intersect_result[0] == "merged") {
		//TODO
	}
	elseif ($intersect_result[0] == "same") {
		//TODO
	}
}

function add_links_of_intersect_segments($seg1, $seg2) {
	$seg1->intersect_with[ ] = $seg2;
	$seg2->intersect_with[ ] = $seg1;
}

function insert_intersect_point_in_array($intersect_result, $events_to_treat) {
	$offset = 0;
	foreach ( $events_to_treat as $value ) {
		if ($intersect_result[1]->x <= $value->x) {
			break;
		}
		$offset ++;
	}
	//$events_to_treat[$offset] = $intersect_result[1];/////////////////TO DO
	array_splice( $events_to_treat, $offset, 0, "empty" );
	$events_to_treat[$offset] = $intersect_result[1];
}

function get_seg_s_event_key($sweep_line, $seg2) {
	foreach ( $sweep_line as $key => $value ) {
		if ($value["seg"] == $seg_s_event) {
			return $key;
		}
	}
	die( "ERROR 4392" );
}	



