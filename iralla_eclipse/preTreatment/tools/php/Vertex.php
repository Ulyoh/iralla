<?php
class Vertex{
	public $lat;
	public $lng;
	
	public static function are_egal($vertex_1, $vertex_2){
		if(($vertex_1->lat == $vertex_2->lat) 
		&& ($vertex_1->lng == $vertex_2->lng)){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function __construct($lat_lng_string_or_lat, $lng_optional){
		global $multipicador;
		if($multipicador == null){
			$multipicador = 10000000;
		}
			
		if ((is_string($lat_lng_string_or_lat) == true)
		&& ( $lng_optional == NULL)){
			$buffer = explode(" ", $lat_lng_string_or_lat);
			$this->lat = bcmul($buffer[0], $multipicador);
			$this->lng = bcmul($buffer[1], $multipicador);
		}
		else{
			$this->lat = bcmul($lat_lng_string_or_lat, $multipicador);
			$this->lng = bcmul($lng_optional, $multipicador);
		}
	}
	
	public function get_lat(){
		global $multipicador;
		if($multipicador == null){
			$multipicador = 10000000;
		}
		return bcdiv($this->lat, $multipicador, 9);
	}
	
	public function get_lng(){
		global $multipicador;
		if($multipicador == null){
			$multipicador = 10000000;
		}
		return bcdiv($this->lng, $multipicador, 9);
	}
}

?>