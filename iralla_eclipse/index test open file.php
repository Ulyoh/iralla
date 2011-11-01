<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN""http://www.w3.org/TR/html4/frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 
   		<title>test load one road </title>


		
    </head>
    <body >
     	<?php 
     		$file_to_open = "d:/roads/1000/900";
			$fh = fopen($file_to_open, 'r') or die("can't open file\n");
			$road = fread($fh, 1000000);
			fclose($fh);
	     	echo $road;
     	?>
     	
     	
     	
     	
    </body>

<head>
<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
</head>
</html>


