<?php

class tuksiPaging {
	
	private $nbRecords = 0;
	private $pagesize = 0;
	private $page = 0;
	private $nbPages = 0;
	
	public function __construct($nbRecords,$pagesize,$page){
		
		$this->nbRecords = intval($nbRecords);
		$this->pagesize = intval($pagesize);
		$this->page = intval($page);
		
		if($pagesize == 0)
			$this->pagesize = $this->nbRecords;
		
		if($page == 0)
			$this->page = 1;	
			
		$this->nbPages = ceil($this->nbRecords / $this->pagesize);
		
		if($this->page > $this->nbPages)
			$this->page = $this->nbPages;
		
	}
	
	public function getNbRecords(){
		return $this->nbRecords;
	}
	
	public function getCurrentPage(){
		return $this->page;
	}
	
	
	public function getNavigation(){
		
		$arrNav = array('first' => 1,
										'last' => $this->nbPages,
										'prev' => 0,
										'next' => 0);
		
		if($this->page < $this->nbPages) {
			$arrNav['next'] = $this->page + 1;
		}
		if($this->page > 1 && $this->nbPages >= 2) {
			$arrNav['prev'] = $this->page - 1;
		}
		return $arrNav;
	}
	
	
	public function getRecords(){
		
		$offset = ($this->page - 1) * $this->pagesize + 1;
		
		if(($offset + $this->pagesize - 1) < $this->nbRecords){
			$stop = $offset + $this->pagesize - 1;
		} else {
			$stop = $this->nbRecords;
		}
		return array('start' => $offset,'stop' => $stop);
	}
	
	public function getPages($pagesToShow = 0){
		
		if($pagesToShow > 0){
			//if we should show all pages
			if($this->nbPages <= $pagesToShow) {
				return array('start' => 1,'stop' => $this->nbPages);
			} else {
				$center = ceil($pagesToShow/2);
				
				if($this->page <= $center) {
					$start = 1;
					if(($this->nbPages - $page) > $pagesToShow) {
						$stop = $pagesToShow;
					} else {
						$stop = $this->nbPages;
					}
				} elseif ($this->page >= (($this->nbPages - $center + 1))) {
					$stop = $this->nbPages;
					$start = ($this->nbPages - $pagesToShow + 1);
				} else {
					if($pagesToShow % 2){
						$start = $this->page - ($center - 1);
					} else {
						$start = $this->page - ($center);	
					}
					$stop = $this->page + ($center - 1);
				}
				return array('start' => $start,'stop' => $stop);
			}
		}
	}
}

?>