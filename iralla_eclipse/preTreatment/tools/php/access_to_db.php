<?php
	//access to the database:
	$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
	$bdd = new PDO('mysql:host=www.cortocamino.com;dbname=Guayaquil', 'root', '', $pdo_options);
	
?>