<?php

class OptionsScheme
{

	protected $Count = 0;
	protected $Options = array();
	protected $OptionList = array();
	protected $Configuration = array();

	function __construct($options=false){
		$Options = self::Options();

		if($options && is_array($options)){
			$BaseScheme = false;
			$this->Configuration = array_merge($Options->Configuration, $options);

			if($this->Configuration['base'] && is_string($this->Configuration['base'])){
				$BaseScheme = self::fetch($this->Configuration['base']);
				foreach($BaseScheme->Options as $Name=>$Option){
					$this->Options[$Name] = $Option;
				}
			}

			if($this->Configuration['scheme'] && is_array($this->Configuration['scheme'])){
				$this->Count = count($this->Configuration['scheme']);
				foreach($this->Configuration['scheme'] as $Name=>$Scheme){
					$this->Options[$Name] = new OptionsItem($Name, $Scheme);
				}
			}

			if(is_array($this->Configuration['group']) && $Groups=$this->Configuration['group']){
				unset($this->Configuration['group']);
				foreach($Groups as $Group){
					$Group = array_merge($Options->Group, $Group);
					if(is_string($Group['options']) && $Group['options']=='%base%' && $BaseScheme){
						$Group['options'] = $BaseScheme->fetchOptionList();
					}
					$this->Configuration['group'][$Group['name']] = $Group;
				}
			}
		}

	}

	function fetchOptionList(){
		if(!$this->OptionList){
			$this->OptionList = array_keys($this->Options);
		}
		return $this->OptionList;
	}

	static function fetch($class, $initialize=true){
		$Exists = isset($GLOBALS['OptionsScheme'][$class]);
		if(!$Exists && $initialize){
			$Exists = self::initialize($class);
		}
		return $Exists ? $GLOBALS['OptionsScheme'][$class] : false;
	}

	static function initialize($class, $function='Options'){
		if(!isset($GLOBALS['OptionsScheme'][$class])){
			if($class && method_exists($class, $function)){
				$GLOBALS['OptionsScheme'][$class] = $class::$function();
				return true;
			}
		}
		return false;
	}

	static function Options(){
		return (object) array(
			'Configuration' => array(
				'associative' => true,
				'base' => '',
				'convert' => false,
				'default' => '',
				'group' => array(),
				'scheme' => array()
			),
			'Group' => array(
				'name' => '',
				'trigger' => '',
				'options' => array()
			)
		);
	}

	static function process(OptionsScheme $object, $options, $class){
		if(!is_array($options)){
			$DefaultOption = false;
			if(!($DefaultOption = $object->Configuration['default'])){
				return false;
			}
			$options = array($DefaultOption => $options);
		}

		if(($Count=count($options)) && self::hasValidOptions($options, $object->Configuration['associative'], $Type)){
			if($Type=='integer'){
				// call function instead of accessing the property in case the property has not been set
				$OptionsList = $object->fetchOptionList();
				if($Count != $object->Count){
					if($object->Configuration['convert'] && $Count>1 && $Count<$object->Count){
						if(is_callable($object->Configuration['convert'])){
							// adjust the values of $OptionsList for the conversion process
							$Successful = $object->Configuration['convert']($OptionsList, $options, $Count);
							if(!$Successful && !is_null($Successful)){
								eZDebug::writeWarning(__METHOD__);
							}
						}
					}
				}
				// convert to an associative array
				foreach($options as $Index=>$Value){
					$Options[$OptionsList[$Index]] = $Value;
				}
				$options = $Options;
				unset($Options);
			}

			// build complete options array
			foreach($object->Options as $Key=>$Option){
				$Valid = true;
				if(isset($options[$Key])){
					if($Valid = $Option->isValid($options[$Key])){
						$Options[$Key] = $options[$Key];
						continue;
					}
				}
				$Options[$Key] = $Option->getDefault();
				if(!$Valid){
					$Message = "The option \"$Key\" for option scheme \"$class\" is required to be of type(s): ".$Option->getType(true, ', ').', using default: '.var_export($Options[$Key], true);
					eZDebug::writeWarning($Message, '['.__METHOD__.'] Invalid Option Type ('.gettype($options[$Key]).')');
				}
			}
			return OptionsHandler::createOptionsObject($Options, $object->Configuration['group']);
		}
		return false;
	}

	protected static function hasValidOptions($options, $associative=true, &$type=false){
		$type = is_integer(key($options)) ? 'integer' : 'string';
		if($type=='integer' && $associative){
			return false;
		}
		foreach($options as $Key=>$Value){
			if(!call_user_func("is_$type", $Key)){
				return false;
			}
		}
		return true;
	}

}

?>