<?php
/**
 * base class for handling the hook system
 *
 */


class hBase {
	
	public $objMod;
	public $arrValues = array();
	
	function __construct($objMod){
		$this->objMod = $objMod;
	}
	
	function addValue($name,$value) {
		$this->arrValues[$name] = $value;	
	}
	
	
	function before($action){
		return true;
	}
	
	function after($action){
		return true;
	}

}
?>