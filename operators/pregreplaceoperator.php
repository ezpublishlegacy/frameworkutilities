<?php

class PregReplaceOperator
{
	var $Operators;

	function PregReplaceOperator(){
		$this->Operators=array('preg_replace', 'preg_match');
	}

	function &operatorList(){
		return $this->Operators;
	}

	function namedParameterPerOperator(){
		return true;
	}

	function namedParameterList(){
		return array(
			'preg_replace'=>array(
				'search'=>array('type'=>'string', 'required'=>true, 'default'=>false),
				'replace'=>array('type'=>'string', 'required'=>true, 'default'=>false)
			),
			'preg_match'=>array(
				'search'=>array('type'=>'string', 'required'=>true, 'default'=>false),
			),
		);
	}

	function modify(&$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters){
		if ($operatorName == 'preg_match') {
			preg_match_all($namedParameters['search'], $operatorValue, $matches);
			$operatorValue=$matches;
		} else {
			$operatorValue=preg_replace($namedParameters['search'], $namedParameters['replace'], $operatorValue);
		}
		return true;
	}
}

?>