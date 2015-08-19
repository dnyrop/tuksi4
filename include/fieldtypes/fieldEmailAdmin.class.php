<?
// Template Class

/**
 * Enter description here...
 *
 * @package tuksiFieldType
 */

class fieldEmailAdmin extends field {

	function __construct($objField) {
		
		parent::field($objField);
				
		// Loading mail conf
		$this->mail = new tuksiNewsletter(0);
		
		if (!$this->mail->conf['maillist']['listsize']) {
			$this->mail->conf['maillist']['listsize'] = 20;
		}
		if (!$this->mail->conf['maillist']['showpages']) {
			$this->mail->conf['maillist']['showpages'] = 2;
		}

		// * ---------------------------------------------------------------------- *
		// Getting data from list
		// * ---------------------------------------------------------------------- *
		$objDB = tuksiDB::getInstance();
		
		$sqlList = "SELECT * FROM mail_emaillist WHERE id = '{$objField->rowid}'";
		$rsList = $objDB->fetch($sqlList);
		$this->arrMailList = $rsList['data'][0];
	}

	function makeSQL($searchstr, $pagenb) {

		$sqlBase = "SELECT *, date_format(dateadded, '%d/%m-%Y') AS dato FROM mail_email WHERE mail_emaillistid = {$this->objField->rowid} ";
		if ($searchstr) {
			$sqlBase .= " AND (name like '%{$searchstr}%' OR email like '%{$searchstr}%') ";
		}
		$sqlBase.= " ORDER BY name";

		$offset = $pagenb > 0 ? ($pagenb - 1) : 0;
		
		$sqlPage = $sqlBase . " LIMIT " . ($offset * $this->mail->conf['maillist']['listsize']) . ", {$this->mail->conf['maillist']['listsize']} ";

		return array($sqlBase, $sqlPage);
	}

