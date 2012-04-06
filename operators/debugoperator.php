<?php

class debugOperator
{
	var $Operators;

	function __construct(){
		$this->Operators=array('debug', 'kill_debug');
	}

	function &operatorList(){
		return $this->Operators;
	}

	function namedParameterPerOperator(){
		return true;
	}

	function namedParameterList(){
		return array(
			'debug'=>array(
				'label'=>array('type'=>'string', 'required'=>false, 'default'=>'')
			),
			'kill_debug'=>array(
				'active'=>array('type'=>'mixed', 'required'=>false, 'default'=>1)
			)
		);
	}

	function modify(&$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters){
		switch ($operatorName) {
			case 'kill_debug':{
				$GLOBALS['eZDebugEnabled']=!(bool)$namedParameters['active'];
				$operatorValue='';
				return true;
				break;
			}
			case 'debug':{
				eZDebug::writeDebug($operatorValue, (isset($namedParameters['label']) ? $namedParameters['label'] : ''));
				$operatorValue='';
				return true;
				break;
			}
		}
	}
}

?>
