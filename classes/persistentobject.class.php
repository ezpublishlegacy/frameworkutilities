<?php

class PersistentObject
{

	function __sleep(){
		if(method_exists($this,'sleep')){
			$this->sleep();
		}
		return array_keys(get_object_vars($this));
	}

	function __wakeup(){
		if(method_exists($this,'wakeup')){
			$this->wakeup();
		}
	}

	function attribute($attribute, $noFunction=false){
		$definition=$this->definition();
		$fields=$definition['fields'];
		$attributeFunctions=isset($definition["function_attributes"]) ? $definition['function_attributes'] : null;
		if($noFunction===false && isset($attributeFunctions[$attribute])){
			$functionName=$attributeFunctions[$attribute];
			if(method_exists($this, $functionName)){
				return $this->$functionName();
			}
			eZDebug::writeError('Could not find function : "' . get_class($this) . '::' . $functionName . '()".', __METHOD__);
			return null;
		}else if(isset($fields[$attribute])){
			$attributeName=$fields[$attribute];
			$attributeValue=false;
			if(is_array($attributeName)){
				$datatype=$attributeName['datatype'];
				$attributeName=$attributeName['name'];
			}
			if(property_exists($this, $attributeName)){
				$attributeValue=$this->$attributeName;
			}
			if(isset($datatype)){
				settype($attributeValue,$datatype);
			}
			return $attributeValue;
		}else{
			eZDebug::writeError("Attribute '$attr' does not exist", $definition['class_name'] . '::attribute');
			$attributeValue=null;
			return $attributeValue;
		}
	}

	function attributes(){
		$definition=$this->definition();
		$attributes=array_keys($definition['fields']);
		if(isset($definition['function_attributes'])){
			$attributes=array_unique(array_merge($attributes, array_keys($definition['function_attributes'])));
		}
		return $attributes;
	}

	function fields(){
		$definition=$this->definition();
		if($definition){
			return $definition['fields'];
		}
		return array();
	}

	function getIdentifier(){
		$definition=$this->definition();
		if(isset($definition['key']) && $this->hasAttribute($definition['key'])){
			return $this->attribute($definition['key']);
		}
		return false;
	}

	function hasAttribute($attribute){
		$definition=$this->definition();
		$hasAttribute=isset($definition['fields'][$attribute]);
		if(!$hasAttribute && isset($definition['function_attributes'])){
			$hasAttribute=isset($definition['function_attributes'][$attribute]);
		}
		return $hasAttribute;
	}

	function setAttribute(){
		
	}

	protected static function extendDefinition(){
		 return call_user_func_array('array_merge_recursive',func_get_args());
	}

	static function definition(){
		return array();
	}

}

?>