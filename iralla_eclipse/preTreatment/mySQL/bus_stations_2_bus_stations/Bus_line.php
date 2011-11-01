<?php

class Bus_line{
	public $id;
	public $name;
	public $path;
	public $extract_part;
	
	public function __construct($id, $name, $path){
		$this->id = $id;
		$this->name = $name;
		$this->path = $path;
	}
}

?>