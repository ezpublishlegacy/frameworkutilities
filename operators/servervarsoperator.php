<?php

class ServerVarsOperator
{
	var $Operators;

	function __construct(){
		$this->Operators=array('servervars', 'postvars', 'getvars');
	}

	function &operatorList(){
		return $this->Operators;
	}

	function namedParameterPerOperator(){
		return false;
	}

	function namedParameterList(){
		return false;
	}

	function modify(&$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters){
		switch($operatorName){
			case 'postvars':{
				$operatorValue=$_POST;
				break;
			}
			case 'getvars':{
				$operatorValue=$_GET;
				break;
			}
			default:{
				$operatorValue=$_SERVER;
			}
		}
		return false;
	}
}

?>