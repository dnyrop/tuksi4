<?php

class tuksiRss {
	
	private $arrItems = array();
	private $arrHeader = array();
	private $autoPermLink = false;
	public $arrError = array();
	public $autoEncode = true;
	
	public function __construct(){
 		$this->tpl = new tuksiSmarty();
	}
 
 
	public function setTitle($str){
		$this->arrHeader['title'] = $this->encode($str);
	}
	
	public function setLink($str){
		$this->arrHeader['link'] =   $this->encode($str);
	}
	
	public function enableAutoPermaLink(){
		$this->autoPermLink = true;
	}
	
	public function disableAutoEncode(){
		$this->autoEncode = true;
	}
	
	public function setDescription($str){
		$this->arrHeader['description'] =   $this->encode($str);
	}
	
	public function setLang($str){
		$this->arrHeader['lang'] =   $this->encode($str);
	}
	
	public function setPubDate($str){
		$this->arrHeader['date'] =   $this->encode($str);
	}
	
	public function setDocs($str){
		$this->arrHeader['docs'] =   $this->encode($str);
	}
	
	public function setManagingEditor($str){
		$this->arrHeader['managingEditor'] =   $this->encode($str);
	}
	
	public function setWebMaster($str){
		$this->arrHeader['webMaster'] =   $this->encode($str);
	}
 
	public function addItem($arrItem){

		if(empty($arrItem['description']) && empty($arrItem['title'])) {
			$this->arrError[] = "An item must have either a title or description";
				return false;
		}
		
		foreach ($arrItem as $key => $value) {
			$arrItem[$key] = $this->encode($value);
		}
		
		if($this->autoPermLink) {
			$arrItem['guid'] = array(	'perma' => true,
																'link' => $arrItem['link']);
		}
		
		$this->arrItems[] = $arrItem;	
	}
		
	public function displayRss(){
		if(($xml = $this->fetchRss()) !== false) {
			$this->setHeaders();
			echo $xml;
		} else {
			return false;
		}
	}
	
	public function fetchRss(){
		
		if(empty(	$this->arrHeader['title'])) {
			$this->arrError[] = "a title must be set";
		}
		
		if(empty($this->arrHeader['link'])) {
			$this->arrError[] = "The URL to the HTML website corresponding to the channel must be set.";
		}
		
		if(empty($this->arrHeader['description'])){
			$this->arrError[] = "";
		}
		
		if(count($this->arrError) > 0){
			return false;
		}
		
		$this->makeHeader();
		$this->makeItems();
		
		return $this->tpl->fetch('rss.tpl');
	 
	}

	private function makeHeader(){
		$this->arrHeader['lastBuildDate'] = date("r");
		$this->tpl->assign('header',$this->arrHeader);
	}
		
	private function makeItems(){
		$this->tpl->assign("items",$this->arrItems);
	}
	
	private function setHeaders(){
		header("Content-Type: application/rss+xml; charset=\"utf-8\"");
	}
	private function encode($str){
		if($this->autoEncode) {
			return utf8_encode($str);	
		} else {
			return $str;
		}
		
	}
	
}
?>