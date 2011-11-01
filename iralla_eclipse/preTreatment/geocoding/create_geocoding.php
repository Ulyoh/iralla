<?php
	require 'saveToDb.php';
	$dir = 'OSMs';
	$my_dir = opendir($dir) or die('error');
	
	$nodes_array = array();
	
	$nodes_way = array();
	
	$ref_and_way_name_list = array();
	
	/*class Node{
		public $reference;
		public $lat;
		public $lng;
		public $way;
	}*/
	
	while($file = readdir()){
		if( pathinfo($file, PATHINFO_EXTENSION) == 'osm' ){
			
			$doc = new DOMDocument();
			$doc->load(realpath($dir . '/'  . $file));
			
			//get all the nodes:
			$DOM_nodes = $doc->getElementsByTagName('node');
			$length = $DOM_nodes->length;
			
			for( $i = 0; $i < $length; $i++){
				$DOM_node = $DOM_nodes->item($i);
				$new_node = array();
				$new_node[reference] = intval($DOM_node->getAttribute('id'));
				$new_node[lat] = $DOM_node->getAttribute('lat');
				$new_node[lng] = $DOM_node->getAttribute('lon');
				
				$nodes_array[$new_node[reference]] = $new_node;
			}
			
			//get all the ways:
			$DOM_ways = $doc->getElementsByTagName('way');
			$length = $DOM_ways->length;

			for( $i = 0; $i < $length; $i++){
				$way_name = "";
				$DOM_way = $DOM_ways->item($i);
				//found way name:
				$DOM_tags = $DOM_way->getElementsByTagName('tag');
				foreach ($DOM_tags as $DOM_tag){
					$DOM_k = $DOM_tag->getAttribute('k');
					if($DOM_k == 'name'){
						$way_name = htmlentities($DOM_tag->getAttribute('v'), ENT_NOQUOTES, 'UTF-8');
						break;
					}
				}
				if($way_name == ""){
					continue;
				}
				
				//save way name to corresponding nodes:
				$DOM_nds = $DOM_way->getElementsByTagName('nd');
				foreach ($DOM_nds as $DOM_nd){
					$node_way = array();
					//get reference:
					$ref = $DOM_nd->getAttribute('ref');
					
					$node_way = $nodes_array[$ref];
					$node_way[way_name] = $way_name;
					$ref_and_way_name = $ref . $way_name;
					if(!in_array($ref_and_way_name, $ref_and_way_name_list)){
						$ref_and_way_name_list[] = $ref_and_way_name;
						$nodes_way[] = $node_way;
					}
				}
			}
		}
	}
	
	//save to database:
	saveToDb($nodes_way, 'geolocalisation');





?>

