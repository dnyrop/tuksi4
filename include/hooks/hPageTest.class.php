<?php

class hPageTest extends hBase {
	
	function __construct($classname){
		
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
		return true;
	}
	
	function afterSave(){
		return true;
	}
	
}
?>