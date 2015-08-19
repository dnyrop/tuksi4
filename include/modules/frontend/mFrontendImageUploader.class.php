<?php

/**
 * Standard module, if you write simpel tpl.
 *
 * @uses mFrontendBase
 * @uses tuksiSmarty
 * @package tuksiFrontendModule
 */
class mFrontendImageUploader extends mFrontendBase {

	function __construct(&$objMod){

		parent::__construct($objMod);

		$this->tpl = new tuksiSmarty();

	}
	
	/**
	 * Get HTML content
	 *
	 * @return string
	 */
	function getHTML() {
		if ($html = $this->checkCache()) {
			return $html;
		}
		
		$pictureid = "frotendupload_" . $this->objMod->id;
		
		if($_GET->getStr('testfile') != "") {
			$this->tpl->assign("img","/uploads".$_GET->getStr('testfile'));
		}
		
		$tuksiText = tuksiText::getInstance();
		
		$popupUrl = "/include/fieldtypes/fieldImageEditor/imageeditorpopup.php?itemid=0&pictureid=". $pictureid ."&PHPSESSID=" . session_id();
		$popupUrl.= "&fieldvalue1=jpg&fieldvalue2=1&fieldvalue3=200x?&hiddenfieldname=testfile&formname=theform&callback=testUpload";
		$onclick = "window.open('$popupUrl',500,800);return false;";
		
		$btnUpload = tuksiFormElements::getButton(array(	'color' => 'black',
																											'value' => $tuksiText->getText("new_picture"),
																											'customaction' => $onclick,
																											'icon' => 'uploadImage'));
		$this->tpl->assign('btnupload',$btnUpload);
		return parent::getHTML();
	}
}
?>