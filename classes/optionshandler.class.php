<?php

class OptionsHandler
{

	protected $SchemeName;
	protected $Configuration = false;

	function __construct($scheme=false){
		if($scheme){
			$this->SchemeName = $scheme;
		}
	}

	function export($name, $group=false, $scheme='custom'){
		eZDebug::accumulatorStart('options_export', 'options_total', __METHOD__);
		if(is_array($name) || is_string($name)){
			if($group){
				if(array_key_exists($name, $this->Configuration->Groups)){
					$scheme = $name;
					$name = $this->Configuration->Groups[$name]['options'];
				}
			}
			foreach((array) $name as $Name){
				$Options[$Name] = $this->get($Name);
			}
			$Handler = new self("$this->SchemeName/$scheme");
			$Handler->Configuration = self::createOptionsObject($Options);
			eZDebug::accumulatorStop('options_export');
			return $Handler;
		}
		eZDebug::accumulatorStop('options_export');
	}

	function get($name, $group=false){
		eZDebug::accumulatorStart('options_get', 'options_total', __METHOD__);
		if($group){
			$Groups = $this->Configuration->Groups;
			if(array_key_exists($name, $this->Configuration->Groups) && is_array($this->Configuration->Groups[$name]['options'])){
				foreach($Groups[$name]['options'] as $OptionName){
					$Options[$OptionName] = $this->get($OptionName);
				}
				eZDebug::accumulatorStop('options_get');
				return $Options;
			}
			eZDebug::accumulatorStop('options_get');
			return null;
		}
		if(array_key_exists($name, $this->Configuration->Options)){
			eZDebug::accumulatorStop('options_get');
			return $this->Configuration->Options[$name];
		}
		eZDebug::accumulatorStop('options_get');
		return null;
	}

	function has($name, $group=false){
		eZDebug::accumulatorStart('options_has', 'options_total', __METHOD__);
		if($group){
			if(!isset($this->Configuration->Groups[$name]) || !($name = $this->Configuration->Groups[$name]['trigger'])){
				eZDebug::accumulatorStop('options_has');
				return false;
			}
		}
		if(array_key_exists($name, $this->Configuration->Options)){
			$Value = $this->Configuration->Options[$name];
			if(is_array($Value) || is_bool($Value) || is_string($Value)){
				eZDebug::accumulatorStop('options_has');
				return !empty($Value);
			}
			eZDebug::accumulatorStop('options_has');
			return (bool) $Value;
		}
		eZDebug::accumulatorStop('options_has');
		return false;
	}

	function is(){
		if($Options = func_get_args()){
			eZDebug::accumulatorStart('options_is', 'options_total', __METHOD__);
			$CheckType = is_bool($Options[0]) ? (array_shift($Options) === true) : false;
			if(is_string($Options[1])){
				$Options[1] = preg_split('/\s*\|\s*/', $Options[1]);
			}
			if(array_key_exists($Options[0], $this->Configuration->Options)){
				$Name = $CheckType ? gettype($this->Configuration->Options[$Options[0]]) : $this->Configuration->Options[$Options[0]];
				eZDebug::accumulatorStop('options_is');
				return in_array($Name, $Options[1]);
			}
			eZDebug::accumulatorStop('options_is');
		}
		return null;
	}

	function isEmpty($name){
		if(array_key_exists($name, $this->Configuration->Options)){
			return empty($this->Configuration->Options[$name]);
		}
		return null;
	}

	function process($options, $operator=false){
		if($Scheme = OptionsScheme::fetch($this->SchemeName)){
			eZDebug::accumulatorStart('options_process', 'options_total', __METHOD__);
			if($operator && is_string($operator)){
				$Scheme = $Scheme[$operator];
			}
			$this->Configuration = OptionsScheme::process($Scheme, $options, $this->SchemeName);
			eZDebug::accumulatorStop('options_process');
			return (bool) $this->Configuration;
		}
		return false;
	}

	function set($name, $value, $group=false){
		eZDebug::accumulatorStart('options_set', 'options_total', __METHOD__);
		if($group){
			$Groups = $this->Configuration->Groups;
			if(array_key_exists($name, $this->Configuration->Groups) && is_array($this->Configuration->Groups[$name]['options'])){
				foreach($Groups[$name]['options'] as $Name){
					if(isset($value[$Name])){
						$this->Configuration->Options[$Name] = $value[$Name];
					}
				}
				eZDebug::accumulatorStop('options_set');
				return true;
			}
		}
		if(array_key_exists($name, $this->Configuration->Options)){
			$this->Configuration->Options[$name] = $value;
			eZDebug::accumulatorStop('options_set');
			return true;
		}
		eZDebug::accumulatorStop('options_set');
		return false;
	}

	function translateTo(&$object, $map){
		eZDebug::accumulatorStart('options_translateto', 'options_total', __METHOD__);
		if(is_object($object)){
			foreach($map as $Name=>$Item){
				if($this->has($Name) && (($isMethod=method_exists($object, $Item)) || property_exists($object, $Item))){
					$Option = $this->get($Name);
					if($isMethod){
						call_user_func(array($object, $Item), $Option);
					}else{
						$object->$Item = $Option;
					}
					continue;
					$UnMapped[] = array(
						'name' => $Name,
						'provided' => $Item
					);
				}
			}
			if(isset($UnMapped) && $UnMapped){
				foreach($UnMapped as $Item){
					eZDebug::writeError('The option "'.$Item['name'].'" is unable to be set in the object.', '['.__METHOD__.'] Unable to map: '.$Item['name']);
				}
				eZDebug::accumulatorStop('options_translateto');
				return false;
			}
		}
		eZDebug::accumulatorStop('options_translateto');
		return false;
	}

	static function create($class){
		OptionsScheme::initialize($class);
		return new self($class);
	}

	static function createOptionsObject($options=array(), $groups=array()){
		$Object = new stdClass();
		$Object->Options = $options;
		$Object->Groups = $groups;
		return $Object;
	}

	static function instance($options=false, $scheme=false){
		$Object = new self($scheme);
		if($options && is_array($options)){
			$Object->Configuration = self::createOptionsObject($options);
		}
		return $Object;
	}

}
eZDebug::createAccumulatorGroup('options_total', 'Options Handler');

?>