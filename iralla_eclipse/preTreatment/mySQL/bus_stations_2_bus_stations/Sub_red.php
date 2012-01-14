<?php


class Sub_red{
	
	//path to save the roads from after first and before last bus station:
	public $path_to_save;
	
	//roads arrays:
	public $roads = array();
	public $roads_by_start_bs_id = array();
	public $roads_by_end_bs_id = array();
	public $roads_of_one_bs2bs_by_start_bs_id = array();
	public $roads_of_one_bs2bs_by_end_bs_id = array();
	public $test_count = 0;
	
	//bus station to bus station arrays:
	public $bs2bss = array();
	public $bs2bss_by_start_end_bs_id = array();
	
	//link to link arrays:
	public $li2lis = array();
	public $li2lis_by_start_end_bs_id = array();
	
	//speed depend on type bus line:
	public $speeds = array();
	
	public function add_road(Road $new_road){
		$this->roads[] = $new_road;
		$this->roads_by_end_bs_id[$new_road->end_bus_station->id][$new_road->start_bus_station->id] = $new_road;
		$this->roads_by_start_bs_id[$new_road->start_bus_station->id][$new_road->end_bus_station->id] = $new_road;
	}
	
	public function add_roads(array $new_roads){
		foreach ($new_roads as $new_road){
			$this->add_road($new_road);
		}
	}
	
	public function add_li2li(Li2li $new_li2li){
		$start_id = $new_li2li->start_bus_station->id;
		$end_id = $new_li2li->end_bus_station->id;
		
		//if there is not a bs2bs with the same 
		//start and end bus station of $new_li2li exist:
		if(isset($this->bs2bss_by_start_end_bs_id[$start_id]) != true){
			$this->bs2bss_by_start_end_bs_id[$start_id] = array();
			$this->li2lis_by_start_end_bs_id[$start_id] = array();
		}
		if(isset($this->bs2bss_by_start_end_bs_id[$start_id][$end_id]) != true){
			$this->bs2bss_by_start_end_bs_id[$start_id][$end_id] = new Bs2bs($new_li2li, $this);
			$this->bs2bss[] = $this->bs2bss_by_start_end_bs_id[$start_id][$end_id];
			$this->li2lis_by_start_end_bs_id[$start_id][$end_id][] = $new_li2li;
			$this->li2lis[] = $new_li2li;
		}
		else{
			$this->bs2bss_by_start_end_bs_id[$start_id][$end_id]->add_a_li2li($new_li2li);
			$this->li2lis_by_start_end_bs_id[$start_id][$end_id][] = $new_li2li;
			$this->li2lis[] = $new_li2li;
		}
	}
	
	public function generate_roads_from_bs2bss(){
		foreach ($this->bs2bss as $bs2bs) {
			$road = new Road($bs2bs);
			$start_id = $road->start_bus_station->id;
			$end_id = $road->end_bus_station->id;
			//$this->roads[] = $road;
			//$this->roads_by_start_bs_id[$start_id][$end_id] = $road;
			//$this->roads_by_end_bs_id[$end_id][$start_id] = $road;
			$this->roads_of_one_bs2bs_by_start_bs_id[$start_id][] = $road;
			$this->roads_of_one_bs2bs_by_end_bs_id[$end_id][] = $road;
		}
	}
	
