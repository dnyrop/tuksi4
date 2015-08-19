<?php

class cNewsletterMain extends cNewsletterBase {

	function __construct($treeid) {
		
		parent::__construct($treeid);

		tuksiNewsletter::$instance = $this;
		
		$this->setMainTemplate("controls/newsletter/" . __CLASS__ . ".tpl");
		
		$this->addTitle($this->arrTree['name']);
	}

}	
?>