	function getHTML() {
		
		$objDB = tuksiDB::getInstance();
		$objPage = tuksibackend::getInstance();
		$objText = tuksiText::getInstance('fieldtypes/fieldEmailAdmin.tpl');
		
		if ($objPage->action == 'ADDEMAIL' && $objPage->arrPerms['ADD']) {
			$this->addEmail();
		}
		
		if ($objPage->action == 'ADDEMAILLIST' && $objPage->arrPerms['ADD']) {
			$this->addEmailList();
		}
		
		// * ---------------------------------------------------------------------- *
		// 	preparing for pagenavigation
		// * ---------------------------------------------------------------------- *

		if ($_POST->getStr('searchstr_old') != $_POST->getStr('searchstr')) {
			$this->pagenr = 1;
		} elseif ($_POST->getStr('gotopage')) {
			$this->pagenr = $_POST->getStr('gotopage');
		} elseif ($_POST->getStr('pagenr')) {
			$this->pagenr 	= $_POST->getStr('pagenr');
		} else
			$this->pagenr= 1;

		
		list($sqlBase, $sqlPage) = $this->makeSQL($_POST->getStr('searchstr'), $this->pagenr);

		$rs = $objDB->fetch($sqlBase);
		
		$this->searchNbemails = $rs['num_rows'];

		$this->pagetotal = ceil($this->searchNbemails / $this->mail->conf['maillist']['listsize']);

		$HtmlTag = parent::getHtmlStart();
	
		$tpl = new tuksiSmarty(); 
		
		$query = 1;
	
		// * ------------------------------------------------------------------------------------ *
		// Show e-mails
		// * ------------------------------------------------------------------------------------ *

		$rs = $objDB->fetch($sqlPage);
		$strNbsearch = $rs['num_rows'];
		
		$arrEmails = array();
		
		foreach ($rs['data'] as $arrEmail) {
			
			($arrEmail['isvalidated']) ? $isvalidated = " CHECKED ":$isvalidated = "";
			($arrEmail['istest']) ? $istest = " CHECKED ":$istest = "";
			($arrEmail['isblacklisted']) ? $isblacklisted = " CHECKED ":$isblacklisted= "";
			($arrEmail['isdeleted']) ? $isdeleted= "CHECKED":$isdeleted= "";
			$date = empty($arrEmail['dateadded']) ? $arrEmail['datechanged'] : $arrEmail['dateadded'];
	
			$arrEmails[] =  array("name" => $arrEmail['name'], 
														"email" =>  $arrEmail['email'], 
														"id" => $arrEmail['id'], 
														"isvalidated" => $isvalidated,
														"isblacklisted" => $isblacklisted,
														"isdeleted" => $isdeleted,
														"dato" => $date,
														"istest" => $istest);
			}
			
			$tpl->assign('emails',$arrEmails);
			
			
			
		
			if ($strNbsearch) {
		
			// * ------------------------------------------------------------------------------------ *
			// making navigation
			// * ------------------------------------------------------------------------------------ *
		
			$midpage = ceil($this->mail->conf['maillist']['showpages'] / 2);

			if ($this->pagetotal == 1){
							$pagesTop = $objText->getText('page') . " {$this->pagenr}";
			} else {
							$pagesTop = $objText->getText('page') . " {$this->pagenr} " . $objText->getText('pageof') . " {$this->pagetotal}: ";
			
				if ($this->pagenr <= $midpage) {
					$i = 1;
				} elseif (($this->pagenr > $midpage) && ($this->pagenr <= $this->pagetotal) && ($this->pagenr < ($this->pagetotal - $midpage))){
					$i = $this->pagenr - $midpage + 1; 
				} elseif ($this->pagenr > ($this->pagetotal - $midpage)){
					$i = ($this->pagetotal + 1) - $this->mail->conf['maillist']['showpages'];
				} else{
					$i = $this->pagenr + ($this->pagetotal -$this->pagenr) - 4;	
					$i = $this->pagenr  - ($midpage);	
				}
				
				$cnt = 1;
				while (($cnt <= $this->mail->conf['maillist']['showpages']) && ($i <= $this->pagetotal)){
					if ($i == $this->pagenr)
						$pages .= " <font color='#198CA9'>$i</font>";
					else
						$pages .= " <a onclick=\"document.tuksiForm.gotopage.value = '" . $i . "'; saveData(); return false;\" href=\"#\">$i</a>";
					$i++;
					$cnt++;
				}
			}
			if($this->pagenr > 1){
				$leftnav = "<a onclick=\"document.tuksiForm.gotopage.value = '" . ($this->pagenr-1) . "'; saveData(); return false;\" href=\"#\"><img src=\"/themes/default/images/newsletter/rewind.gif\" border=\"0\" valign=\"middle\" width=\"14\" height=\"13\"></a>";
				$fastleftnav = "<a onclick=\"document.tuksiForm.gotopage.value = '1'; saveData(); return false;\" href=\"#\"><img src=\"/themes/default/images/newsletter/fast_rewind.gif\" border=\"0\" valign=\"middle\" width=\"14\" height=\"13\"></a>";
			}
			if($this->pagenr < $this->pagetotal){
				$rightnav = "<a onclick=\"document.tuksiForm.gotopage.value = '" . ($this->pagenr+1) . "'; saveData(); return false;\"  href=\"#\"><img src=\"/themes/default/images/newsletter/forward.gif\" valign=\"middle\" width=\"14\" height=\"13\" border=\"0\"></a>";
				$fastrightnav = "<a onclick=\"document.tuksiForm.gotopage.value = '{$this->pagetotal}'; saveData(); return false;\" href=\"#\"><img src=\"/themes/default/images/newsletter/fast_forward.gif\" valign=\"middle\" width=\"14\" height=\"13\" border=\"0\"></a>";
			}
			$tpl->assign("pagestatus", $pagesTop);
			$tpl->assign("pagenr", $this->pagenr);
			$tpl->assign("pagenav", $pages);
			$tpl->assign("leftnav", $leftnav);
			$tpl->assign("rightnav", $rightnav);
			$tpl->assign("fastleftnav", $fastleftnav);
			$tpl->assign("fastrightnav", $fastrightnav);

		
		
			if ($this->arrError['AddEmailError']) {
				$tpl->assign("addemail_error", $this->arrError['AddEmailError']);
			}
			
			if ($this->arrError['AddEmailListError'])
				$tpl->assign("addemaillist_error",$this->arrError['AddEmailListError']);

		} // END have results
			
		$tpl->assign("searchstr", $_POST->getStr('searchstr'));

		$HtmlTag .= $tpl->fetch('fieldtypes/fieldEmailAdmin.tpl');
		
		return parent::returnHtml("", $HtmlTag);
	}

	function addEmail(){
		
		$objDB = tuksiDB::getInstance();
		$objPage = tuksibackend::getInstance();
		
		$email = $_POST->getStr('form_email');
		$name = $_POST->getStr('form_name');
		
		if(tuksiValidate::isEmail($email)) {

			// * -------------------------------- *
			// Adding e-mail til list
			// * -------------------------------- *
			$sql = "SELECT * FROM mail_email WHERE mail_emaillistid = {$this->objField->rowid} AND email = '{$email}'";
			$rsCheck = $objDB->fetch($sql);
			if ($rsCheck['num_rows'] > 0) {
				$this->arrError['AddEmailError'] = $objPage->cmstext('AddEmailError_exist');
				return false;
			}
			
			// * -------------------------------- *
			// Adding e-mail til list
			// * -------------------------------- *
			$sqlInsertEmail = "INSERT INTO mail_email (mail_emaillistid, email, name, isvalidated, dateadded, cmssitelangid) ";
			$sqlInsertEmail.= "VALUES('{$this->objField->rowid}','{$email}','" . $objDB->realEscapeString($name). "', 1, now(), " . $objPage->arrTree['cmssitelangid'] . ")";
			$objDB->write($sqlInsertEmail);
		} else {
			$this->arrError['AddEmailError'] = $objPage->cmstext('AddEmailError_validate');
			return false;
		}
	}
	
