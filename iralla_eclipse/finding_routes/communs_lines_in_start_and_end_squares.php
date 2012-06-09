<?php
function communs_lines_in_start_and_end_squares(Point $start,Point $end){
	global $bdd;
	
	$start_f_and_l_squares = $start->first_and_last_squares;
	$end_f_and_l_squares = $end->first_and_last_squares;
	
	//extract bl_ids list:
	$bl_ids_list = $start_f_and_l_squares[0]['first']['bl_id'];
	$i = 1;
	$j = 0;
	while ($start_bl_id = $start_f_and_l_squares[$i]['first']['bl_id']){
		while ($start_f_and_l_squares[$j]['first']['bl_id'] < $start_f_and_l_squares[$i]['first']['bl_id']){
			$j++;
		}
		if($start_f_and_l_squares[$j]['first']['bl_id'] == $start_f_and_l_squares[$i]['first']['bl_id']){
			$bl_ids_list = ' OR id = ' . $start_f_and_l_squares[$j]['first']['bl_id'];
		}
		$i++;
	}
	
	//extract bus lines:
	$query =  file_get_contents('extract_bus_lines_depend_on_where_search.sql');
	$req = $bdd->query($query);
	
	//for each bl exctracted
	$bl_id_to_calculate = 0;
	$j = $k = 0;
	
	while($bus_line_datas = $req->fetch()){
	$bl_id_to_calculate = $bus_line_datas['id'];

		A:while($start_f_and_l_squares[$j++]['first']['bl_id'] < $bl_id_to_calculate);
		$first_k_for_this_bl_id = $k;
		B:while($end_f_and_l_squares[$k++]['first']['bl_id'] < $bl_id_to_calculate);
			
			//create bus line object:
			$bl = new Busline($bus_line_datas['path'], $bus_line_datas['name']);
			$bl->flow = $bus_line_datas['flow'];
			
			//calculate nearest point on the bus line from the start point
			$start->projection_on_polyline_between_two_squares($bl, $start_f_and_l_squares['first'], $start_f_and_l_squares['last']);
			
			//calculate nearest point on the bus line from the end point
			$end->projection_on_polyline_between_two_squares($bl, $start_f_and_l_squares['first'], $start_f_and_l_squares['last']);
			
			//calculate the road
			$results[] = caculate_road_on_one_bus_line(
					$bl,
					$start,
					$end);
	
		if($end_f_and_l_squares['first']['bl_id'] == $bl_id_to_calculate){
			goto B;
		}		
		if($start_f_and_l_squares['first']['bl_id'] == $bl_id_to_calculate){
			$k = $first_k_for_this_bl_id;
			goto A;
		}
	}
	
	
	return $results;
	
}


