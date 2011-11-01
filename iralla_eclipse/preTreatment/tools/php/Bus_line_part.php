<?php


class Bus_line_part{
	public $id;
	public $name;
	public $vertex_list = array();
	public $go_in;
	public $go_out;
	public $next_link = NULL;
	public $path_to_next_link = NULL;
	public $previous_link = NULL;
	public $path_to_previous_link = NULL;
	
	
	public function __construct($bus_line){
		$this->id = $bus_line[bus_line_id];
		$this->name = $bus_line[bus_line_name];
		$this->go_in = new Vertex("0 0", NULL);
		$this->go_out = new Vertex("0 0", NULL);
	}
}

