<?php

class productiononlyOperator
{
	var $Operators;

	function __construct(){
		$this->Operators=array('production_only');
	}

	function &operatorList(){
		return $this->Operators;
	}

	function namedParameterPerOperator(){
		return true;
	}

	function namedParameterList(){
		return array(
			'production_only'=>array()
		);
	}

	function modify(&$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters){
		if (file_exists("/mnt/ebs/iamproduction.txt") && strpos($_SERVER[HTTP_HOST], 'thinkcreativeinternal') === false) {
			$GLOBALS['eZDebugEnabled'] = false;
		} else {
			$ini = eZINI::instance();
			$ini->setVariable('OutputSettings', 'OutputFilterName', '');
		}
		return true;
	}
}

?>