<?php

class tuksiRssReader {
	
	private $url,$xml;
	
	function __construct($url){
		$this->url = $url;
	}
	
	function load(){
		if(($this->xml = @simplexml_load_file($this->url)) !== false){
			return true;
		} else {
			return false;
		}
	}
	
	function getTitle(){
		$title = $this->xml->xpath("/rss/channel/title");
		if(count($title) > 0) {
			return utf8_decode((string) $title[0]);
		}
	}
	
	function getDescription(){
		$description = $this->xml->xpath("/rss/channel/description");
		if(count($description) > 0) {
			return utf8_decode((string) $description[0]);
		}
	}
	
	function getLink(){
		$link = $this->xml->xpath("/rss/channel/link");
		if(count($link) > 0) {
			return utf8_decode((string) $link[0]);
		}
	}
	
	function getItems(){
		
		$arrItems = array();
		
		$arrXmlItems = $this->xml->xpath("/rss/channel/item");
		if(count($arrXmlItems) > 0) {
			foreach($arrXmlItems as $arrItem){
				
				$date = utf8_decode((string) $arrItem->pubDate);
				
				$arrItems[] = array(	'title' => utf8_decode((string) $arrItem->title),
															'link' => utf8_decode((string) $arrItem->link),
															'description' => utf8_decode((string) $arrItem->description),
															'guid' => utf8_decode((string) $arrItem->guid),
															'pubDate' => $date,
															'pubDate_ts' => strtotime($date),
															'pubDate_clean'  => date("d-m-y H:i",strtotime($date)));
			}
		}
		return $arrItems;
	}
}
?>