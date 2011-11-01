<?php
require_once 'access_to_db.php';
$bus_line = $bdd->query("
SELECT * 
FROM  `bus_lines` 
WHERE bus_lines.id = 11
");

$bus_line = $bus_line->fetch();

$path = json_decode($bus_line[path]);

$nbr_of_vertex = count($path);
echo "$nbr_of_vertex";

?>