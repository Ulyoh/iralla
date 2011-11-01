<?php
	$serveur = '127.0.0.1';
	$database = 'Guayaquil';
	$table = 'fields';

	$list = $_REQUEST["q"];
	
	$datas = explode("-", $list);

	//connect to the server:
	if (!(mysql_connect($serveur, 'root', ''))){
		echo 'could not connect to the serveur: ' . $serveur;
		return;
	}
	
	//open the database:
	if (!(mysql_select_db($database))){
		echo 'could not open the database: ' . $database;
		return;		
	}
	
	//open or create the table if not exist
	$query = 'CREATE TABLE IF NOT EXISTS ' . $table . '(id VARCHAR(4), UNIQUE(id))';
	if (!(mysql_query($query))){
		echo 'table ' . $table . ' could not be created ';
		return;
	}
	
	//store datas in the database:
	$first = 1;
	foreach ($datas as $value)
	{
		if ($first == 1){
			//extract the id :
			$id = $value;
			echo '*** id = ' . $id .' ';
							
			//add a line with the value of $id in the column ids:
			$query = 'INSERT INTO '	. $table . '(id) VALUES(' . $id . ')';
			if (!(mysql_query($query))){			
				echo 'line of the id value of ' . $id . ' could not be added, its possibly exists already';
			}
			
			$first = 0;
			
		}else{
			echo 'field = ' . $value . ' ';
								
			//create column corresponding to $value:
			$query = 'ALTER TABLE ' . $table . ' ADD ' . $value . ' TINYINT';
			if (!(mysql_query($query))){
				echo " column " . $value . " could not be created ";
			}

			//mark "1" in the line $id and column $value
			$query = 'UPDATE ' . $table .' SET ' . $value . ' = 1 WHERE id = ' . $id;
			if (!(mysql_query($query))){
				echo 'the data could not be recorded in the column ' . $value . ' with id = ' . $id;
			}
		}			
	}
	
	
?>