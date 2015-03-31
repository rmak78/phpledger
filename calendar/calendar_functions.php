<?php
function filterInput($str, $type = "text"){
	switch(strtolower($type)){
		case "number":
			if(is_numeric($str))
				return array(true, $str);
			else return array(false, "Invalid input - '$str', required number");
			break;
		case "boolean":
			if($str == "" || $str == 0 || $str == 1 || is_bool($str))
				return array(true, $str);
			else return array(false, "Invalid input - '$str', required boolean");
			break;
		default:
			//$str = addslashes($str);
			$str = htmlspecialchars($str, ENT_QUOTES);
						
			return array(true, $str);
	}
}

function getParameter($name, $type='text', $default=""){
	$v = isset($_REQUEST[$name]) ? trim($_REQUEST[$name]) : $default;
	
	$results = filterInput($v, $type);
	$result_txt = isset($results[1]) ? $results[1] : "";
	if(isset($results[0]) && $results[0] === true){
		return $result_txt;
	}else exit("Error returned '$name' - ".$result_txt);
}

function getTranslatedTxt($txt, $allow_tag = false, $maps = array()){
	$content = $txt;
	
	//$content = unhtmlentities($content);
	$content = htmlspecialchars_decode($content, ENT_QUOTES);
	
	foreach($maps as $map_key=>$map_value){
		$content = str_replace($map_key, $map_value, $content);
	}
		
	$content = urldecode($content);
	$content = stripslashes($content);
	$content = stripslashes($content);
	
	if(!$allow_tag){
		$content = strip_tags($content);
	}	
	
	return $content;
}
?>