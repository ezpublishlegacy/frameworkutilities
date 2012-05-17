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

if(!function_exists('array_key_pascal_case')){
	function array_key_pascal_case(&$array){
		if($array){
			$Values = array_values($array);
			foreach($array as $Key=>$Value){
				$Keys[] = str_replace(' ', '', ucwords(str_replace('_', ' ', $Key)));
			}
			$array = array_combine($Keys, $Values);
		}
	}
}

if(!function_exists('array_extract_key')){
	function array_extract_key($array, $keys){
		return array_intersect_key($array, array_flip($keys));
	}
}

?>