	public function find_all_roads_from_one_bs( $bus_station_id){
		echo "calculate all roads for bus station id = " . $bus_station_id ."<br \>\n";
		
		$this->roads_by_start_bs_id[$bus_station_id] = array();
		
		$possibles_roads = array();
		//$roads_from_bs = array();
		//$to_merge = array();
		
		//list of the roads from one bus station:
		$possibles_roads = $this->roads_of_one_bs2bs_by_start_bs_id[$bus_station_id];
		
		//$road_added = 0;
		do{
			//$this->test_count = $this->test_count  + 1;
			//extract the shortest road of $possibles_roads:
			$cur_road = Road::extract_shortest_road(&$possibles_roads);
			
			//if there are not already a road going to that bus station
			//and not going to the start bus station:
			if((array_key_exists($cur_road->end_bus_station->id, $this->roads_by_start_bs_id[$bus_station_id]) === false)
			&& ($cur_road->end_bus_station->id != $bus_station_id)){
				//create the roads which can extend this one:
				//if roads from that bus station:
				if(isset($this->roads_of_one_bs2bs_by_start_bs_id[$cur_road->end_bus_station->id])){
					$cur_road_cloned = clone $cur_road;
					$possibles_roads = array_merge(
						$possibles_roads,
						$cur_road_cloned->extend_with_each_of(
						$this->roads_of_one_bs2bs_by_start_bs_id[$cur_road->end_bus_station->id],
						$this)
					);
				}
				$this->add_road($cur_road);
				//$road_added++;
				//echo "road added : $road_added\n";
				
			}
		}while(count($possibles_roads) > 0);
		
		//verification:
		//all the others bus stations have to be reach only once:
		$array_of_ids = array_keys($this->roads_of_one_bs2bs_by_end_bs_id);

		//is all stations reached:
		$array_of_bus_stations_ids_not_reach = array();
		$string_of_bus_stations_ids_not_reach = "";
		foreach($array_of_ids as $id){
			if ((array_key_exists($id, $this->roads_by_end_bs_id) === false)
			&&( $id != $bus_station_id)){
				$array_of_bus_stations_ids_not_reach[] = $id;
				$string_of_bus_stations_ids_not_reach .= $id . " ";
			}
		}
		if(count($array_of_bus_stations_ids_not_reach) > 0){
			exit( "the bus station(s) with the id(s) '" . $id . "' is(are) not reached by any roads from $bus_station_id \n");
		}
		
		//is number of roads found is correct:
		$bus_station_qty = count($this->roads_of_one_bs2bs_by_end_bs_id);
		$bus_station_reach_qty = count($this->roads_by_start_bs_id[$bus_station_id]);
		
		if(isset($this->roads_of_one_bs2bs_by_end_bs_id[$bus_station_id])){
			//if the start bus station can be an end bus station
			$diff = $bus_station_qty - $bus_station_reach_qty - 1;	
		}
		else{
			//if the start bas station is not an end bus station
			$diff = $bus_station_qty - $bus_station_reach_qty;
		}
		
		//if not all stations reached from the current start bus stations
		/*if ( $diff > 0 ){
			$roads_qty_by_start_bs_id = count($this->roads_by_start_bs_id);
			$array_of_bs_ids_of_roads_by_start_bs_id = array_keys($this->roads_by_start_bs_id);
			
			foreach ($this->roads_by_end_bs_id as $key => $value) {
				$roads_qty_which_reach_the_bs = count($value);
				//should have $roads_qty_which_reach_the_bs == $roads_qty_by_start_bs_id
				//if not:
				if(( (array_key_exists($key, $this->roads_by_start_bs_id) === false)
				&& ($roads_qty_which_reach_the_bs < $roads_qty_by_start_bs_id) ) ||
				($roads_qty_which_reach_the_bs < $roads_qty_by_start_bs_id - 1) ){

					$string_of_bus_stations_ids_not_reach .= $key . " ";
				}
			}
			exit("there are not roads found to reached this(those) bus station(s) : " .$string_of_bus_stations_ids_not_reach );
			
			exit( "lack of " . $diff . " bus station(s) from bus station" . $bus_station_id . "<br \>\n");
		}
		//if any stations reached more than once from the current start bus stations
		else */ if ( $diff < 0 ){
			exit( -$diff . "road(s) more than the numbers of max reach bus stations from the bus station" . $bus_station_id . "<br \>\n");
		}
		
		//make a false road to go to the same bus station:
		$cur_road_cloned = clone $cur_road;
		$cur_road_cloned->end_bus_station = $cur_road_cloned->start_bus_station;
		$cur_road_cloned->bs2bss = array();
		$this->add_road($cur_road_cloned);
		
	}
	
