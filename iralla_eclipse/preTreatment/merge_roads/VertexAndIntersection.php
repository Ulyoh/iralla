<?php
/**
 * @author Yohann MERIENNE <yohann@gmail.com>
 */

/** ***************************************** **
 * 
 * 		class: Vertex_and_intersection
 * 
 ** ***************************************** **
 * 
 * define a "link" between a vertex of a busline and a point
 * on an other bus line
 * 
 * all the Vertex_and_intersection with the same vertex have the
 * same node_id value.
 * 
 * once created none of its parameters can be change
 * 
 * public methodes:
 * 
 * non-static:
 * 	get_id() 										return integer
 *	get_node_id() 									return integer
 *	get_bus_line_id_of_origin_vertex() 				return integer
 *	get_index_of_origin_vertex()					return integer
 * 	get_bus_line_id_of_intersection()				return integer
 * 	get_index_of_previous_vertex_of_intersection()	return integer
 * 	get_intersection_coordinate()					return Lat_lng
 * 	get_distance_to_previous_vertex()				return float
 * 
 */

class Vertex_and_intersection{

	//static parameters:
	private static $next_id = 0;
	private static $list_by_id = array();
	private static $list_by_node_id = array();
	private static $next_node_id = 0;
	private static $node_ids_list = array(); //$node_ids_list[bus_line_id][vertex_index][]

	//local parameters:
	private $id;
	private $node_id;
	private $bus_line_id_of_origin_vertex;
	private $index_of_origin_vertex;
	private $bus_line_id_of_intersection;
	private $index_of_previous_vertex_of_intersection;
	private $intersection_coordinates;
	private $distance_to_previous_vertex;

	//static methodes:
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $bus_line_id
	 * @param unknown_type $vertex_index
	 * @param unknown_type $interval
	 * @param unknown_type $bus_line_list
	 */
	public static function find_all_intersections_from_vertex(
	$bus_line_id, $vertex_index, $interval, $bus_line_list){
		
	}
	
	//non-static methodes
	private function remove_from_all_list(){
		unset(Vertex_and_intersection::$list_by_id[$this->id]);
		
		$key_to_remove = array_search(Vertex_and_intersection::$list_by_node_id, $this->node_id);
		unset(Vertex_and_intersection::$list_by_node_id[$this->node_id][$key_to_remove]);
		
		if(count(Vertex_and_intersection::$list_by_node_id[$this->node_id]) == 0){
			unset(Vertex_and_intersection::$list_by_node_id[$this->node_id]);
		}
	}
	
	public function get_id(){
		return $this->bus_line_id_of_origin_vertex;
	}
	
	public function get_node_id(){
		return $this->bus_line_id_of_origin_vertex;
	}
	
	public function get_bus_line_id_of_origin_vertex(){
		return $this->bus_line_id_of_origin_vertex;
	}

	public function get_index_of_origin_vertex(){
		return $this->index_of_origin_vertex;
	}

	public function get_bus_line_id_of_intersection(){
		return $this->bus_line_id_of_intersection;
	}
	
	public function get_index_of_previous_vertex_of_intersection(){
		return $this->index_of_previous_vertex_of_intersection;
	}
	
	public function get_intersection_coordinate(){
		return $this->intersection_coordinates;
	}
		
	public function get_distance_to_previous_vertex(){
		return $this->distance_to_previous_vertex;
	}

	/**
	 * 
	 * CONSTRUCTOR
	 * @param integer $bus_line_id_of_origin_vertex
	 * @param integer $index_of_origin_vertex
	 * @param integer $bus_line_id_of_intersection
	 * @param integer $index_of_previous_vertex_of_intersection
	 * @param Lat_lng $coordinates_of_previous_vertex_of_intersection  //TODO : find an other way to get these coordinates
	 * @param Lat_lng $intersection_coordinates
	 */
	function __construct($bus_line_id_of_origin_vertex, $index_of_origin_vertex,
	$bus_line_id_of_intersection, $index_of_previous_vertex_of_intersection,
	$coordinates_of_previous_vertex_of_intersection, $intersection_coordinates){

		$this->id = Vertex_and_intersection::$next_id++;
		$this->bus_line_id_of_origin_vertex = $bus_line_id_of_origin_vertex;
		$this->index_of_origin_vertex = $index_of_origin_vertex;
		$this->bus_line_id_of_intersection = $bus_line_id_of_intersection;
		$this->index_of_previous_vertex_of_intersection = $index_of_previous_vertex_of_intersection;
		$this->intersection_coordinates = $intersection_coordinates;
		$this->distance_to_previous_vertex = real_distance_between_2_vertex(
		$coordinates_of_previous_vertex_of_intersection, $intersection_coordinates);
		
		//set node_id value:
		$this->node_id  = Vertex_and_intersection::$next_node_id++;
		if(!isset(Vertex_and_intersection::$node_ids_list[$bus_line_id_of_origin_vertex])){
			Vertex_and_intersection::$node_ids_list[$bus_line_id_of_origin_vertex] = array();
		}
		if(!isset(Vertex_and_intersection::$node_ids_list[$bus_line_id_of_origin_vertex][$index_of_origin_vertex])){
			Vertex_and_intersection::$node_ids_list[$bus_line_id_of_origin_vertex][$index_of_origin_vertex] = array();
			Vertex_and_intersection::$node_ids_list[$bus_line_id_of_origin_vertex][$index_of_origin_vertex][] = 
			$this->node_id ;
		}
		
		Vertex_and_intersection::$list_by_id[$this->id] = $this;
		if(!isset(Vertex_and_intersection::$list_by_node_id[$this->node_id])){
			Vertex_and_intersection::$list_by_node_id[$this->node_id] = array();
		}
		Vertex_and_intersection::$list_by_node_id[$this->node_id][] = $this;
	}

	function __destruct(){
		$this->remove_from_all_list();
	}

}
