<?php
class Vertex{
	public $lat;
	public $lng;
	public $with_multiplicador;
	
	public static function are_egal($vertex_1, $vertex_2){
		if(($vertex_1->lat == $vertex_2->lat) 
		&& ($vertex_1->lng == $vertex_2->lng)){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function __construct($lat_lng_string_or_lat, $lng_optional, $with_multiplicador = true){
		global $multipicador;
		$this->with_multiplicador = $with_multiplicador;
		if($multipicador == null){
			$multipicador = 10000000;
		}
			
		if ((is_string($lat_lng_string_or_lat) == true)
		&& ( $lng_optional == NULL)){
			$buffer = explode(" ", $lat_lng_string_or_lat);
			if ($this->with_multiplicador === true){
				$this->lat = bcmul($buffer[0], $multipicador);
				$this->lng = bcmul($buffer[1], $multipicador);
			}
			else{
				$this->lat = $buffer[0];
				$this->lng = $buffer[1];
			}
		}
		else{
			if ($this->with_multiplicador === true){
				$this->lat = bcmul($lat_lng_string_or_lat, $multipicador);
				$this->lng = bcmul($lng_optional, $multipicador);
			}
			else{
				$this->lat = $lat_lng_string_or_lat;
				$this->lng = $lng_optional;
			}
		}
	}
	
	public function get_lat(){
		if ($this->with_multiplicador === true){
			global $multipicador;
			if($multipicador == null){
				$multipicador = 10000000;
			}
			return bcdiv($this->lat, $multipicador, 9);
		}
		else{
			return $this->lat;
		}
	}
	
	public function get_lng(){
		if ($this->with_multiplicador === true){
			global $multipicador;
			if($multipicador == null){
				$multipicador = 10000000;
			}
			return bcdiv($this->lng, $multipicador, 9);
		}
		else{
			return $this->lng;
		}
	}
}

?>