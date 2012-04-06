<?php

class RedirectOperator
{
	var $Operators;

	function __construct(){
		$this->Operators=array('redirect');
	}

	function &operatorList(){
		return $this->Operators;
	}

	function namedParameterPerOperator(){
		return true;
	}

	function namedParameterList(){
		return array(
			'redirect'=>array(
				'status'=>array('type'=>'mixed', 'required'=>false, 'default'=>false)
			)
		);
	}

	function modify(&$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters){
		// if redirect URI is not starting with scheme://
		if(!preg_match('#^\w+://#', $operatorValue)){
			// we need to make sure we have one and only one slash at the concatenation point between index dir and redirect URI.
			$operatorValue=rtrim(eZSys::indexDir(),'/').'/'.ltrim($operatorValue, '/');
		}
		// redirect to redirect URI by returning given status code and exit.
		eZHTTPTool::redirect($operatorValue, array(), $namedParameters['status']);
		eZExecution::cleanExit();
	}
}

?>