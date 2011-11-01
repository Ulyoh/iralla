<?php
require 'saveToDb.php';
require_once 'access_to_db.php';

	/* Vérification de la connexion */
	if (mysqli_connect_errno()) {
		printf("échec de la connexion : %s\n", mysqli_connect_error());
		exit();
	}
	
$request = $bdd->query("
	SELECT id, name
	FROM  bus_lines
	WHERE bus_lines.type = 'other'
	
");

$data = array();
while($line = $request->fetch()){
	$data[bus_line_id] = $line[id];
	$lineString = $line[name];
	
	$lineString = str_ireplace('ruta ', '', $lineString);
	$lineString = str_ireplace('ruta', '', $lineString);
	
	$lineStrings = split(' ', $lineString);
	$words_list = array();
	
	$error = true;
	
	foreach ($lineStrings as $lineString){
		
		//if the string is only a number:
		if (ereg('^[[:digit:]]{1,}$', $lineString) == true){
			$words_list[] = $lineString;
			$error = false;
		}
		
		//if the string include the "-":
		elseif( ereg('^[[:digit:]]+[-].', $lineString) == true){
			$words_list[] = $lineString;
			$words_list[] = str_replace('-', ' ', $lineString);
			$words_list[] = ereg_replace('[-].', '', $lineString);
			$error = false;
		}
		
		//if the string is a number between (  )  //only one case
		elseif( ereg('^\(+[[:digit:]]+\)$', $lineString) == true){
			$words_list[] = str_replace(array('(', ')'), ' ', $lineString);
		}
	/*	
		//else
		else{
			$words_list[] = $lineString;
		}*/
		
	}
	
	//if not one number found:
	if($error == true){
		exit('no one number found');
	}

	foreach ( $words_list as $word){
		$data[word] = $word;
		$datas[] = $data;
	}
}

$bdd->query("TRUNCATE TABLE words_to_search_rutas");
saveToDb($datas, 'words_to_search_rutas');
		//error






?>
