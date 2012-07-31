<?php

class setsessionvarOperator
{
	var $Operators;

	function __construct(){
		$this->Operators=array('setsessionvar');
	}

	function &operatorList(){
		return $this->Operators;
	}

	function namedParameterPerOperator(){
		return true;
	}

	function namedParameterList(){
		return array(
			'setsessionvar'=>array(
				'newvalue'=>array('type'=>'string', 'required'=>true, 'default'=>'')
			)
		);
	}

	function modify(&$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters){
		$http = eZHTTPTool::instance();
		$http->setSessionVariable($operatorValue,  $namedParameters['newvalue']);
		$operatorValue='';
		return true;
		break;
	}
}

?>
