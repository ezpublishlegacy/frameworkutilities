<?php

$eZTemplateOperatorArray = SiteUtils::TemplateOperators('extension/frameworkutilities');

if(!function_exists('array_remove')){
	function array_remove(&$array, $value, $key=false){
		array_splice($array, array_search($value, $array), 1);
	}
}

if(!function_exists('array_swap')){
	function array_swap(&$array, $key1, $key2, $value=null){
		$Value = $array[$key1];
		$array[$key1] = $value!==null ? $value : $array[$key2];
		$array[$key2] = $Value;
	}
}

?>