	public function find_all_roads_for_each_bus_stations($first_id_of_bus_station_to_do, $nbr_of_bus_station_to_do){
		global $path_to_save;
		$count = 0;
		foreach($this->roads_of_one_bs2bs_by_start_bs_id as $roads){
			if($roads[0]->start_bus_station->id < $first_id_of_bus_station_to_do){
				continue;
			}
			if($roads[0]->start_bus_station->id >= $first_id_of_bus_station_to_do + $nbr_of_bus_station_to_do){
				break;
			}
			//to debug
/*			if($roads[0]->start_bus_station->id != 72)
				continue;*/
			//end to debug
			//if it s not a boundary:
			if($roads[0]->start_bus_station->type != "boundary"){
				$this->find_all_roads_from_one_bs($roads[0]->start_bus_station->id);
				
				//prepare the roads from the current bus station to be saved
				$roads_to_save = $this->format_datas_to_save();
				
				$this->save_roads($roads_to_save);
				//$this->show_stats();
				$this->remove_all_roads_created();

				//echo "all roads from start bus station of if : $roads[0]->start_bus_station->id\n";
			}
			//echo "coucou\n";
		}
	}
	
	//format_datas_to_save
	public function format_datas_to_save(){
		$datas_to_save = array();
		//to debug
		$count = 0;
		//end to debug
		foreach ($this->roads as /*$start_bus_station => */$road) {
			if($road->end_bus_station != $road->start_bus_station){
				$datas_to_save[] = $road->format_datas_to_save(/*$start_bus_station*/);
			}
			//to debug
			$count++;
			//end to debug
		}
		return $datas_to_save;
	}
	
	public function save_roads($roads_to_save){
		foreach ($roads_to_save as $road_to_save) {
			Road::save_road($road_to_save);
		}	
	}

	public function remove_all_roads_created(){
		
		//remove the roads from sub_red to free the memory:
		$keys = array_keys($this->roads);
		$start_bus_station_id = $this->roads[$keys[0]]->start_bus_station->id;
		foreach ($keys as $key) {
			unset($this->roads[$key]);
		}
		
		$keys = array_keys($this->roads_by_end_bs_id);
		foreach ($keys as $key) {
			unset($this->roads_by_end_bs_id[$key]);
		}
		
		
		$keys = array_keys($this->roads_by_start_bs_id[$start_bus_station_id]);
		foreach ($keys as $key) {
			$this->roads_by_start_bs_id[$start_bus_station_id][$key]->__destruct();
		}
		unset($this->roads_by_start_bs_id[$start_bus_station_id]);
		

		/*$this->roads = array();
		$this->roads_by_end_bs_id = array();
		$this->roads_by_start_bs_id = array();*/
		
		//todo : make it works:
		gc_collect_cycles();
	}
	
	public function show_stats(){
		$bs2bss_nbr_by_length = array();
		foreach ($this->roads as $road) {
			$bs2bss_length = count($road->bs2bss);
			if(isset($bs2bss_nbr_by_length[$bs2bss_length])){
				$bs2bss_nbr_by_length[$bs2bss_length]++;
			}
			else{
				$bs2bss_nbr_by_length[$bs2bss_length]=1;
			}
		}
		foreach ($bs2bss_nbr_by_length as $key => $value){
			echo "nbr of busline with $key part: $value\n";
		}
	}
	
	//from a road list base found all the roads possible:
	public function __construct(){
		global $path_to_save;
		$this->path_to_save = $path_to_save;
		
		$this->speeds["mainLine"] = 13; //13 m/s ~30km/h
		$this->speeds["feeder"] = 7; //7 m/s ~25km/h
		$this->speeds["other"] = 7; //7 m/s ~25km/h
		$this->speeds["by_foot"] = 0.7; //0.7 m/s ~2.5km/h
	
		return $this;
	}
}


















?>






