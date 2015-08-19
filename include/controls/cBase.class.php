<?php

/**
 * Base class for alle html pages 
 * 
 * @package tuksiBase 
 * @author Henrik Jochumsen <hjo@dwarf.dk> 
 */
class cBase {
	
	// contains page information as: title, metatags osv.
	public $arrPage = array();
	public $tplMain,$tplMain_filename;


	/**
	 * Contains page class object 
	 * 
	 * @var object 
	 */
	public $objPageTemplate;
	
	function __construct() {
		$this->tplMain = new tuksiSmarty();
	}

	/**
	 * Return current page information 
	 * 
	 * @return array  
	 */
	function getPage() {
		return $this->arrPage;
	}

	function setPageVar($var, $value) {
		$this->arrPage[$var] = $value;
	}

	function setPageArray($arrValues) {
		if (count($arrValues))
		foreach ($arrValues as $var => $value)
			$this->arrPage[$var] = $value;
	}

	function setUserVar($var, $value) {
		$this->arrUser[$var] = $value;
	}
	
	function addTitle($str, $clear = false) {
		if (isset($this->arrPage['title']) && !$clear)
			$this->arrPage['title'] .=  $str;
		else 
			$this->arrPage['title'] =  $str;	
	}
	
	function addTitleFront($str) {
		$this->arrPage['title'] =  $str . $this->arrPage['title'];	
	}
	
	function addHeadline($str) {
		$this->arrPage['headline'] = $str;
	}

	function addMetaKeyword($strKeywords) {
		if(!empty($strKeywords)) {
			if (isset($this->arrPage['metakeywords']))
				$this->arrPage['metakeywords'] .= " ".strip_tags(str_replace("\n", "", $strKeywords));
			else
				$this->arrPage['metakeywords'] = strip_tags(str_replace("\n", "", $strKeywords));
		}
	}

	function addMetaDescription($strDesc) {
		if(!empty($strDesc)) {
			if (isset($this->arrPage['metadescription']))
				$this->arrPage['metadescription'] .= " ".strip_tags(str_replace("\n", "", $strDesc));
			else
				$this->arrPage['metadescription'] = strip_tags(str_replace("\n", "", $strDesc));
		}
	}

	function addMetaTag($metaKey, $arrVars, $force = true) {
		if (!empty($metaKey) && count($arrVars)) {
			// Abort if override isn't forced
			if (!$force && isset($this->arrPage['metatags'][$metaKey])) return;

			$arrCont = array();
			foreach ($arrVars as $param => $value) {
				$arrCont[] = sprintf("%s=\"%s\"", $param, $value);
			}
			$this->arrPage['metatags'][$metaKey] = "<meta " . join(' ', $arrCont) . " />";
		}
	}

	function addJavascript($jsSrc) {
		if (isset($this->arrPage['javascript']) && is_array($this->arrPage['javascript'])) {
			$this->arrPage['javascript'][] = $jsSrc;
			//make sure the script isnt loaded more than once
			$this->arrPage['javascript'] = array_unique($this->arrPage['javascript']);
		} else {
			$this->arrPage['javascript'] = array();
			$this->arrPage['javascript'][] = $jsSrc;
		}
	}
	
	function addOnload($onload) {
		if (isset($this->arrPage['onload']) && is_array($this->arrPage['onload'])) {
			$this->arrPage['onload'][] = $onload;
			$this->arrPage['onload'] = array_unique($this->arrPage['onload']);
		} else {
			$this->arrPage['onload'] = array();
			$this->arrPage['onload'][] = $onload;
		}
	}
	
	function putContent($content) {
		$this->arrPage['content'] =  $content;
	}
	
	function isFrontpage() {
		$this->arrPage['isfrontpage'] =  true;
	}
	
	function setMainTemplate($filename) {
		$this->tplMain_filename = $filename;
	}
	
	function getHTML() {
		
		$this->arrPage['treeinfo'] = $this->arrTree;
		
		$this->tplMain->assign('page', $this->arrPage);
		
		return $this->tplMain->fetch($this->tplMain_filename);
	}
	
} // End tuksi_frontend klasse
