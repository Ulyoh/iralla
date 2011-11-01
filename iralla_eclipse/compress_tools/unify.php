<?php
//create a folder if not exists to save the "acumulate" site:
$folder_to_create = "/xampp/htdocs/unify";

$site_folder = "/xampp/htdocs/iralla_eclipse/";

if(is_dir($folder_to_create)){
   echo "folder " . $folder_to_create . " already exists /n";
}else{
   mkdir($folder_to_create);
}

$index_html = file_get_contents("http://localhost/iralla_eclipse/index.php");

//open index.php:
$doc = new DOMDocument();
$test = $doc->loadHTML($index_html);

//extract the paths of scripts files:
$paths_list = $doc->getElementsByTagName('script');

$js_script = "";

//get all javascript in one string:
for ($i = $paths_list->length - 1; $i >= 0 ; $i--) {
   	$script = $paths_list->item($i);
   	
   	if($script->getAttribute('type') == 'text/javascript'){
   		$js_path = $script->getAttribute('src');
   		
   		//is the script from my servor:
   		//ie the stript does not begin by http
   		if(substr_compare('http', $js_path, 0, 4, true) != 0 ){
   			//if ths strip is not internal:
   			if($js_path != ""){
	   			//save the script:
	   			$js_script = file_get_contents($site_folder . $js_path) . $js_script ;
   			}
   			else{
   				$js_script = $script->nodeValue . $js_script ;
   			}
	   		//remove the node:
	   		$script->parentNode->removeChild($script);
   		}
   	}
   	else{
   		echo 'this script is not javascript : ' .  $script->getAttribute('type') . "/n";
   	}
}

//add script node with $js_script inside:
$new_script_node = $doc->createElement('script', $js_script);
$new_script_node->setAttribute('type', 'text/javascript');

$doc->getElementsByTagName('head')->item(0)->appendChild($new_script_node);


//extract the path of links to get css:
$paths_list = $doc->getElementsByTagName('link');
$css_script = "";

for ($i = $paths_list->length - 1; $i >= 0 ; $i--) {
   	$link = $paths_list->item($i);
   	
   	if($link->getAttribute('type') == 'text/css'){
   		$css_path = $link->getAttribute('href');
   		
   		$css_script = file_get_contents($site_folder . $css_path) . $css_script ;
   		//remove the node:
   		$link->parentNode->removeChild($link);
   	}
   	else{
   		echo 'this script is not css : ' .  $css->getAttribute('type') . "/n";
   	}
}

//add css node with $css_script inside:
$new_link_node = $doc->createElement('style', $css_script);
$new_link_node->setAttribute('type', 'text/css');

$doc->getElementsByTagName('head')->item(0)->appendChild($new_link_node);

//save result as index.html:
$file_to_create = $site_folder . "/index_before_compress.html";
$doc->saveHTMLFile($file_to_create);
?>