	function addEmailList(){
			
			$filename       = $_FILES["form_liste"]['name'];
			$filename_tmp   = $_FILES["form_liste"]['tmp_name'];
			$filetype       = $_FILES["form_liste"]['type'];

			$objPage = tuksiBackend::getInstance();
			$objDB = tuksiDB::getInstance();
			$objText = tuksiText::getInstance('fieldtypes/fieldEmailAdmin.tpl');			
			
			if (is_uploaded_file($filename_tmp)) {

				$handle = fopen($filename_tmp, "r");
				while (($data = fgetcsv($handle, 1000, ";")) !== false) {

					$email = trim($data[0]);
					$name = isset($data[1]) ? trim($data[1]) : '';
					
					if (!empty($email)) {
						if (tuksiValidate::isEmail($email)) {
							
							$ok = 1;
							
							// * -------------------------------- *
							// Adding e-mail til list
							// * -------------------------------- *
							$sql = "SELECT id FROM mail_email WHERE mail_emaillistid = '{$this->objField->rowid}' AND email = '{$email}'";
							$rsCheck = $objDB->fetch($sql);
							if ($rsCheck['num_rows']) {
								$ok = 0;
								$error[] = "E-mail ($email) is already registered.";
							}
						
					
							if ($ok) {
								// * -------------------------------- *
								// Adding e-mail til list
								// * -------------------------------- *
								$sqlInsertEmail = "INSERT INTO mail_email (mail_emaillistid, email, name, isvalidated, dateadded, cmssitelangid) ";
								$sqlInsertEmail.= "VALUES ('{$this->objField->rowid}', '{$email}', '" . $objDB->realEscapeString($name) . "', 1, now(), " . $objPage->arrTree['cmssitelangid'] . ")";
								$objDB->write($sqlInsertEmail);
								//$error[] = "E-mail added ($email).";
							}				

						} else {
							$error[] = "E-mail ($email) could not be validated. Wrong format.";
						} // END if email is validated
					}
				} // END while each line in file
			} // File is uploaded OK
			else {
				$error[] = $objText->getText('email_nofile');
			}

			if ($error)
				$this->arrError['AddEmailListError'] = "<br>" . join("<br>", $error);
	}
	
	function saveData() {

		$objDB = tuksiDB::getInstance();
		
		list($sqlBase, $sqlPage) = $this->makeSQL($_POST->getStr('searchstr_old'), $_POST->getStr('pagenr_old'));
		
		$rs = $objDB->fetch($sqlPage) or print mysql_error();
		
		foreach($rs['data'] as $arrEmail) {
		
			if ($_POST->getStr('form_deleteemail_' . $arrEmail['id'])) {
				
				// Deleting selected item
				$sqlDelete = "DELETE FROM mail_email WHERE mail_emaillistid = {$this->objField->rowid} AND id = '{$arrEmail['id']}'";
				$objDB->write($sqlDelete);
			
			} else {
		
				// Updating item
				$fuldenavn = $_POST->getStr('form_name_' . $arrEmail['id']);
				$isdeleted = ($_POST->getStr('form_isdeleted_' . $arrEmail['id'])) ? 1 : 0;
				$isblacklisted = ($_POST->getStr('form_isblacklisted_' . $arrEmail['id'])) ? 1 : 0;
				$isvalidated = ($_POST->getStr('form_isvalidated_' . $arrEmail['id'])) ? 1 : 0;
		
				$sqlUpdate = "UPDATE mail_email SET ";
				$sqlUpdate.= " name = '$fuldenavn', ";
				$sqlUpdate.= " isdeleted = '$isdeleted', ";
				$sqlUpdate.= " isblacklisted = '$isblacklisted', ";
				$sqlUpdate.= " isvalidated = '$isvalidated', ";
				$sqlUpdate.= " datechanged = now() ";
				$sqlUpdate.= " WHERE mail_emaillistid = {$this->objField->rowid} AND id = '{$arrEmail['id']}'";
				$objDB->write($sqlUpdate);
			}
		}
	}
		

	function getListHtml() {
		$objDB = tuksiDB::getInstance();
		
		$sql = "SELECT count(*) AS c FROM mail_email WHERE isdeleted = 0 AND isbounced = 0 AND mail_emaillistid='{$this->objField->rowid}'";
		$rsCount = $objDB->fetchItem($sql);

		$count = $rsCount['data']['c'];
										    
		if($this->objMailList->external_list) {
																    
			$sql2 = str_replace("#ROWID#",$this->objField->rowid,$this->objMailList->sql_list_count);
			$rsExt = $objDB->fetchItem($sql2);
			$count += $rsExt['data']['c'];
		}
																											    
		return ($count);
		
	}

} // END Class
?>
