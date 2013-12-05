<?php

class HTMLShortenOperator
{
	var $Operators;

	function __construct(){
		$this->Operators=array('html_shorten');
	}

	function operatorList(){
		return $this->Operators;
	}

	function namedParameterPerOperator(){
		return true;
	}

	function namedParameterList(){
		return array(
			'html_shorten'=>array(
				'wc_limit'=>array('type'=>'numeric', 'required'=>false, 'default'=>50),
				'append'=>array('type'=>'string', 'required'=>false, 'default'=>'...')
			)
		);
	}

	function modify($tpl, &$operatorName, &$operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, &$namedParameters){
		if(!in_array(mb_detect_encoding($operatorValue), array('ISO-8859-1', 'UTF-8'))){
			$operatorValue=iconv(mb_detect_encoding($operatorValue), "ISO-8859-1//TRANSLIT", $operatorValue);
		}
		preg_replace('/[ \t]+/s', ' ', preg_replace('/[\r\n]+/s', "\n", $operatorValue));
		
		
		$operatorValue = preg_replace("/^\s*</mus", "<", $operatorValue);
		$operatorValue = preg_replace("/>\s*</mus", "><", $operatorValue);
		$operatorValue = preg_replace("/\n|\r/mus", " ", $operatorValue);
		
		eZDebug::writeDebug($operatorValue);

		$SaveTemp=$operatorValue;
		$SaveTemp2=$operatorValue;
		preg_match_all("/<[^>]*?>/", $SaveTemp, $TagMatches);
		if($namedParameters['wc_limit']==0){
			if(count($TagMatches[0])){
				$SaveTemp3=preg_replace("/<p>(&nbsp;|\s)*?<\/p>/", "", $SaveTemp2);
				$Out=preg_replace("/(<[^>]*?>)(\s*)?$/", $namedParameters['append'].'$1$2', $SaveTemp3);
			}else{
				$Out=preg_replace("/(\S)(\s*?)$/", '$1'.$namedParameters['append'].'$2', $SaveTemp2);
			}
			$operatorValue=$Out;
			return true;
		}else{
			$ResultString='';
			$OpenTags=array();
			$MaxLength=$namedParameters['wc_limit'];
			$EndString=$namedParameters['append'];
			$Cropped=false;

			foreach($TagMatches[0] as $Key=>$ThisTagMatch){
				$SplitArray=preg_split('/'.preg_quote($ThisTagMatch, '/').'/', $SaveTemp, 2);
				$TagType=preg_replace("/ .*\/?>/", ">", $ThisTagMatch);
				$TagTypeOpen=preg_replace("/\//", "", $TagType);
				if(strlen($SplitArray[0])>$MaxLength && !$Cropped){
					$WordEnd=strpos($SplitArray[0], ' ', $MaxLength);
					if(!$WordEnd){
						$WordEnd=strlen($SplitArray[0]);
					}
					$ResultString.=substr($SplitArray[0], 0, $WordEnd);
					$Cropped=true;
					$ResultString.=$EndString;
				}else{
					if(!$Cropped){
						$ResultString.=$SplitArray[0];
						$Directional=(strpos($TagType, '/')===false) ? 1 : -1;
						$TagTypeOpen=preg_replace("/\//", "", $TagType);
						if(array_key_exists($TagTypeOpen, $OpenTags)){
							$OpenTags[$TagTypeOpen]=$OpenTags[$TagTypeOpen]+$Directional;
						}else{
							$OpenTags[$TagTypeOpen]=$Directional;
						}
					}
					$MaxLength=$MaxLength-strlen($SplitArray[0]);
				}
				if(!$Cropped){
					$ResultString.=$ThisTagMatch;
				}else{
					if(strpos($TagType, '/')==1 && array_key_exists($TagTypeOpen, $OpenTags) && $OpenTags[$TagTypeOpen]>0){
						$ResultString.=$TagType;
						$OpenTags[$TagTypeOpen]=$OpenTags[$TagTypeOpen]-1;
					}
				}
				$SaveTemp=$SplitArray[1];
			}

			if(count($TagMatches[0])==0){
				if(strlen($SaveTemp2)>$MaxLength){
					$WordEnd=strpos($SaveTemp2, ' ', $MaxLength);
					$ResultString.=substr($SaveTemp2, 0, $WordEnd);
					$Cropped=true;
					$ResultString.=$EndString;
				}else{
					$ResultString=$SaveTemp2;
				}
			}

			$operatorValue=$ResultString;
		}
	}
}

?>
