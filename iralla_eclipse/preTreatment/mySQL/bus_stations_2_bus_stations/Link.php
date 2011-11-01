<?php
//definition of the class link:
/*
 * link between a bus station a lat lng and a bus line
 */
class Link{
	public $id;
	public $previous_vertex_index;
	public $distance_from_previous_vertex;
	public $lat; //position on the busline
	public $lng; //position on the busline
	public $bus_station_id;
	
	public function __construct($id, $previous_vertex_index, $distance_from_previous_vertex, $lat, $lng, $bus_station_id){
		$this->id = $id;
		$this->previous_vertex_index = $previous_vertex_index;
		$this->distance_from_previous_vertex = $distance_from_previous_vertex;
		$this->lat = $lat;
		$this->lng = $lng;
		$this->bus_station_id = $bus_station_id;
	}
}

?>