<?php

class hListaTest extends hBase {
	
	private $rowid;
	
	function __construct($objMod){
		parent::__construct($objMod);
	}
	
	function before($action){
		$status = true;
		switch ($action) {
			case 'save':$status = $this->beforeSave();break;
			
		}
		return $status;
	}
	
	function after($action){
		$status = true;
		switch ($action) {
			case 'save':$status = $this->afterSave();break;
			
		}
		return $status;
	}
	
	
	function beforeSave(){
		$objPage = tuksiBackend::getInstance();
		$objPage->alert('before save');
		return true;
	}
	
	function afterSave(){
		$objPage = tuksiBackend::getInstance();
		$objPage->alert('after save');
		return true;
	}
	
}
?>