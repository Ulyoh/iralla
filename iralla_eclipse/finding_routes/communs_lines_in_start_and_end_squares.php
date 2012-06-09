<?php
function communs_lines_in_start_and_end_squares($start, $end){
	$start_f_and_l_squares = $start->first_and_last_squares;
	$end_f_and_l_squares = $end->first_and_last_squares;
	
	$current_start_f_and_l_square = $start_f_and_l_squares[0];
	$current_end_f_and_l_square = $end_f_and_l_squares[0];
	$i_start = 0;
	$i_end = 0;
	
	while($current_start_f_and_l_square) {
		//bl_ids was ordered in the mySQL request
		while($current_end_f_and_l_square &&
		 ($current_end_f_and_l_square['first']['bl_id'] < $current_start_f_and_l_square['first']['bl_id']) ){
			
			$i_end++;
			$current_end_f_and_l_square = $end_f_and_l_squares[$i_end];
		}
		while($current_end_f_and_l_square &&
		 ($current_end_f_and_l_square['first']['bl_id'] == $current_start_f_and_l_square['first']['bl_id']) ){
			
			//calcul the nearest point from start point
			//add infos needed to calcul the road to $start_pt_on_bl
			$first_square = $start_f_and_l_squares[$i_start];
			$last_square = $start_f_and_l_squares[$i_start + 1];
			
			//calcul the nearest point from end point
			//add infos needed to calcul the road to $end_pt_on_bl
			
			
			$results[] = bus_line_from_to($start_pt_on_bl, $end_pt_on_bl);
			
			$i_end++;
			$current_end_f_and_l_square = $end_f_and_l_squares[$i_end];
		}
		
		$i_start++;
		$current_start_f_and_l_square = $start_f_and_l_squares[$i_start];
	}
}