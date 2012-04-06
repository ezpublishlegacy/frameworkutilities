<?php

class CookieOperator
{
	var $Operators;

	function __construct(){
		$this->Operators=array('cookie', 'has_cookie', 'delete_cookie');
	}

	function &operatorList(){
		return $this->Operators;
	}

	function namedParameterPerOperator(){
		return true;
	}

	function namedParameterList(){
		return array(
			'cookie'=>array(
				'name'=>array('type'=>'string', 'required'=>true, 'default'=>''),
				'value'=>array('type'=>'string', 'required'=>false, 'default'=>''),
				'expire'=>array('type'=>'integer', 'required'=>false, 'default'=>0),
				'path'=>array('type'=>'string', 'required'=>false, 'default'=>''),
				'domain'=>array('type'=>'string', 'required'=>false, 'default'=>'.' . $_SERVER["HTTP_HOST"]),
				'secure'=>array('type'=>'boolean', 'required'=>false, 'default'=>false),
				'httponly'=>array('type'=>'boolean', 'required'=>false, 'default'=>false)
			),
			'has_cookie'=>array(
				'name'=>array('type'=>'string', 'required'=>true, 'default'=>''),
				'create'=>array('type'=>'boolean', 'required'=>false, 'default'=>false)
			),
			'delete_cookie'=>array(
				'name'=>array('type'=>'string', 'required'=>true, 'default'=>'')
			)
		);
	}

	function modify(&$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters){
		switch($operatorName){
			case 'has_cookie':{
				$operatorValue=self::hasCookie($namedParameters['name'], $namedParameters['create']);
				break;
			}
			case 'delete_cookie':{
				call_user_func_array('self::setCookie',array($namedParameters['name'], '', time()-3600));
				$operatorValue='';
				break;
			}
			default:{
				$Result=call_user_func_array('self::cookie', $namedParameters);
				$operatorValue=$Result['has_value'] ? $Result['value'] : '';
			}
		}
		return true;
	}

	static function cookie(){
		if($Count=func_num_args() && $Name=func_get_arg(0)){
			$Value=func_get_arg(1);
			if(gettype($Name)=='string' && empty($Value) && self::hasCookie(func_get_arg(0))){
				return array('has_value'=>true, 'value'=>$_COOKIE[$Name]);
			}
			return array('has_value'=>false, 'value'=>call_user_func_array('self::setCookie', func_get_args()));
		}
		return array('has_value'=>false, 'value'=>false);
	}

	static function hasCookie($name='', $create=false){
		if($create){
			return isset($_COOKIE[$name]) ? $_COOKIE[$name] : self::setCookie($name,'null');
		}
		return isset($_COOKIE[$name]);
	}

	static function setCookie(){
		return call_user_func_array('setcookie', func_get_args());
	}
}

?>
