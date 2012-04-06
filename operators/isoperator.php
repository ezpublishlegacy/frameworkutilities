<?php

class IsOperator
{
	var $Operators;

	function __construct(){
		$this->Operators=array('is');
	}

	function &operatorList(){
		return $this->Operators;
	}

	function namedParameterPerOperator(){
		return true;
	}

	function namedParameterList(){
		return array(
			'is'=>array(
				'className'=>array('type'=>'mixed', 'required'=>true, 'default'=>false)
			)
		);
	}

	function modify(&$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters){
		if(is_string($namedParameters['className'])){
			$namedParameters['className']=array($namedParameters['className']);
		}else if(!is_array($namedParameters['className'])){
			$ClassNameType=gettype($namedParameters['className']);
			eZDebug::writeError("The datatype [$ClassNameType] is invalid for className. Valid datatypes are array and string.",'Invalid Class Name Datatype');
			$operatorValue=false;
			return false;
		}
		$operatorValue=in_array($operatorValue->classIdentifier(), $namedParameters['className']);
		return true;
	}
}

?>