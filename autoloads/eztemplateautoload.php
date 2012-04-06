<?php

if(!function_exists('array_remove')){
	function array_remove(&$array, $value, $key=false){
		array_splice($array, array_search($value, $array), 1);
	}
}

$eZTemplateOperatorArray=SiteUtils::TemplateOperators('extension/frameworkutilities');

?>