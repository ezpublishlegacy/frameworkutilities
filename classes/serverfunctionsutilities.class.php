<?php

class ServerFunctionsUtilities
{

	static function processParameters($parameters, $keys=false, $additional=false){
		if(is_array($parameters)){
			if($keys && is_array($keys)){
				if(($ValuesLength=count($parameters)) !== ($KeysLength=count($keys))){
					array_splice($keys, $ValuesLength);
				}
				$parameters=array_combine($keys, $parameters);
			}
			foreach($parameters as $Key=>$Value){
				if(is_string($Value) && $Value!==''){
					$Parameters[$Key]=$Value;
					if(is_numeric($Value)){
						$Parameters[$Key]=strpos($Value, '.')!==false ? (float)$Value : (int)$Value;
					}
				}
			}
			return $additional ? array_merge($Parameters, $additional) : $Parameters;
		}
		return false;
	}

	static function processResults($results, $parameters, $fixed=false){
		if(is_array($results)){
			$parameters=array_change_key_case($parameters);
			foreach($results['rows'] as $Key=>$Value){
				$SortColumn[$Key]=$Value[$parameters['sort']];
				$FixedColumn[$Key]=$Value[$fixed];
			}
			if($Parameters['sort']==$fixed){
				array_multisort($SortColumn, (int)$parameters['order'] ? SORT_ASC : SORT_DESC, $results['rows']);
			}else{
				array_multisort($SortColumn, (int)$parameters['order'] ? SORT_ASC : SORT_DESC, $FixedColumn, SORT_ASC, $results['rows']);
			}
			if(count($results['rows'])>$parameters['limit']){
				$results['row_count']=(int)$parameters['limit'];
				$results['rows']=array_slice($results['rows'], $parameters['offset'], $parameters['limit']);
			}
			if($parameters['order']!==false){
				$parameters['order']=$parameters['order'] ? 'asc' : 'desc';
			}
			return array_merge($parameters, $results);
		}
		return false;
	}

}

?> 
