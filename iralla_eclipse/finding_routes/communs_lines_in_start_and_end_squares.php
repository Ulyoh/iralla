<?php
function communs_lines_in_start_and_end_squares(Point $start,Point $end){
	global $bdd;
	
	$start_f_and_l_squares = $start->first_and_last_squares;
	$end_f_and_l_squares = $end->first_and_last_squares;
	
	//extract bl_ids list:
	$i = 0;
	$j = 0;
	$bl_ids_list = '';
	while (($start_f_and_l_squares[$i]) && ($start_bl_id = $start_f_and_l_squares[$i]['first']['bl_id'])){
		while ($end_f_and_l_squares[$j]['first']['bl_id'] < $start_bl_id){
			$j++;
		}
		if($end_f_and_l_squares[$j]['first']['bl_id'] == $start_bl_id){
			if ($bl_ids_list == ''){
				$bl_ids_list = 'id = ' . $start_bl_id;
			}
			else{
				$bl_ids_list .= ' OR id = ' . $start_bl_id;
			}
		}
		$i++;
	}
	
	//extract bus lines from db:
	$query =  '
		select id, path, flows, name
		from bus_lines
		where ' . $bl_ids_list .'
		order by id';
	$req = $bdd->query($query);
	
	$bl_id_to_calculate = 0;
	$j = $k = -1;
	
	//for each bl exctracted
	//calculate the nearest point from start on the bl
	//calculate the nearest point from end on the bl
	//extract the possibles roads from start to end
	while($bus_line_datas = $req->fetch()){
	$bl_id_to_calculate = $bus_line_datas['id'];

		A:while($start_f_and_l_squares[++$j]['first']['bl_id'] < $bl_id_to_calculate);
		$start_f_and_l_square = $start_f_and_l_squares[$j];
		$first_k_for_this_bl_id = $k;
		B:while($end_f_and_l_squares[++$k]['first']['bl_id'] < $bl_id_to_calculate);
			$end_f_and_l_square = $end_f_and_l_squares[$k];
			
			//create bus line object:
			$bl = new Busline(extract_path_from_string_to_points($bus_line_datas['path']), $bus_line_datas['name']);
			$bl->flow = $bus_line_datas['flows'];
			
			//calculate nearest point on the bus line from the start point
			$next_pt = ($start_f_and_l_square['last']['prev_index_of_next_link'] + 1 < $bl->get_length()) ? $start_f_and_l_square['last']['prev_index_of_next_link'] + 1 : $start_f_and_l_square['last']['prev_index_of_next_link']; 
			$start_pt_on_bl = $start->projection_on_polyline_between(
					$bl, 
					$start_f_and_l_square['first']['prev_index_of_prev_link'], 
					$next_pt);
			
			//calculate nearest point on the bus line from the end point
			$next_pt = ($end_f_and_l_square['last']['prev_index_of_next_link'] + 1 < $bl->get_length()) ? $end_f_and_l_square['last']['prev_index_of_next_link'] + 1 : $end_f_and_l_square['last']['prev_index_of_next_link']; 
			$end_pt_on_bl = $end->projection_on_polyline_between(
					$bl, 
					$end_f_and_l_square['first']['prev_index_of_prev_link'], 
					$next_pt);
			
			//calculate the road
			$results = array_merge($results, $bl->get_points_between_start_and_end_pts_on_bl($start_pt_on_bl, $end_pt_on_bl));
	
		if($end_f_and_l_square['first']['bl_id'] == $bl_id_to_calculate){
			goto B;
		}		
		if($start_f_and_l_square['first']['bl_id'] == $bl_id_to_calculate){
			$k = $first_k_for_this_bl_id;
			goto A;
		}
	}
	
	
	return $results;
	
}


