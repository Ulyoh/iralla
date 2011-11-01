<?php

	header('Content-Type: text/xml');
	header ('Cache-Control: no-cache');
	header ('Cache-Control: no-store' , false);
	
	$XmlFileName = $_POST['q'];
	
	readfile("1Ac2oGhS26J/" . $XmlFileName);

?>
