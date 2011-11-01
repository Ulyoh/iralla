<?php
require 'geo_reduce_words.php';
require_once 'access_to_db.php';

$way_written = $_POST['q'];

//$way_written = "av 38 e no";
	
/* Vérification de la connexion */
if (mysqli_connect_errno()) {
	 printf("echec de la connexion : %s\n", mysqli_connect_error());
	 exit();
}

class Word{
	public $string;
	public $nth;

	public function __construct($string, $nth){
		$this->string = $string;
		$this->nth = $nth;
	}
}

$simplify_way_written = reduce_latinos_words($way_written);

$words = explode(" ", $simplify_way_written);

$words_list = array();
foreach ($words as $key => $word){
	$words_list[] = new Word($word, $key+1);
}

$length = count($words_list);
$values = array();
$conditions = "";
foreach($words_list as $key => $word){
	$conditions .= 'word = ? ';
	//$values[] = '`' . $word->string . '`';
	$values[] = $word->string;
	//to debug
	//break;
	if($key < $length - 1){
		$conditions .= 'OR ';
	}
}


$half_crossroads = $bdd->prepare('
	SELECT `way_name_id`, COUNT(*) AS qty, word_quantity_to_search, way_name
	FROM(
		SELECT 	way_name_id, 
				geo_ways_names.id as geo_way_name_id,
				way_name,
				word_quantity_to_search
		FROM `geo_words_in_ways_names`, geo_ways_names 
		WHERE ' . $conditions . ' 
		HAVING way_name_id = geo_way_name_id
		ORDER BY `way_name_id`
		) AS temp
	GROUP BY `way_name_id`
	ORDER BY qty DESC
	');
$half_crossroads->execute($values);

class Way_name_to_send{
	public $name;
	public $id;
	
	public function __construct($name, $id){
		$this->name = $name;
		$this->id = $id;
	}
}

$half_crossroad_list = array();
$ways_names_to_send = array();
$first_value = true;

$length = 0;
while($half_crossroad = $half_crossroads->fetch()){
	
	//set $max_qty on the first loop
	if($first_value == true){
		$max_qty = $half_crossroad[qty];
		$first_value = false;
	}
	
	//if at least one result match with more than one word and the current match only with one
	if(($max_qty > 1 ) && ($half_crossroad[qty] == 1)) {
		break;
	}
	
	$half_crossroad[diff] = abs($half_crossroad[word_quantity_to_search] - $half_crossroad[qty]);
	$way_name_to_send = new Way_name_to_send($half_crossroad[way_name], $half_crossroad[way_name_id]);

	if ($length > 0){
		$i = $length;
		$index_to_insert = $length;
		while(($half_crossroad[qty] == $half_crossroad_list[$i-1][qty]) 
		&& ($half_crossroad[diff] < $half_crossroad_list[$i-1][diff]) ){
			$half_crossroad_list[$i] = $half_crossroad_list[$i-1];
			$ways_names_to_send[$i] = $ways_names_to_send[$i-1];
			$index_to_insert = $i-1;
			$i--;
		}
		$half_crossroad_list[$index_to_insert] = $half_crossroad;
		$ways_names_to_send[$index_to_insert] = $way_name_to_send;
	}
	else{
		$half_crossroad_list[0] = $half_crossroad;
		$ways_names_to_send[0] = $way_name_to_send;
	}
	$length++;
}

//if difference of the name between two roads is only the cardinal direction:

//create a road name with two way name id:


//keep the first 20 result:
/*
if(count($ways_names_to_send) > 20){
//TODO modify, does not work:
	$ways_names_to_send = array_splice($ways_names_to_send, 20);
}
*/

echo json_encode($ways_names_to_send);
?>

