<?php
require 'saveToDb.php';
require 'modifyDb.php';
require 'geo_reduce_words.php';
require_once 'access_to_db.php';	
/*
function is_abreviation($string){
	$abreviations_list = array('pa', 'ne', 'no', 'so', 'se', 'cl');
	return in_array($string, $abreviations_list);
}*/
	
/* Vérification de la connexion */
if (mysqli_connect_errno()) {
	 printf("echec de la connexion : %s\n", mysqli_connect_error());
	 exit();
}

$half_crossroads = $bdd->query("SELECT * FROM geolocalisation ORDER BY `way_name` ");

$couples_way_name_id_and_word = array();
$previous_way_name = "";
$save_to_ways_names_list = array();
$modify_geolocalisation =array();
$id_way_name = 0;

while($half_crossroad = $half_crossroads->fetch()){
	$way_name = html_entity_decode($half_crossroad[way_name], ENT_NOQUOTES, 'UTF-8');

	$simplify_way_name = reduce_latinos_words($way_name);
	
	//prepare to save way name in geo_ways_names if new:
	if($previous_way_name != $way_name){
		
		$one_to_save_to_ways_names_list[id] = ++$id_way_name;
		$one_to_save_to_ways_names_list[way_name] = $half_crossroad[way_name];
		//to save to the ways_name database:
		$to_save_to_ways_names_list[] = $one_to_save_to_ways_names_list;
		//used for the if:
		$previous_way_name = $way_name;
		//used to get the id with a $simplify_way_name already know:
		$simplify_way_name_ids_by_way_names[$simplify_way_name] = $id_way_name;
		//current id:
		$current_id = $id_way_name;
		
		$words = explode(" ", $simplify_way_name);
	
		$words_saved = array();
		$words_qty = 0;
		$words_list = "";
		
		foreach ($words as $word){
			if ((!in_array($word, $words_saved)) && ($word != ""))
			{
				$one_couple_way_name_id_and_word[way_name_id] = $current_id;
				$one_couple_way_name_id_and_word[word] = $word;
				$one_couple_way_name_id_and_word[nth] = ++$words_qty;
				//to save in database:
				$couples_way_name_id_and_word[] = $one_couple_way_name_id_and_word;
				$words_saved[] = $word;
				$words_list .= $word . " ";
			}	
			
			//if numbers inside the $word:
			if( ( ereg('[0-9]', $word) ) && (ereg('[a-z]', $word)) ){
				//save the number inside the word and the letter before and after:
				$number = ereg_replace('[^0-9]+', '', $word);
				$number_position = strpos($word, $number);
				$prev_next_letters = explode($number, $word);
				
				//if letters before the number:
				if($prev_next_letters[0] != ""){
					$word = reduce_latinos_words($prev_next_letters[0]);
					
					if($word == 'e'){
						$word = 'y';
					}
					
					
					$one_couple_way_name_id_and_word[way_name_id] = $current_id;
					$one_couple_way_name_id_and_word[word] = $word;
					$one_couple_way_name_id_and_word[nth] = $words_qty;
					//to save in database:
					$couples_way_name_id_and_word[] = $one_couple_way_name_id_and_word;
					$words_saved[] = $word;
					$words_list .= $word . " ";
				}
				
				//saving the number:
				$one_couple_way_name_id_and_word[way_name_id] = $current_id;
				$one_couple_way_name_id_and_word[word] = $number;
				$one_couple_way_name_id_and_word[nth] = $words_qty;
				//to save in database:
				$couples_way_name_id_and_word[] = $one_couple_way_name_id_and_word;
				$words_saved[] = $number;
				$words_list .= $number . " ";
				
				
				//if letters after the number:
				if($prev_next_letters[1] != ""){
					
					$word = reduce_latinos_words($prev_next_letters[1]);
					
					if($word == 'e'){
						$word = 'y';
					}					
					
					
					$one_couple_way_name_id_and_word[way_name_id] = $current_id;
					$one_couple_way_name_id_and_word[word] = $word;
					$one_couple_way_name_id_and_word[nth] = $words_qty;
					//to save in database:
					$couples_way_name_id_and_word[] = $one_couple_way_name_id_and_word;
					$words_saved[] = $word;
					$words_list .= $word . " ";		
				}
				
			}
			
			//if one cardinal point indication:
			//if(in_array($word, array(n,ne,no,s,se,so,e,o)))
		}
		
		$one_to_modify_geolocalisation[id] = $half_crossroad[id];
		$one_to_modify_geolocalisation[way_name_id] = $current_id;
		
		$one_to_save_to_ways_names_list[way_name_reduced] = $simplify_way_name;
		$one_to_save_to_ways_names_list[words_selected_to_search] = $words_list;
		$one_to_save_to_ways_names_list[word_quantity_to_search] = $words_qty;
		$one_to_save_to_ways_names_list[geolocalisation_ids] = $half_crossroad[id];
		
		$save_to_ways_names_list[] = $one_to_save_to_ways_names_list;
	}
	else{ 
		$one_to_modify_geolocalisation[id] = $half_crossroad[id];
		$one_to_modify_geolocalisation[way_name_id] = $current_id;
		
		foreach($save_to_ways_names_list as $key => $one_to_save_to_ways_names_list){
			if($one_to_save_to_ways_names_list[id] == $simplify_way_name_ids_by_way_names[$simplify_way_name]){
				$save_to_ways_names_list[$key][geolocalisation_ids] .= ' ' . $half_crossroad[id];
			}
		}
	}
	$modify_geolocalisation[] = $one_to_modify_geolocalisation;
}
	
$bdd->query("TRUNCATE geo_words_in_ways_names");
//save to words_in_way_name:
saveToDb($couples_way_name_id_and_word, 'geo_words_in_ways_names');

//modify geolocalisation:
modifyDb($modify_geolocalisation, 'geolocalisation');

//save to geo_ways_names
$bdd->query("TRUNCATE geo_ways_names");
saveToDb($save_to_ways_names_list, 'geo_ways_names');

?>


