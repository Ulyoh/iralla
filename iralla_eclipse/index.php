<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN""http://www.w3.org/TR/html4/frameset.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 
   		<title>Buses de Guayaquil</title>

		<link rel="stylesheet" media="screen" type="text/css" title="Design" href="mainpage/main_style.css" />

<!--call google map script: -->
		<?php 
		//	if ( (isset($_GET['preTreatment'])) && ($_GET['preTreatment'] == 'true') ){
				echo "<script type='text/javascript' src='http://maps.google.com/maps/api/js?libraries=geometry&v=3.3&sensor=false'></script>";
		/*	}
			else{
				echo "<script type='text/javascript' src='http://maps.google.com/maps/api/js?v=3.3&sensor=false'></script>";
			}*/
				if ( (isset($_GET['debug'])) && ($_GET['debug'] == 'true') ){
					echo ' <script class="my_script" src="debug/reload.js" type="text/javascript"></script>';
				}
		?>
		<script <?php if ( (isset($_GET['debug'])) && ($_GET['debug'] == 'true') ){echo 'class="my_script"';} ?>src="libraries/json/json2.js" type="text/javascript"></script>
		
		<script <?php if ( (isset($_GET['debug'])) && ($_GET['debug'] == 'true') ){echo 'class="my_script"';} ?>src="js/commonsFunctions.js" type="text/javascript"></script>
		
		<script <?php if ( (isset($_GET['debug'])) && ($_GET['debug'] == 'true') ){echo 'class="my_script"';} ?>src="js/ArrayOf.js" type="text/javascript"></script>

		<script <?php if ( (isset($_GET['debug'])) && ($_GET['debug'] == 'true') ){echo 'class="my_script"';} ?>src="js/Map.js" type="text/javascript"></script>

		<script <?php if ( (isset($_GET['debug'])) && ($_GET['debug'] == 'true') ){echo 'class="my_script"';} ?>src="js/SubMap.js" type="text/javascript"></script>
		
		<script <?php if ( (isset($_GET['debug'])) && ($_GET['debug'] == 'true') ){echo 'class="my_script"';} ?>src="js/Marker.js" type="text/javascript"></script>
		
		<script <?php if ( (isset($_GET['debug'])) && ($_GET['debug'] == 'true') ){echo 'class="my_script"';} ?>src="js/Cross.js" type="text/javascript"></script>
		
		<script <?php if ( (isset($_GET['debug'])) && ($_GET['debug'] == 'true') ){echo 'class="my_script"';} ?>src="js/BusLine.js" type="text/javascript"></script>
		
		<script <?php if ( (isset($_GET['debug'])) && ($_GET['debug'] == 'true') ){echo 'class="my_script"';} ?>src="js/BusStation.js" type="text/javascript"></script>
		
		<script <?php if ( (isset($_GET['debug'])) && ($_GET['debug'] == 'true') ){echo 'class="my_script"';} ?>src="js/Polyline.js" type="text/javascript"></script>
		
		<script <?php if ( (isset($_GET['debug'])) && ($_GET['debug'] == 'true') ){echo 'class="my_script"';} ?>src="js/showResult.js" type="text/javascript"></script>
		
		<script <?php if ( (isset($_GET['debug'])) && ($_GET['debug'] == 'true') ){echo 'class="my_script"';} ?>src="js/findRutas.js" type="text/javascript"></script>
		
		<script <?php if ( (isset($_GET['debug'])) && ($_GET['debug'] == 'true') ){echo 'class="my_script"';} ?>src="js/findRoute.js" type="text/javascript"></script>
		
		<script <?php if ( (isset($_GET['debug'])) && ($_GET['debug'] == 'true') ){echo 'class="my_script"';} ?>src="js/lookForMenu.js" type="text/javascript"></script>
		
		<?php 
			if ( (isset($_GET['preTreatment'])) && ($_GET['preTreatment'] == 'true') ){
				echo " <script class='my_script' src='preTreatment/preTreatment.js' type='text/javascript'></script>";
			}
		?>
		
		<!-- call handling script of busses lines -->
		<script <?php if ( (isset($_GET['debug'])) && ($_GET['debug'] == 'true') ){echo 'class="my_script"';} ?> src="mainpage/init.js" type="text/javascript"></script>
		<!-- call handling script to add buses lines -->
		<script <?php if ( (isset($_GET['debug'])) && ($_GET['debug'] == 'true') ){echo 'class="my_script"';} ?> src="mainpage/communication.js" type="text/javascript"></script>
		<!-- call handling script to modify css styles -->
		<script <?php if ( (isset($_GET['debug'])) && ($_GET['debug'] == 'true') ){echo 'class="my_script"';} ?> src="mainpage/style.js" type="text/javascript"></script>
		
		<!--  load the bus stations from the db -->
		<?php
			echo'<script class="my_script" type="text/javascript">';
			include 'get_mains_bus_stations_to_show.php';
			include 'get_mains_lines_to_show.php';
			echo'</script>';
		?>
		
    </head>
    <body onload="initialize();<?php
    	if ( (isset($_GET['preTreatment'])) &&  ($_GET['preTreatment'] == 'true')){
     		echo "accessToPreTreatment(); setTimeout('launchMainShowBusStationsOnMap()', 5000);";
    	}
    	if ( (isset($_GET['debug'])) && ($_GET['debug'] == 'true') ){
    		echo 'debugMode();';
    	}
     ?>">
     	<div id="buscar" onMouseOver="showLookForMenu()"> buscar </div>
     	<table id="look_for_menu">
     		<tr><th><button id="select_look_for_roads" class="look_for_menu_button" onClick="show_look_for_roads()" > una ruta </button></th></tr>
     		<tr><th><button id="select_look_for_route" class="look_for_menu_button" onClick="show_look_for_route()" > un itinerario </button></th></tr>
      		<tr><th><button id="select_look_for_roads_near_to" class="look_for_menu_button" onClick="show_look_for_roads_near_to()"> rutas cerca de </button></th></tr>
     	</table>
     	
     	<div id='direction'>
			ubica una ruta: <input id="address" type="text" onKeyUp="findRutas(event)" />
			<input class="cross_button" type="image" src="data/unvalid.png" onClick="cross_button_click('direction')"/>
		</div>
		
		<ul id='suggestionListNode'></ul>
		
		<table id='itinerario'>
			<tr><td>
				<span id="from_road_title" class="from_road"> Desde la esquina : </span>
				<span id="to_road_title" class="to_road"> Hasta la esquina : </span>
				<span id="near_to_title" class="to_road"> Cerca de la esquina : </span>
				<input class="cross_button" type="image" src="data/unvalid.png" onClick="cross_button_click(this, 'itinerario')"/>
			</td></tr>
			 <tr  id = "table_to_find_road_1" ><td>
				 <table>
				 	<tr id="row_nombre_road_1" ><td>
						<span id='text_first_road_name' > nombre(s) de la primera calle: </span>
						<button id="modify_roads_button_1" class="modify_button" onClick="modify_roads_1()"> 
							<span id="text_modify_roads_button_1"> cambiar </span>
						</button>
						<button id="valid_roads_button_1" class="modify_button" onClick="validar(this)"> 
							<span id="text_valid_roads_button_1"> validar </span>
						</button>
					</td></tr>
					<tr id="row_list_road_1" ><td>
					
					</td></tr>
					<tr id="row_input_road_1" ><td>
						<input id='to_find_road_1' class='found_roads' type='text'  onKeyUp="findRoads(this, event)"/><br />
					</td></tr>
					<tr id="row_suggestion_list_road_1" ><td>	
						<table class="suggestionListNode2">
						</table>
					</td></tr>
				</table>
			</td></tr>
			<tr id = "table_to_find_road_2"><td>
				<table>
					<tr id="row_nombre_road_2" ><td>
						<span id='text_second_road_name' > nombre(s) de la secunda calle: </span>
						<button id="modify_roads_button_2" class="modify_button" onClick="modify_roads_2()"> 
							<span id="text_modify_roads_button_2"> cambiar </span>
						</button>
						<button id="valid_roads_button_2" class="modify_button" onClick="validar(this)"> 
							<span id="text_valid_roads_button_2"> validar </span>
						</button>
					</td></tr>
					<tr id="row_list_road_2" ><td>
					
					</td></tr>
					<tr id="row_input_road_2" ><td>
						<input id='to_find_road_2' class='found_roads' type='text' disabled="disabled"  onKeyUp="findRoads(this, event)"/><br />
					</td></tr>
					
					<tr id="row_suggestion_list_road_2" ><td>
						<table class="suggestionListNode2" >
						
						</table>
					</td></tr>
				</table>
			</td></tr>
			<tr id="instructions_to_select_marker"><td>
				<span id="text_instructions_to_select_marker"> clic el indicador para seleccionar el lugar de salida</span> 
			</td></tr>
			<tr id="cross_road_not_found" ><td>
				<span id="text_no_results"> no hay resultados </span> 
			</td></tr>
			<tr id="directly_point_at_the_place" ><td> 
				<button id="directly_point_at_the_place_button" onClick="initToPlaceTheMarker()"> 
					<span id="text_click_on_map" class="from_road"> apuntar directamente la salida en la mapa </span> 
				</button>
			</td></tr>
			<tr id="row_instructions_marker" ><td>
				<span id="text_row_instructions_marker"> has clic en la mapa para seleccionar el punto de salida</span> 
			</td></tr>
			<tr id="row_valid_cancel_marker" ><td>
				<button id="valid_marker" onClick="validMarker()"> <span id="text_valid_marker"> validar </span> </button>
				<button id="cancel_marker" onClick="cancelMarker()"> <span id="text_cancel_marker"> cancelar </span> </button>
			</td></tr>
		</table>
		
		<!-- <div id='calculate'> calcular </div> -->
		
		
		
		<div id="myInfo"></div>
		
		<div id="show_infos">
			<p id="infos">	</p>
		</div>
		
		<div id="map_canvas"></div>
		
		<div id="show_buslines_list">
		</div>
		
    </body>

<head>
<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
</head>
</html>


