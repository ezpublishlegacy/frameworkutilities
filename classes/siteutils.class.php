<?php

class SiteUtils
{

	static function ConfigSetting($blockName, $varName, $fileName='site.ini', $rootDir='settings', $directAccess=false){
		$INI=eZINI::instance($fileName, $rootDir, null, null, null, $directAccess);
		return ($INI->hasSection($blockName)&&$INI->hasVariable($blockName, $varName))?$INI->variable($blockName, $varName):false;
	}
	
	static function ConfigSettingBlock($blockName, $fileName='site.ini', $rootDir='settings', $directAccess=false){
		$INI=eZINI::instance($fileName, $rootDir, null, null, null, $directAccess);
		return $INI->hasGroup($blockName)?$INI->group($blockName):false;
	}

	static function hasConfigSetting($blockName, $varName, $fileName, $rootDir='settings', $directAccess=false){
		$INI=eZINI::instance($fileName, $rootDir, null, null, null, $directAccess);
		if($INI->hasSection($blockName) && $INI->hasVariable($blockName, $varName)){
			return self::ConfigSetting($blockName, $varName, $fileName, $rootDir, $directAccess);
		}
		return false;
	}

	static function isContentPage(){
		$Data=$GLOBALS['eZRequestedModuleParams'];
		return ($Data['module_name']=='content' && $Data['function_name']=='view' && $Data['parameters']['ViewMode']=='full' && isset($Data['parameters']['NodeID']));
	}

	static function NavigationPart($identifier){
		return self::ConfigSetting("Topmenu_$identifier",'NavigationPartIdentifier','menu.ini');
	}

	static function PHPFileClassList($filename){
		$Classes=array();
		$Tokens=token_get_all(file_get_contents($filename));
		$Count=count($Tokens);
		for($I=2; $I<$Count; $I++){
			if($Tokens[$I-2][0]==T_CLASS && $Tokens[$I-1][0]==T_WHITESPACE && $Tokens[$I][0]==T_STRING){
				$ClassName=$Tokens[$I][1];
				$Classes[]=$ClassName;
			}
		}
		return $Classes;
	}

	static function TemplateOperators($autoloadsExt){
		$TemplateOperators = array();
		foreach(eZDir::findSubitems($OperatorsPath="$autoloadsExt/operators") as $OperatorFile){
			$ClassList=self::PHPFileClassList("$OperatorsPath/$OperatorFile");
			$OperatorArray=array(
				'script'=>"$OperatorsPath/$OperatorFile",
				'class'=>$ClassList[0]
			);
			$OperatorList=array();
			foreach(array($OperatorArray['class']) as $ClassName){
				$ClassObject=new $ClassName();
				$OperatorList=array_merge($OperatorList, $ClassObject->operatorList());
			}
			$OperatorArray=array_merge($OperatorArray, array('operator_names'=>$OperatorList));
			array_push($TemplateOperators, $OperatorArray);
		}
		return $TemplateOperators;
	}

}

?>