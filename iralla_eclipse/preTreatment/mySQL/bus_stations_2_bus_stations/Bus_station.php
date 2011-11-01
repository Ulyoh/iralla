<?php
class Bus_station{
	public $id;
	public $name;
	public $lat;
	public $lng;
	public $type;
	
	public function __construct($id, $name, $lat, $lng, $type){
		$this->id = $id;
		$this->name = $name;
		$this->lat = $lat;
		$this->lng = $lng;
		$this->type = $type;
	}
}
