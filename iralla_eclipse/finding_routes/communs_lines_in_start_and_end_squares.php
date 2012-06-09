<?php
function communs_lines_in_start_and_end_squares(Point $start,Point $end){
	$start_f_and_l_squares = $start->first_and_last_squares;
	$end_f_and_l_squares = $end->first_and_last_squares;
	
	$current_start_f_and_l_square = $start_f_and_l_squares[0];
	$current_end_f_and_l_square = $end_f_and_l_squares[0];
	$i_start = 0;
	$i_end = 0;
	$first_i_end_to_match_with_start_f_and_l_square = null;
	
	
	//extract all bl with bl_ids in start and end square
	
	
	
	
	//for each bl exctracted
	$bls_ids_to_calculate = array();
	$bl_id_to_calculate = 0;
	$i = $j = $k = 0;
	while($bl_id_to_calculate = $bls_ids_to_calculate[$i++]){
		A:while($start_f_and_l_squares[$j++]['first']['bl_id'] < $bl_id_to_calculate);
		$first_k_for_this_bl_id = $k;
		B:while($end_f_and_l_squares[$k++]['first']['bl_id'] < $bl_id_to_calculate);
	
	
		if($end_f_and_l_squares['first']['bl_id'] == $bl_id_to_calculate){
			goto B;
		}		
		if($start_f_and_l_squares['first']['bl_id'] == $bl_id_to_calculate){
			$k = $first_k_for_this_bl_id;
			goto A;
		}
	}
	
	
	while($current_start_f_and_l_square != null) {
		//bl_ids was ordered in the mySQL request
		while($current_end_f_and_l_square &&
		 ($current_end_f_and_l_square['first']['bl_id'] < $current_start_f_and_l_square['first']['bl_id']) ){
			
			$i_end++;
			$current_end_f_and_l_square = $end_f_and_l_squares[$i_end];
		}
		
		//in case start bl_id is the same that the previous start bl_id
		//and at least one square had the same bl_id
		if(($first_i_end_to_match_with_start_f_and_l_square != null)
				
			&& ($end_f_and_l_squares[$first_i_end_to_match_with_start_f_and_l_square]['first']['bl_id'] 
				== $current_start_f_and_l_square['first']['bl_id'])){
			
			//reinit $i_end to the first end_f_and_l_square with the same bl_id
			$i_end = $first_i_end_to_match_with_start_f_and_l_square;
		}
		
		
		$first_i_end_to_match_with_start_f_and_l_square = $i_end;
		
		while(($current_end_f_and_l_square != null) &&
		 ($current_end_f_and_l_square['first']['bl_id'] == $current_start_f_and_l_square['first']['bl_id']) ){
			
			$elts_to_calculate_road = array();
			$elts_to_calculate_road['start_square'] = $current_start_f_and_l_square;
			$elts_to_calculate_road['end_square'] = $current_end_f_and_l_square;
			
			$i_end++;
			$current_end_f_and_l_square = $end_f_and_l_squares[$i_end];
		}
		
		$i_start++;
		$current_start_f_and_l_square = $start_f_and_l_squares[$i_start];
	}
	
	//extract the bus lines needes from the data base
	$start->projection_on_polyline_between($p, $first_index, $last_index);
	
	//calculate the nearest point
	
	//extract the road
	
}


