<?php
require_once 'access_to_db.php';

$received = $_POST["q"];

	/* Vérification de la connexion */
	if (mysqli_connect_errno()) {
		printf("échec de la connexion : %s\n", mysqli_connect_error());
		exit();
	}
	
	$vars = array();
	
	eregi('[[:digit:]]+[-]+[[:digit:]]', $received, $var1);
	

	if(isset($var1)){
		foreach ($var1 as $var){
			$received = str_replace($var, '', $received);
		}
		$vars = $var1;
	}

	
	eregi('[[:digit:]]+[ ]+[[:digit:]]', $received, $var2);
	if(isset($var2)){
		foreach ($var2 as $var){
			$received = str_replace($var, '', $received);
		}
		$vars = array_merge($vars, $var2);
	}
	
	eregi('[[:digit:]]{1,}', $received, $var3);
	if(isset($var3)){
		$vars = array_merge($vars, $var3);
	}
	
	if (count($vars) > 0){

		$interrogations_points = '';
		
		foreach($vars as $var){
			$interrogations_points .= ',? ';
		}
		
		$interrogations_points = substr_replace($interrogations_points, '', 0, 1);
		
		$req = $bdd->prepare("
			SELECT 
				words_to_search_rutas.bus_line_id,
				words_to_search_rutas.word,
				bus_lines.id,
				bus_lines.name,
				bus_lines.path,
				bus_lines.flows
			FROM 
				words_to_search_rutas,
				bus_lines
			WHERE
				word = ". $interrogations_points . "
			HAVING
				bus_lines.id=words_to_search_rutas.bus_line_id
			
		");
		
		$req->execute($vars);
		
		$busLines = array();
		while($result = $req->fetch()){
			$busLine = array();
			$busLine[id] = $result[id];
			$busLine[name] = $result[name];
			$busLine[flows] = $result[flows];
			$busLine[path] = $result[path];
			$busLines[] = $busLine;
		}
		
		echo json_encode($busLines);
	}
?>
