<?php
//access to the database:
$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
$bdd = new PDO('pgsql:host=www.cortocamino.com;dbname=guayaquil', 'postgres', 'cathare', $pdo_options);

?>