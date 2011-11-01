<?php
	
   function test_my_array($array) 
   {
	   $test1 = 'hello';
	   var_dump( $test1 );
	   echo 'show array:';
       echo $array;

   } 

   $test = array(array(5, 6, 7), 'k', 5);
   
   test_my_array($test);

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
