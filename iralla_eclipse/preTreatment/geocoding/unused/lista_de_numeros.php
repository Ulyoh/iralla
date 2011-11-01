<?php
require_once 'access_to_db.php';

$numeros_cardinales_list = array("uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve", "diez", "once", "doce", "trece", "catorce", "quince", "dieciseis", "diecisiete", "dieciocho", "diecinueve", "veinte", "veintiuno", "veintidos", "veintitres", "veinticuatro", "veinticinco", "veintiseis", "veintisiete", "veintiocho", "veintinueve", "treinta", "treinta y uno", "treinta y cinco", "treinta y nueve", "cuarenta", "cincuenta", "sesenta", "setenta", "ochenta", "noventa", "cien", "ciento" ,"doscientos", "doscientas", "trescientos", "trescientas", "cuatrocientos", "cuatrocientas", "quinientos", "quinientas", "seiscientos", "seiscientas", "setecientos", "setecientas", "ochocientos", "ochocientas", "novecientos", "novecientas", "mil", "millon", "millones", "billon");

/* Vérification de la connexion */
if (mysqli_connect_errno()) {
	 printf("echec de la connexion : %s\n", mysqli_connect_error());
	 exit();
}

$half_crossroads = $bdd->query("SELECT * FROM geolocalisation");

$have_numero = array();
while($half_crossroad = $half_crossroads->fetch()){
	$palabras = explode(" ", $half_crossroad[way_name]);
	
	foreach ($palabras as $palabra){
		if(in_array($palabra, $numeros_cardinales_list)){
			$have_numero[] = $palabra;
			break;
		}
	}
	
}
	
	
echo 'to see';
?>