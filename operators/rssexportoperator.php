<?php

class RSSExportOperator
{
	var $Operators;

	function __construct(){
		$this->Operators=array('rssexport');
	}

	function &operatorList(){
		return $this->Operators;
	}

	function namedParameterPerOperator(){
		return true;
	}

	function namedParameterList(){
		return array(
			'rssexport'=>array(
				'nodeid'=>array('type'=>'number', 'required'=>true, 'default'=>0)
			),
		);
	}

	function modify(&$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters){
		$DB=eZDB::instance();
		$RS=$DB->arrayQuery("SELECT * FROM ezrss_export, ezrss_export_item WHERE ezrss_export_item.source_node_id=".$namedParameters['nodeid']." AND ezrss_export_item.rssexport_id=ezrss_export.id");
		$operatorValue=count($RS)?$RS[0]:array();
		return true;
	}
}

?>
