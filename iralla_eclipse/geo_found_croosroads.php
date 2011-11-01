<?php
require_once 'access_to_db.php';
	
/* Vérification de la connexion */
if (mysqli_connect_errno()) {
	 printf("echec de la connexion : %s\n", mysqli_connect_error());
	 exit();
}

$received = json_decode($_POST['q']);

$roads_list_ids_1 = $received->roads1;
$roads_list_ids_2 = $received->roads2;

/*
$roads_list_ids_1[] = "1556";
$roads_list_ids_2[] = "1408";
$roads_list_ids_2[] = "2021";
*/
if((is_array($roads_list_ids_1)) && (is_array($roads_list_ids_2))){
	$conditions_1 = "way_name_id = ? ";
	for ($i = 1; $i < count($roads_list_ids_1); $i++) {
		$conditions_1 .= 'OR way_name_id = ? ';
	}	
	
	$conditions_2 = "way_name_id = ? ";
	for ($i = 1; $i < count($roads_list_ids_2); $i++) {
		$conditions_2 .= 'OR way_name_id = ? ';
	}
	
	$values = array_merge($roads_list_ids_1, $roads_list_ids_2);
	
	$nodes = $bdd->prepare('
	SELECT *
		FROM  
		(
		SELECT reference, lat, lng
			FROM geolocalisation
			WHERE ' . $conditions_1 .'
		) AS result1,
		
		(
		SELECT reference, lat, lng
			FROM geolocalisation
			WHERE ' . $conditions_2 .'
		) AS result2
		WHERE result1.reference = result2.reference
	
	');
	
	
	$nodes->execute($values);
	$coords = array();
	while($node = $nodes->fetch()){
		$coords[lat] = $node[lat];
		$coords[lng] = $node[lng];
		$coords_list[] = $coords;
	}
	
	echo json_encode($coords_list);
}	





/*

	SELECT g1.reference, g1.lat, g1.lng
		FROM geolocalisation as g1, geolocalisation as g2
		WHERE g1.way_name_id = "1556" OR g2.way_name_id = "1408" OR g2.way_name_id ="2021"
		HAVING g1.way_name_id = g2.way_name_id




SELECT *
	FROM  
	(
	SELECT reference, lat, lng
		FROM geolocalisation
		WHERE way_name_id = "1556"
	) AS result1,
	
	(
	SELECT reference, lat, lng
		FROM geolocalisation
		WHERE way_name_id = "1408" OR way_name_id ="2021"
	) AS result2
	WHERE result1.reference = result2.reference



*/









/*
$half_crossroads->execute($values);
$ways_names_first_id = array();
$ways_names_second_id = array();
$ways_names_for_one_id = array();
$first_loop = true;

while( $half_crossroad = $half_crossroads->fetch() ){
	$current_way_name_id = $half_crossroad[way_name_id];
	
	//if the way name id change change:
	if(($first_loop == false) && ($previous_way_name_id != $current_way_name_id)){
		//save first list
		$ways_names_first_id = $ways_names_for_one_id;
		//reinit $ways_name_for_one_id
		$ways_name_for_one_id = array();
	}
	
	//save by the reference of the node:
	$ways_names_for_one_id[$half_crossroad[reference]] = $half_crossroad;
	
	//if the first way name is done
	
	$first_loop = false;
	$previous_way_name_id = $current_way_name_id;
}

$ways_names_second_id = $ways_names_for_one_id;
*/








?>

