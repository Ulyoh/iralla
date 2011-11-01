<?php
/*
	FORMAT OF $datas:
				$datas->$data1[field_0] = $value_0
					    $data1[field_1] = $value_1
					    	   .	    =   .
					   	$data2[field_0] = $value_10
					   	$data2[field_1] = $value_11
			
*/
require_once 'access_to_db.php';

	function saveToDb($datas, $table){
		global $bdd;
			/* Vérification de la connexion */
			if (mysqli_connect_errno()) {
			    printf("echec de la connexion : %s\n", mysqli_connect_error());
			    exit();
			}
			
			$columns_names = getColumnNames($table);
			
			// VERIFIER LA SECURITE :
			
			//store datas in the database:
			if(!is_array($datas)){
				die("$datas is not an array");
			}
			foreach ($datas as $data){
				$fields = array();
				$values = array();
				$interogationsPoints = '';
				if(!is_array($data)){
					die("$data is not an array");
				}
					foreach ($data as $key => $value){
					$fields[] = $key;
					$values[] = $value;
					//array_push($fields, $key);
					//array_push($values, $value);
					$interogationsPoints .= ',? ';
				}
				$interogationsPoints = substr_replace($interogationsPoints, '', 0, 1);

				//verification if the names of fields are in the data base:
				$string_fields = '';
				foreach ( $fields as $field){
					$string_fields = $string_fields . ',' . $field; 
					foreach ($columns_names as $column_name){
						if($column_name == $field){
							continue 2;
						}
					}
					return false;
				}
				$string_fields = substr_replace($string_fields, '', 0, 1);
				$req = $bdd->prepare('INSERT INTO ' . $table . '(' . $string_fields . ') VALUES(' . $interogationsPoints . ')');
				$req->execute($values);
			}
	}
	

	function getColumnNames($table_name){
		global $bdd;
        $sql = 'SHOW COLUMNS FROM ' . $table_name;
        
        $req = $bdd->prepare($sql);
            
        try {    
            if($req->execute()){
                $raw_column_data = $req->fetchAll();
                
                foreach($raw_column_data as $outer_key => $array){
                    foreach($array as $inner_key => $value){
                            
                        if ($inner_key === 'Field'){
                            if (!(int)$inner_key){
                            	$column_names[] = $value;
                        	}
                     	}
                    }
                }        
            }
			$req->closeCursor();
            return $column_names;
        } catch (Exception $e){
                return $e->getMessage(); //return exception
        }

        $req = null;
    }   
	
?>