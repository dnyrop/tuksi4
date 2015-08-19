<?php

/**
 * function for loading the menu in the backend
 *
 */

class tuksiBackendSitemap {
	
	private $treei;
	private $objMenu, $objTopmenu;
	private $topMenuId;
	
	public function __construct($treeid){
		$this->treeid = $treeid;
	}
	
	public function loadTopmenu(){
		
		$arrConf = tuksiConf::getConf();
		
		$this->objTopmenu = new tuksiMenu($arrConf['link']['topmenu_treeid'],$this->treeid);
		
		$this->objTopmenu->openSubNodes = false;
		
		$this->topMenuId = $this->objTopmenu->getTopmenuId($this->treeid);
		
		$this->objTopmenu->setActiveNodes(array($this->topMenuId));
		
		// If no topmenu ID found. ControlPanel ID is used.		
		if ($this->topMenuId == 0) {
			$this->topMenuId = $arrConf['link']['controlpanel_treeid'];
		}
		
	}
	
	public function loadMenu(){
		
		if(!$this->objTopmenu) {
			$this->loadTopmenu();
		}

		$this->objMenu = new tuksiMenu($this->topMenuId,$this->treeid);
			
		$this->objMenu->setOpenNodes(array($this->treeid));
		
		$this->objMenu->loadOpenFromSession();
		
	}
	public function setOpenNodes($arr){
		$this->objMenu->setOpenNodes($arr);
	}
	public function closeNode($id) {
		$this->objMenu->closeNode($id);
	}

	public function loadOpenFromSession() {
		$this->objMenu->loadOpenFromSession();
	}
	
	
	public function getMenu($userid) {
		return $this->objMenu->getMenu($userid);
	}
	
	public function getTopMenu($userid) {
		return $this->objTopmenu->getMenu($userid);
	}
	
	public function getTopmenuId(){
		if(!$this->objTopmenu) {
				$this->loadTopmenu();
		}
		return $this->topMenuId;
	}
	
}
?>