<?php

class ShuffleOperator
{
	var $Operators;

	function __construct(){
		$this->Operators=array('shuffle');
	}

	function &operatorList(){
		return $this->Operators;
	}

	function modify(&$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters){
		shuffle($operatorValue);
		return true;
	}
}

?>
