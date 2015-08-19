<?php

class cNewsletterBase extends cBase {
	
	public $arrTree;
	public $treeid;
	public $arrLinks = array();
	public $previewMode;
	
	function __construct($treeid) {
		parent::__construct();
		
		$this->treeid = $treeid;

		$this->setPageInformation();
		
		tuksiNewsletter::$instance = $this;
	}
	
	function setPreviewMode( $state ) {
		$this->previewMode = $state;
	}
	
	function getHTML() {
		$html = $this->getHtmlNewsletter();
		if( $this->previewMode ) {
						$html = str_replace("[TRACKINGID]", $_GET->getStr('t'), $html);  
		}
		
		return $html;
	}
	
	function getText() {
		return $this->getTextNewsLetter();
	}
	
	function setPageInformation(){
		
		$objDB = tuksiDB::getInstance();
		
		//finder pagetemplate
		$sqlNews = "SELECT pt.classname,t.* FROM cmstree t,pg_page_template pt ";
		$sqlNews.= "WHERE pt.template_type = '2' AND pt.id = t.pg_page_templateid AND t.id = '".$this->treeid."' order by t.name";
		
		$arrRsNews = $objDB->fetchItem($sqlNews);

		if($arrRsNews['num_rows'] == 1) {
			$this->arrTree = $arrRsNews['data'];
			$this->setPageArray($this->arrTree);
		}

	}	

	function getTitle() {
		return $this->arrTree['pg_browser_title'];
	}
	
	function getHtmlNewsletter(){

		$html = "";
		
		$orgTpl = (isset($this->arrTree['template'])) ? $this->arrTree['template'] : '';
		$orgClass = $this->arrTree['classname'];
		
		if (preg_match("/^(.*).tpl$/", $this->arrTree['classname'], $m)) {
			$this->arrTree['template'] =  "pages/newsletter/" . $m[1] . ".html.tpl";
			$this->arrTree['classname'] = "pNewsletterBase";
		} else {
			$this->arrTree['template']=  "pages/newsletter/" . $this->arrTree['classname'] . ".html.tpl";
		}
		if (class_exists($this->arrTree['classname'])) {
			$this->objPageTemplate = new $this->arrTree['classname']();
			$html = $this->objPageTemplate->getHtml();
		} else {
			$error = "Page class not found " . $page->classname . ".class.php";
		}
		$this->arrTree['template'] = $orgTpl ;
		$this->arrTree['classname'] = $orgClass;
		
		return $html;
	}
	
	function getTextNewsLetter(){
		
		$html = "";
		
		$orgTpl = $this->arrTree['template'];
		$orgClass = $this->arrTree['classname'];
		
		if (preg_match("/^(.*).tpl$/", $this->arrTree['classname'], $m)) {
			$this->arrTree['template'] =  "pages/newsletter/" . $m[1] . ".text.tpl";
			$this->arrTree['classname'] = "pNewsletterBase";
		} else {
			$this->arrTree['template']=  "pages/newsletter/" . $this->arrTree['classname'] . ".text.tpl";
		}

		if (class_exists($this->arrTree['classname'])) {
			$this->objPageTemplate = new $this->arrTree['classname']();
			$html = $this->objPageTemplate->getText();
		} else {
			$error = "Page class not found " . $page->classname . ".class.php";
		}
		
		$this->arrTree['template'] = $orgTpl ;
		$this->arrTree['classname'] = $orgClass;
		
		return $html;
	}

	public function addLink($token,$link){
		$this->arrLinks[$token] = $link;
	}
	public function getLinks(){
		return $this->arrLinks;
	}
	
}	
?>
