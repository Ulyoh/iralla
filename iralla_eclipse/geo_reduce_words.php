<?php
/*
$string = "fr aס\"ci?sc'o";
$result = reduce_latinos_words($string);
echo $result;*/

function reduce_latinos_words($string){
	
	//remove ":
	//trim($string)
	
	//remove accents:
	$string = filter($string);
	
	//to lower-case:
	$string = strtolower($string);
	
	//numbers:
	$string = reduce_numbers($string);
	
	//reduce latino orthograph:
	$string = abreviation_latino($string);
	
	//simplify orthografia
	$string = simplify_latino_orthography($string);
	
	return $string;
}

function filter($string) {
	//TODO: bug: '°' convert to 'a'
	$search = array ('@[יטךכÊֻ]@i','@[אגהֲִ]@i','@[מן־ֿ]@i','@[ûשüÛÜ]@i','@[פצװײ]@i','@[ס,ׁ]@i','@[ח]@i','@[^a-zA-Z0-9]@');
	$replace = array ('e','a','i','u','o','n','c', ' ');
	return preg_replace($search, $replace, $string);
}

function reduce_numbers($string){
	$numeros_cardinales_list = array("uno", "una", "un", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve", "diez", "once", "doce", "trece", "catorce", "quince", "dieciseis", "diecisiete", "dieciocho", "diecinueve", "veinte", "veintiuno", "veintiuna", "veintiun", "veintidos", "veintitres", "veinticuatro", "veinticinco", "veintiseis", "veintisiete", "veintiocho", "veintinueve", "treinta", "treinta y uno", "treinta y una", "treinta y un");
	$numbers2 = array("1","1","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","21","21","22","23","24","25","26","27","28","29","30","31","31","31" );
	
	//TODO: mofify the previous arrays to do not need to use array_reverse:
	$numeros_cardinales_list = array_reverse($numeros_cardinales_list);
	$numbers2 = array_reverse($numbers2);
	
	$string = str_ireplace($numeros_cardinales_list, $numbers2, $string);
	return $string;
}

function simplify_latino_orthography($string){
	$string = str_ireplace(array(ci,ce,cc,ge,gi,qu,ll), array(si,se,ks,je,ji,q,y), $string);
	return strtr($string, 'w,z,c', 'v,s,k');
}

function abreviation_latino($string){
	return str_ireplace(array('\"', 'pasaje', ' e ','avenida'), array(' ', 'pa', ' y ', 'av'), $string);
}


