<?php
require_once 'access_to_db.php';
require_once 'tools.php';
require_once 'tools_to_look_for_roads.php';

//to debug
//echo 'first' . memory_get_usage (true ). "\n";
//end to debug

$start = time();
$multipicador = 10000000;
$denominator_to_get_real_values = 10000000;
$grid_path = 0.001;
$grid_path_mult = bcmul($multipicador, $grid_path)/10;  //TODO why /10???? to check with create grid
$foot_speed = 0.7;
$bus_speed = 7;

$request = $_POST['q'];
//$request = '	{"lat":-2.2540513884736235,"lng":-79.89485015869138}';
$request = json_decode($request);

$position['lat'] = $request->lat;
$position['lng'] = $request->lng;

//find nearst bus stations :
$interval = 0.003;

//change position to fit with from square coordinates
$position['lat'] =bcmul($position['lat'], $grid_path_mult);
$position['lng'] =bcmul($position['lng'], $grid_path_mult);

//from square
$interval = 3;
$ecart_min_between_d_min_and_d_max = 100;//en meter

$bus_lines_ids = nearest_squares_2($position, $interval, $ecart_min_between_d_min_and_d_max);

//extract the bus lines path:
$values = array();
foreach ($bus_lines_ids as $bus_line_id){
	if(isset($test)){
		$test .= 'OR id = ? ';
	}
	else{
		$test = 'id = ? ';
	}
	$values[] = $bus_line_id;
}

$req = $bdd->prepare("
		SELECT name, path, id
		FROM bus_lines
		WHERE $test
");

$req->execute($values);

while($bus_line = $req->fetch()){
	unset($bus_line[0]);
	unset($bus_line[1]);
	unset($bus_line[2]);
	$bus_lines[]=$bus_line;
}
require_once 'close_bdd.php';

//$execution_time = time() - $start;
//echo'execution time: '. $execution_time . "\n";

//echo memory_get_peak_usage (true );
echo json_encode($bus_lines);


