<?php
require_once 'access_to_db.php';
require_once 'saveToDb.php';
require_once 'tools.php';
require_once 'Vertex.php';
require_once 'create_squares_between_links.php';
require_once 'create_squares.php';
require_once 'extract_datas_from_db.php';
require_once 'find_areas_to_make_squares.php';
require_once 'create_squares_of_busline.php';


bcscale(0);

$multipicador = 10000000; //if it needs to be mayor, lat and lng in to_square and from_square must be resetting
$grid_path = bcmul($multipicador, 0.001);
$precision = - substr_count($grid_path, '0');

ini_set('memory_limit', "1000M");
set_time_limit(30000);


//divide all the town in square of $grid_path ~10m of the side
//and found the bus lines and bus station inside each one

//access to the database:
$bdd;
$bus_lines_list;

extract_datas_from_db();

$last_id = 0;
//for each bus lines
foreach ($bus_lines_list as $bus_line) {
	if($bus_line['type'] != 'mainLine'){
		echo 'processing grid creation for bus line : ' . $bus_line['bus_line_id'] . "<br \\> \n";
		$squares = array();
		$last_id = create_squares_of_bus_line($bus_line, $last_id);
		saveToDb($links_squares, 'squares');
		echo ' grid creation for bus line : ' . $bus_line['bus_line_id'] . " done <br \\> \n";
	}
}

echo 'all done';

