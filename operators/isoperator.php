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
				'className'=>array('type'=>'mixed', 'required'=>true, 'default'=>false),
				'descendant'=>array('type'=>'boolean', 'required'=>false, 'default'=>false)
			)
		);
	}

	function modify(&$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters){
		if(is_string($namedParameters['className'])){
			$namedParameters['className']=preg_split('/\s*,\s*/', $namedParameters['className']);
		}else if(!is_array($namedParameters['className'])){
			$ClassNameType=gettype($namedParameters['className']);
			eZDebug::writeError("The datatype [$ClassNameType] is invalid for className. Valid datatypes are array and string.",'Invalid Class Name Datatype');
			$operatorValue=false;
			return false;
		}
		if (!is_object($operatorValue)) {
		    $operatorValue = false;
		    return false;
		}
		$is=self::is($operatorValue, $namedParameters['className']);
		if(!$is && $namedParameters['descendant']){
			$PathArray = array_reverse($operatorValue->pathArray());
			array_shift($PathArray);
			foreach($PathArray as $Item){
				if($is = self::is(eZContentObjectTreeNode::fetch($Item), $namedParameters['className'])){
					break;
				}
			}
		}
		$operatorValue = $is;
		return true;
	}

	static function is(eZContentObjectTreeNode $object, $identifier){
		return in_array($object->classIdentifier(), $identifier);
	}

}

?>