<?php
require_once 'access_to_db.php';
	function modifyDb($datas, $table){
	global $bbb;
		try{
		
			/* VÃ©rification de la connexion */
			if (mysqli_connect_errno()) {
			    printf("échec de la connexion : %s\n", mysqli_connect_error());
			    exit();
			}
			
			$columns_names = getColumnNames($bdd, $table);
			
			
			//TODO:  VERIFIER LA SECURITE :
			
			
			//store datas in the database:
			foreach ($datas as $data){
				$fields = array();
				$values = array();
				
				foreach ($data as $key => $value){
					if($key == 'id'){
						$id = $value;
					}
					else{
						$fields[] = $key;
						$values[] = $value;
					}
				}

				//verification if the names of fields are in the data base:
				$string_par_fields_values = '';
				$string_fields = '';
				$interogationsPoints = '';
				foreach ( $fields as $field){
					$string_par_fields_values = $string_par_fields_values . ',' . $field . '=? ';
					$string_fields = $string_fields . ',' . $field;
					$interogationsPoints .= ',? '; 
					foreach ($columns_names as $column_name){
						if($column_name == $field){
							continue 2;
						}
					}
					return false;
				}
					
				$string_par_fields_values = substr_replace($string_par_fields_values, '', 0, 1);
				$string_fields = substr_replace($string_fields, '', 0, 1);
				$interogationsPoints = substr_replace($interogationsPoints, '', 0, 1);
					
				if ($id == null){
					//echo $data;
					//echo json_encode($data);
					$req = $bdd->prepare('INSERT INTO ' . $table . '(' . $string_fields . ') VALUES(' . $interogationsPoints . ')');
					$req->execute($values);
				}
				else{
					//verify if the id already exist:
					if(isset($id_list) == false){
						$id_list = array();
						$id_list_bdd = $bdd->query('SELECT id FROM ' . $table);
						while($id_bdd = $id_list_bdd->fetch()){
							$id_list[] = $id_bdd['id'];
						}
					}
					if( in_array($id, $id_list) == true){
						$req = $bdd->prepare('UPDATE ' . $table . ' SET ' . $string_par_fields_values . 'WHERE id=' . $id);
						$req->execute($values);
					}
					else{
						$interogationsPoints .= ',? ';
						array_unshift($values, $id);
						$string_fields = 'id,' .  $string_fields;
						$req = $bdd->prepare('INSERT INTO ' . $table . '(' . $string_fields . ') VALUES(' . $interogationsPoints . ')');
						$req->execute($values);
					}
					
				}
				$id = null;
			}
			$req->closeCursor();
		}
		catch(Exception $e){
		    die('Erreur : '.$e->getMessage());
		}
	}
?>