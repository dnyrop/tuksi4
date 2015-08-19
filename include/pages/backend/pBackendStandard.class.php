<?php

/**
 * pBackendStandard 
 * 
 * @uses pBackendBase
 * @package tuksiBackendPage 
 * @author Henrik Jochumsen <hjo@dwarf.dk> 
 */
class pBackendStandard extends pBackendBase {

	private $arrTabs = array();
	private $showContent = true;
	
	 //class for the content area returns with modules
	function __construct() {
		parent::__construct();

		$this->tplPage->assignByRef('tabs',$this->arrTabs);
	}
	
	public function addTab($name,$id,$isactive = false){
		
		if($isactive) {
			$this->tplPage->assign('areaid',$id);
		}
		
		$this->arrTabs[] = array(	'name' => $name,
															'isactive' => $isactive,
															'id' => $id);
	}
	
	public function clearContent(){
		$this->showContent = false;
	}
	public function getHtml(){
		
		$this->tplPage->assign("showcontent",$this->showContent);
		
		
		
		return parent::getBaseHtml(parent::getHtml());
	}
}
?>
