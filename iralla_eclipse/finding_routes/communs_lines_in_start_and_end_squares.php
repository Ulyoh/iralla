<?php
function communs_lines_in_start_and_end_squares($start_squares, $end_squares){
	$current_start_square = $start_squares[0];
	$current_end_square = $end_squares[0];
	$i = 0;
	/**
	 * TODO ameliorer la boucle ci dessous
	 * (the bl_ids are in order from the mySQL search)
	 */
	while($current_start_square) {
		while($current_end_square && ($current_end_square['bl_id'] < $current_start_square['bl_id']) ){
			$i += 2;
			$current_square = $end_squares[$i];
		}
		if($current_end_square['bl_id'] == $current_start_square['bl_id']){
			//calcul the nearest point from start point
			//add infos needed to calcul the road to $start_pt_on_bl
			
			//calcul the nearest point from end point
			//add infos needed to calcul the road to $end_pt_on_bl
			
			
			$results[] = bus_line_from_to($start_pt_on_bl, $end_pt_on_bl);
		}
		
		$i += 2;
		$current_start_square = $start_squares[$i];
	}
}