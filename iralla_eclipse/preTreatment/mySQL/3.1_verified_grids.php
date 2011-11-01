<?php
require_once 'access_to_db.php';

		$bus_lines_db = $bdd->query("
			SELECT 
				id
			FROM 
				bus_lines
		");

while($bus_line = $bus_lines_db->fetch()){
	
	$squares_db = $bdd->query("
		SELECT
			from_square.id AS from_square_id,
			from_square.path AS from_square_path,
			from_square.bus_line_id,
			
			to_square.id AS to_square_id,
			to_square.path AS to_square_path
			
		FROM
			from_square, to_square
			
		HAVING
			from_square.bus_line_id = $bus_line[id]
		AND from_square_id = to_square_id
	");
	
	$compt = 0;
	while($squares = $squares_db->fetch()){
		
		$from_square_path = json_decode($squares[from_square_path]);
		$length_of_from_square_path = count($from_square_path);
		$to_square_path = json_decode($squares[to_square_path]);	
		
		$previous_link = $to_square_path[0];
		$next_link = $from_square_path[$length_of_from_square_path];
		
		if(($previous_previous_link != null)
		&& ($previous_link != $next_link)){
			
			if($previous_previous_link != $previous_link){
				echo "error on previous_link \n";
			}
			if(previous_next_link != $next_link){
				echo "error on next link \n";
			}
			
			$compt = 0;
			
		}
		else{
			$compt++;
			echo "compt = $compt \n";
		}
		
		$previous_previous_link = $previous_link;
		$previous_next_link = $next_link;
	}
	
}

function verified_squares($to_square_list, $from_square_list){
	$compt = 0;
	foreach ($to_square_list as $key => $to_square) {
		$from_square = $from_square_list[key];
		
		$from_square_path = json_decode($from_square[path]);
		$length_of_from_square_path = count($from_square_path);
		$to_square_path = json_decode($to_square[path]);
		
		$previous_link = $to_square_path[0];
		$next_link = $from_square_path[$length_of_from_square_path];
		
		if(($previous_previous_link != null)
		&& ($previous_link != $next_link)){
			
			if($previous_previous_link != $previous_link){
				echo "error on previous_link \n";
			}
			if(previous_next_link != $next_link){
				echo "error on next link \n";
			}
			
			$compt = 0;
			
		}
		else{
			$compt++;
			echo "compt = $compt \n";
		}
		
		$previous_previous_link = $previous_link;
		$previous_next_link = $next_link;
	}
	
}

?>