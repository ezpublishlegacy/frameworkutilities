<?php

class OptionsItem
{

	public $Name;

	protected $Options;

	function __construct($name, $options=false){
		$Options = self::Options();
		$this->Name = $name;
		if($options && is_array($options)){
			$Options = array_merge($Options, $options);
			$Options['type'] = preg_split('/\s*\|\s*/', $Options['type']);
		}
		$this->Options = $Options;
	}

	function getDefault(){
		return $this->Options['default'];
	}

	function getType($asString=false, $glue=' | '){
		return $asString ? implode($glue, $this->Options['type']) : $this->Options['type'];
	}

	function isValid($option){
		if(is_array($this->Options['type'])){
			if(in_array(gettype($option), $this->Options['type'])){
				return $this->Options['values'] ? in_array($option, $this->Options['values']) : true;
			}
			return false;
		}
		return true;
	}

	static function Options(){
		return array(
			'type' => null,
			'default' => null,
			'values' => array()
		);
	}

}

?>