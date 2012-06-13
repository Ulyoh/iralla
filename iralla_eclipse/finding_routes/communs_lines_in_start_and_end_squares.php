<?php
function communs_lines_in_start_and_end_squares(Point $start,Point $end){
	global $bdd;
	
	$start_f_and_l_squares = $start->first_and_last_squares;
	$start_f_and_l_squares_length = count($start_f_and_l_squares);
	$end_f_and_l_squares = $end->first_and_last_squares;
	$end_f_and_l_squares_length = count($end_f_and_l_squares);
	
	//extract bl_ids list:
	$i = 0;
	$j = 0;
	$bl_ids_list = '';
	while (($i < $start_f_and_l_squares_length) && ($start_bl_id = $start_f_and_l_squares[$i]['first']['bl_id'])){
		while (( $j < $end_f_and_l_squares_length) && ($end_f_and_l_squares[$j]['first']['bl_id'] < $start_bl_id)){
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
		select id, path, flows, name, type
		from bus_lines
		where ' . $bl_ids_list .'
		order by id';
	$req = $bdd->query($query);
	
	$bl_id_to_calculate = 0;
	$j = $k = -1;
	$results = array();
	
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
		$bl = new Busline(extract_path_from_string_to_points($bus_line_datas['path']), $bus_line_datas['name'], $bus_line_datas['type']);
		$bl->flow = $bus_line_datas['flows'];
		
		//create a road part:
		if($road_part = Road_part::create_road_part_from_points_out_of_bus_line(
			$start, 
			$start_f_and_l_square, 
			$end, 
			$end_f_and_l_square, 
			$bl,
			"normal")){
			//add the road part to the road result:
			
		}
		
		if($road_part = Road_part::create_road_part_from_points_out_of_bus_line(
				$start,
				$start_f_and_l_square,
				$end,
				$end_f_and_l_square,
				$bl,
				"reverse")){
				//add the road part to the road result:
			
		}
		
		($start, $end, $start_pt_on_bl, $end_pt_on_bl, $speeds_parameters);
		
		//save the shortest result with the distance 
		//(max shortest time + 5 minutes ou if shortest time < 25 min, shortest time + 20%)
		//max result : 20
		//calculate the distance(s)
		
		
		//
		
		//save the results:
		$results[] = array(
				'distance'=> $distance,
				'busline'=>bl,)
		$distance
		$bl
		$start_pt_on_bl
		$end_pt_on_bl
		busline name
		
		time from start to bl
		$start_pt_on_bl
		time from bl to end
		$end_pt_on_bl
		busline name
		

		}
		
		if(($k < $end_f_and_l_squares_length - 1) && ($end_f_and_l_squares[$k+1]['first']['bl_id'] == $bl_id_to_calculate)){
			goto B;
		}		
		if(($j < $start_f_and_l_squares_length - 1) && ($start_f_and_l_squares[$j+1]['first']['bl_id'] == $bl_id_to_calculate)){
			$k = $first_k_for_this_bl_id;
			goto A;
		}
	}
	
	
	return $results;
	
}


