<?php

class TemplateDataOperator
{
	var $Operators;

	// internal variable for managing template variable hash
	static protected $TemplateVariable=array();

	function __construct(){
		$this->Operators=array('available_classlist', 'available_operators', 'template_set', 'template_merge', 'template_get', 'template_unset', 'template_vars', 'template_namespace');
	}

	function &operatorList(){
		return $this->Operators;
	}

	function namedParameterPerOperator(){
		return true;
	}

	function namedParameterList(){
		return array(
			'available_classlist'=>array(
				'parameters'=>array('type'=>'array', 'required'=>false, 'default'=>array())
			),
			'available_operators'=>array(),
			'template_set'=>array(
				'key'=>array('type'=>'string', 'required'=>true, 'default'=>false),
				'value'=>array('type'=>'mixed', 'required'=>true, 'default'=>false)
			),
			'template_merge'=>array(
				'hash'=>array('type'=>'array', 'required'=>true, 'default'=>false)
			),
			'template_get'=>array(
				'variable'=>array('type'=>'string', 'required'=>true, 'default'=>''),
				'delete'=>array('type'=>'boolean', 'required'=>false, 'default'=>false)
			),
			'template_unset'=>array(
				'variables'=>array('type'=>'array', 'required'=>true, 'default'=>false)
			),
			'template_vars'=>array(
				'name'=>array('type'=>'mixed', 'required'=>false, 'default'=>false),
				'namespace'=>array('type'=>'string', 'required'=>false, 'default'=>'')
			),
			'template_namespace'=>array(
				'current'=>array('type'=>'boolean', 'required'=>false, 'default'=>true)
			)
		);
	}

	function modify(&$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters){
		switch($operatorName){
			case 'available_classlist':{
				$Default=array('node_id'=>2, 'json'=>true);
				$Parameters=array_merge($Default, $namedParameters['parameters']);
				$Node=eZContentObjectTreeNode::fetch($Parameters['node_id']);
				$operatorValue=eZContentObjectTreeNode::availableClassListJsArray(array('node'=>$Node));
				if(!$Parameters['json']){
					$operatorValue=json_decode($operatorValue);
				}
				break;
			}
			case 'available_operators':{
				$operatorValue=array_keys($tpl->Operators);
				break;
			}
			case 'template_set':{
				self::setTemplateVariable($namedParameters['key'], $namedParameters['value']);
				break;
			}
			case 'template_merge':{
				if($namedParameters['hash']){
					foreach($namedParameters['hash'] as $Key=>$Value){
						self::setTemplateVariable($Key, $Value);
					}
				}
				break;
			}
			case 'template_get':{
				$operatorValue=self::getTemplateVariable($namedParameters['variable'], $namedParameters['delete']);
				break;
			}
			case 'template_unset':{
				if($namedParameters['variables']){
					foreach($namedParameters['variables'] as $Name){
						self::deleteTemplateVariable($Name);
					}
				}
				break;
			}
			case 'template_vars':{
				if($namedParameters['namespace']=='$this'){
					$namedParameters['namespace']=$currentNamespace;
				}
				self::getTemplateVariables($tpl, $operatorValue, $namedParameters);
				break;
			}
			case 'template_namespace':{
				$operatorValue=$namedParameters['current'] ? $currentNamespace : array_keys($tpl->Variables);
				break;
			}
		}
	}

	static public function deleteTemplateVariable($key){
		if(isset(self::$TemplateVariable[$key])){
			array_pop(self::$TemplateVariable[$key]);
			if(empty(self::$TemplateVariable[$key])){
				unset(self::$TemplateVariable[$key]);
			}
		}
	}

	static public function getTemplateVariable($key, $delete=false){
		if(isset(self::$TemplateVariable[$key])){
			$Value=end(self::$TemplateVariable[$key]);
			if($delete){
				self::deleteTemplateVariable($key);
			}
			return $Value;
		}
		eZDebug::writeWarning("\"$key\" does not exist in the template variable hash.", __METHOD__);
		return null;
	}

	static public function getTemplateVariables($tpl, &$operatorValue, $parameters){
		if($parameters['name']){
			$operatorValue=$tpl->hasVariable($parameters['name'],$parameters['namespace']) ? $tpl->variable($parameters['name'],$parameters['namespace']) : false;
			return true;
		}
		$operatorValue=$tpl->Variables[$parameters['namespace']];
	}

	static public function includeTemplate(&$tpl, $uri, $variables=false){
		$Initial=array_keys($tpl->Variables['']);
		if($variables){
			foreach($variables as $key=>$value){
				$tpl->setVariable($key, $value);
			}
		}
		$Result=$tpl->fetch($uri);
		foreach(array_diff(array_keys($tpl->Variables['']),$Initial) as $key){
			$tpl->unsetVariable($key);
		}
		return $Result;
	}

	static public function setTemplateVariable($key, $value){
		self::$TemplateVariable[$key][]=$value;
	}

}

?>