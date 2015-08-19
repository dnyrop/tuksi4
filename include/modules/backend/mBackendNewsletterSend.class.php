<?php
/**
 * This module handles dispatchment of newsletters 
 *
 * @package tuksiBackendModule
 */
class mBackendNewsletterSend extends mBackendBase {
	
	function __construct(&$objMod){
		parent::__construct($objMod);

		$this->tpl = new tuksiSmarty();
	}	

	function getHtml(){
		

		$objText = $this->getCmsText();
		
		$objPage = tuksiBackend::getInstance();
		$objPage->addJavascript('/javascript/backend/fieldtypes/fieldDatepicker.js');
		$objPage->addJavascript('/javascript/backend/libs/tuksi_divPopup.js');

		$objDB = tuksiDB::getInstance();
		tuksiIni::loadNewsletterConf();
		$arrConf = tuksiConf::getConf();
		
		$cmssitelangid = $objPage->arrTree['cmssitelangid'];
		$newsletterSent = false;
		$returnHtml = "";

		$arrError = array();
		
		if ($this->userActionIsSet('SENDSINGLE') && $objPage->arrPerms["SAVE"]) {
			
			$strInfo = $_POST->getStr('json');
			$arrValues = json_decode(tuksiTools::encode($strInfo));
			
			$objNewsletter = new tuksiNewsletterSend($arrValues->newsletterid);
			
			if ($objNewsletter->setNewsletterID($_POST->getInt('newsletter_choose'))) {
				$arrError['id'] = $objText->getText('error_newsletter');
			}

			if ($objNewsletter->setFromID($_POST->getInt('newsletter_from'))) {
				$arrError['from'] = $objText->getText('error_newsletter_from');
			}
			
			$objNewsletter->setToEmail($arrValues->toemail);
			$objNewsletter->setName($_POST->getStr('newsletter_title'));
			
			if ($objNewsletter->ok()) {
				$arrRes = $objNewsletter->sendSingleEmail($arrValues->toemail);
				if ($arrRes['ok']) {
					$objPage->status($objText->getText('newsletter_send_spool'));
					$newsletterSent = true;
				} else {
					$objPage->alert($objText->getText($arrRes['error']));
				}
			} else {
				$objPage->alert(join("<br />",$arrError));		
			}
		}
		
		if ($this->userActionIsSet('SEND') && $objPage->arrPerms["SAVE"]) {
			
			$ok = true;
			$objNewsletter = new tuksiNewsletterSend();

			if ($objNewsletter->setName($_POST->getStr('newsletter_title'))) {
				$arrError['title'] = $objText->getText('error_newsletter_title');
			} 

			if ($objNewsletter->setNewsletterID($_POST->getInt('newsletter_choose'))) {
				$arrError['id'] = $objText->getText('error_newsletter');
			}
			
			if ($objNewsletter->setFromID($_POST->getInt('newsletter_from'))) {
				$arrError['from'] = $objText->getText('error_newsletter_from');
			}
			
			if ($objNewsletter->setMailListID($_POST->getInt('newsletter_to'))) {
				$arrError['to'] = $objText->getText('error_newsletter_to');
			}
			
			$errorno = $objNewsletter->setDateToSend($_POST->getStr('newsletter_date'));

			if ($errorno == 1) {
				$arrError['date1'] = $objText->getText('error_newsletter_nodate');
			}

			if ($errorno == 2) {
				$arrError['date2'] = $objText->getText('error_newsletter_datesoon');
			}
			
			if ($objNewsletter->ok()) {
				$arrRes = $objNewsletter->spoolMail();
				if ($arrRes['ok']) {
					$objPage->status($objText->getText('newsletter_send_spool'));
					$newsletterSent = true;
				} else {
					$objPage->alert($objText->getText($arrRes['error']) . "<br />" . $objText->getText('error_newsletter_fatale'));		
				}
			} else {
				$objPage->alert(join("<br />",$arrError));		
			}
		}
		
		if (!$newsletterSent) {

			$arrReturn = tuksiNewsletterSend::checkFolders();
		
			if (!$arrReturn['ok']) {
				$objPage->alert($objText->getText('error_letters') . ' (' . $arrReturn['error'] . ')');
				return;
			}
			
			$objStdTpl = new tuksiStandardTemplateControl();		

			$objStdTpl->addHeadline($objText->getText('newsletter_send'));

			$objStdTpl->addInputElement($objText->getText("newsletter_title"), array('id' => "newsletter_title", 'value' => $_POST->getStr('newsletter_title'), 'error' => $arrError['title']));

			// Get newsletters
			$arrOptionNews = array();
			$arrOptionNews[] = array('name' => $objText->getText('choosenewsletter'));
			
			$sqlNews = "SELECT t.name, t.id ";
			$sqlNews.= "FROM cmstree t ";
			$sqlNews.= "INNER JOIN pg_page_template pt ON t.pg_page_templateid = pt.id ";
			$sqlNews.= "WHERE pt.template_type = '2' AND t.isdeleted = '0' AND t.cmssitelangid = '{$cmssitelangid}' ";
			$sqlNews.= "ORDER BY t.datecreated ASC";
			$rsNews = $objDB->fetch($sqlNews);

			foreach($rsNews['data'] as $arrNews) {
				$arrOptionNews[] = array(	'name' => $arrNews['name'], 'value' => $arrNews['id']);
			}

			$objStdTpl->addSelectElement($objText->getText("newsletter_choose"),array('id' => "newsletter_choose", 'options' => $arrOptionNews, 'selected' => $_POST->getInt('newsletter_choose'), 'error' => $arrError['id']));
			
			//get from
			$arrOptionFrom = array();
			$arrOptionFrom[] = array('name' => $objText->getText('choosesender')); 
			
			$sqlFrom = "SELECT * FROM mail_from WHERE cmssitelangid = '{$cmssitelangid}' ORDER BY name";
			$rsFrom = $objDB->fetch($sqlFrom);
			foreach($rsFrom['data'] as $arrFrom) {
				$arrOptionFrom[] = array(	'name' => $arrFrom['name'], 'value' => $arrFrom['id']);
			}
			$objStdTpl->addSelectElement($objText->getText("newsletter_from"),array('id' => "newsletter_from",'options' => $arrOptionFrom,'selected' => $_POST->getInt('newsletter_from'), 'error' => $errError['from']));
			
			//get To
			$arrOptionTo = array();
			$arrOptionTo[] = array('name' => $objText->getText('choosereceiver'));
			
			$toSql = "SELECT * FROM mail_emaillist WHERE cmssitelangid = '{$cmssitelangid}' AND isactive = '1' ORDER BY name";
			
			$rsTo = $objDB->fetch($toSql);
			
			foreach($rsTo['data'] as $arrTo) {
				$arrOptionTo[] = array(	'name' => $arrTo['name'], 'value' => $arrTo['id']);
			}

			$objStdTpl->addSelectElement($objText->getText("newsletter_to"), array('id' => "newsletter_to", 'options' => $arrOptionTo, 'selected' => $_POST->getInt('newsletter_to'), 'error' => $arrError['to']));
			
			

			$objStdTpl->addDateElement($objText->getText("newsletter_date"),array('id' => "newsletter_date","usehour" => true,'value' => $_POST->getStr('newsletter_date'), 'error' => $arrError['date1'] . $arrError['date2']));

			$objStdTpl->addElement($objText->getText("server_time"), date('H:i:s T (\U\T\C O)'));
			
			$this->addButton("SEND", $objText->getText('btn_send_newsletter'),"SAVE");				

			$this->addButton("SENDSINGLE", $objText->getText('btn_send_newsletter_single'),"SAVE","","sendSingleNewsletterDialog('". $objText->getText('sendsinglenewsletter')."');return false;");				
			
			$objStdTpl->addHtml(parent::getHtml());
			
			$returnHtml = $objStdTpl->fetch();
		} 
		return $returnHtml;
	}
	
	function saveData(){
		
	}
}
?>
