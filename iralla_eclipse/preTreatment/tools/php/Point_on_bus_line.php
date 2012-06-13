<?php
class Point_on_bus_line extends Point{
	public $bus_line;
	public $previous_index;
	public $distance_from_previous_index;
	public $bus_station;
	public $distance_from_first_vertex ;
	
	public function distance_from_first_vertex_with_square_infos($f_and_l_square){
		//TODO revoir la base de donnee pour simplifer se calcul
		//ie connaitre la distance de chaque vertex a partir du debut
		$bl = $this->bus_line;
		$square_pt = new Point(
				$f_and_l_square['first']['pt_coords_lng'],
				$f_and_l_square['first']['pt_coords_lat']);
		$previous_index_of_square = (int) $f_and_l_square['first']['prev_index_of_pt'];
		$previous_vertex_pt = $bl->get_point_at($previous_index_of_square);
	
		$distance_from_first_to_previous_vertex_of_square =
		$f_and_l_square['first']['distance_from_first_vertex']
		-
		$square_pt->earth_distance_to($previous_vertex_pt);
	
		$previous_index_of_this = $this->$previous_index;
	
		$this->distance_from_first_vertex =
		$distance_from_first_to_previous_vertex_of_square
		+
		$bl->calculate_distance_between_2_vertex($previous_index_of_square, $previous_index_of_this)
		+
		$this->distance_from_previous_index;
	}
	
	public function __construct(
			$x,
			$y,
			$bus_line,
			$previous_index,
			$distance_from_previous_index,
			$bus_station = "none"){
		
		parent::__construct($x,$y);
		
		$this->bus_line = $bus_line;
		$this->previous_index = $previous_index;
		$this->distance_from_previous_index = $distance_from_previous_index;
		$this->bus_station = $bus_station;	
	}
}
