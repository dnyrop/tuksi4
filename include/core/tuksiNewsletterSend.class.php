<?php
/**
 * This class handles the creation and sending of a newsletter.
 *
 * @package tuksiNewsletter
 */
class tuksiNewsletterSend {
	
	private $arrNewsletterData = array();

	private $arrError = array();
	private $bOk = true;
	
	/**
	 * Enter description here...
	 *
	 * @param int $newsletterid Newsletters TreeID that should be sent.
	 */
	function __construct(){
		
		tuksiIni::loadNewsletterConf();
	}
	/**
	 * Generates e-mail from newsletter template i letters folder.
	 *
	 * @return array 'ok' and 'error' string
	 */
	function checkNewsletterQueue() {
		
		$arrConf = tuksiConf::getConf();
		
		$objDB = tuksiDB::getInstance();

		// Henter nuværende dato
		$current_year   = date("Y");
		$current_month  = date("n");
		$current_day    = date("d");
		$current_hour   = date("G");

		$log = "";

		// Get Newsletters to be sent in current hour 
		$sql = "SELECT * FROM mail_newslettersent ";
		$sql.= "WHERE issent = 0 AND datetosend = '{$current_year}-{$current_month}-{$current_day} {$current_hour}:00:00'";

		$arrList = $objDB->fetch($sql);

		foreach ($arrList['data'] as $arrNewsletter) {
			$id = $arrNewsletter['id']; 
			$sql = "UPDATE mail_newslettersent SET datestarttosend = NOW(), note = 'Generating mails' WHERE id = '{$id}'";
			$objDB->write($sql);

			$emaillistId = $arrNewsletter['mail_emaillistid']; 
				
			// Nulstil e-mailliste
			$arrEmails = array();
		
			// Hent e-mailliste data fra ID
			$sql  = "SELECT external_list, sql_list, sql_list_count ";
			$sql .= "FROM mail_emaillist ";
			$sql .= "WHERE id = '$emaillistId'";
			$rs = $objDB->fetchItem($sql);
							
			if ($rs['ok'] && $rs['num_rows'] > 0) {
				$externalList = $rs['data']['external_list']; 
				$sqlList = $rs['data']['sql_list']; 
				$sqlListCount = $rs['data']['sql_list_count']; 
			} else {
				$sql = "UPDATE mail_newslettersent SET issent = -1, note = 'Mailinglist not found' WHERE id = '{$id}'";
				$objDB->write($sql);

				continue;
			}
							
			if ($externalList) {
				$sql = $sqlList;
				$arrReturnExt = $objDB->fetch($sql);
				if ($arrReturnExt['ok'] && $arrReturnExt['num_rows']) {
					foreach ($arrReturnExt['data'] as $arrData) {
						// Gemmer fundne e-mail/navn i array med ext = 1
						$arrEmails[$arrData['email']] = array("id" => $arrData['id'], "name"=>$arrData['name'], "ext"=> 1);
					}
				}
			} // End hent ekstern e-mail liste
							
			// Henter lokal e-mailliste
			$sql  = "SELECT id, name, email ";
			$sql .= "FROM mail_email ";
			$sql .= "WHERE mail_emaillistid = '$emaillistId' AND isblacklisted=0 AND isdeleted=0 AND isvalidated=1 AND isbounced=0 ";
							
			$arrReturnInt = $objDB->fetch($sql);
			if ($arrReturnInt['ok'] && $arrReturnInt['num_rows']) {
				foreach ($arrReturnInt['data'] as $arrData) {
					// Gemmer fundne e-mail/navn i array med ext = 0
					$arrEmails[$arrData['email']] = array("id" => $arrData['id'], "name"=>$arrData['name'], "ext"=> 0);
				}
			}
							
			$this->makeEmails($arrNewsletter, $arrEmails);
			
			$sql = "UPDATE mail_newslettersent SET datesent = NOW(), issent = 1, note = 'Mails made in spool folder' WHERE id = '{$id}'";
			$objDB->write($sql);
			
		} // End foreach newsletter
	}
	
	/**
	 * Checks that all folders needed by the newsletter module is made
	 *
	 * @return array 'ok' and 'error' string
	 */
	function checkFolders() {
		$arrConf = tuksiConf::getConf();
		
		tuksiIni::loadNewsletterConf();

		$pathSpool= $arrConf['newsletter']['path']['spool'];
		$pathSpoolSingel= $arrConf['newsletter']['path']['spool_single'];

		$bOk = true;
		$arrError = array();

		if (!file_exists($pathSpool)) {
			$bOk = false;
			$arrError[] = $pathSpool;
		}
		if (!file_exists($pathSpoolSingel)) {
			$bOk = false;
			$arrError[] = $pathSpoolSingel;
		}
		if ($bOk) {
			// Check file permissions. Must be World write access
			$permSpool = fileperms ($pathSpool);
			if (!($permSpool & 0x0002)) {
				$arrError[]=  $pathSpool;
			}
			$permSpoolSingel = fileperms ($pathSpoolSingel);
			if (!($permSpoolSingel & 0x0002)) {
				$arrError[]=  $pathSpoolSingel;
			}

		}
		if (count($arrError)) {
			$bOk = false;
			$error = join(', ', $arrError);
		} else {
			$error = '';
		}
		
		$arrReturn = array('ok' => $bOk, 'error' => $error);

		return $arrReturn;
	} // End checkFolders()

	function makeEmails($arrNewsletter, $arrEmails) {
		
		$objDB = tuksiDB::getInstance();
		
		$arrConf = tuksiConf::getConf();

		$pathSpool = $arrConf['newsletter']['path']['spool'] . "/" . $arrNewsletter['id'];

		// Lav mappe til e-mails
		@mkdir($pathSpool, 0777);
		@chmod($pathSpool, 0777);
			
		// Lav e-mail udfra array
		foreach ($arrEmails as $email => $data) {

			// Checking basic email standard
			if (!tuksiValidate::isEmail($email, true)) {
				continue;
			}
			
			// Lav fil som e-mail indhold skal skrives til			
			$file = $pathSpool ."/". $email;
			
			$trackingid = $this->makeTrackingID($arrNewsletter['id'], $data['id'], $data['ext']);
			
			// Mail must only be made if tracking ID exist.
			if ($trackingid) {
				// indsæt værdier i skabelon					
				$emailMessage = str_replace("[TO_EMAIL]", $email, $arrNewsletter['mailtemplate']);
				$emailMessage = str_replace("[EMAIL]", $email, $emailMessage);
				$emailMessage = str_replace("[NAME]", $data['name'], $emailMessage);
				$emailMessage = str_replace("[TRACKINGID]", $trackingid, $emailMessage);
				$emailMessage = str_replace("[USERID]", $email, $emailMessage);
				$emailMessage = str_replace("[USERKEY]", md5('TukZi' . $email), $emailMessage);
				$emailMessage = str_replace("[SENDDATE]", date("j/n-Y"), $emailMessage);
			
				// Skriv fil til spool mappe		
				$fp = fopen($file, "w");
				fwrite($fp, $emailMessage);
				fclose($fp);
			}
			
		} // End for hver e-mail der skal sendes
	}
	
	/**
	 * Generate mail source from $this->arrNewsletterData
	 * 
	 *
	 */
	function makeMailSource($makeTemplate = 0) {
		$arrConf = tuksiConf::getConf();
		$arrPageConf = tuksiConf::getPageConf($this->arrNewsletterData['newsletter_treeid']);	
	
		$objEmail = new tuksiEmail($this->arrNewsletterData['fromname'], $this->arrNewsletterData['fromemail']);
		if ($this->arrNewsletterData['emailto']) {
			$objEmail->to_email = $this->arrNewsletterData['emailto'];
		} else {
			$objEmail->to_email	= "[TO_EMAIL]";
		}
		
		$objEmail->errors_to    = $arrConf['newsletter']['setup']['errormail'];
		$objEmail->returnemail  = $arrConf['newsletter']['setup']['errormail'];
		
		if($arrPageConf['mail_encoding'] == "utf8") {
			$objEmail->setUTF8 = true;
			$this->arrNewsletterData['subject'] = tuksiTools::encode($this->arrNewsletterData['subject']);
		}
		
		$objEmail->subject 		= $this->arrNewsletterData['subject'];
		$objEmail->email_text   = $this->arrNewsletterData['text'];
		$objEmail->email_html   = $this->arrNewsletterData['html'];
		$objEmail->make_email();
		   
		$emailMessage = $objEmail->emailsourceall;
		
		if (!$makeTemplate) {
			$emailMessage = str_replace("[TO_EMAIL]", $objEmail->to_email, $emailMessage);
			$emailMessage = str_replace("[EMAIL]", $objEmail->to_email, $emailMessage);
			$emailMessage = str_replace("[NAME]", $this->arrNewsletterData['nameto'], $emailMessage);
			$emailMessage = str_replace("[TRACKINGID]", $this->arrNewsletterData['mail_trackingid'], $emailMessage);  
			$emailMessage = str_replace("[USERID]", $objEmail->to_email, $emailMessage); 
			$emailMessage = str_replace("[USERKEY]", md5('TukZi' . $objEmail->to_email), $emailMessage);
			$emailMessage = str_replace("[SENDDATE]", date("j/n-Y"), $emailMessage);       
		}	
		
		$this->arrNewsletterData['mailsource'] = $emailMessage;
		
	}
	
	function makeTrackingID($newslettersentid, $emailId, $isemailexternal = 0) {
		
		$objDB = tuksiDB::getInstance();

		$md5= substr(md5('Track_' . $newslettersentid . '_' . $emailId), 0, 16);
		
		// Lav tracking ID til e-mail
		$sqlInsert  = "INSERT INTO mail_tracking (mail_newslettersentid, emailtypeid, emailid, isemailexternal, md5) ";
		$sqlInsert .= "VALUES('{$newslettersentid}', '1', '{$emailId}', '{$isemailexternal}', '{$md5}')";
		$rsIns = $objDB->write($sqlInsert);

		if ($rsIns['ok']) {
			$this->arrNewsletterData['mail_trackingid'] = $rsIns['insert_id'] . '_' . $md5;
		} else {
			$this->arrNewsletterData['mail_trackingid'] = '';
		}
		 
		return $this->arrNewsletterData['mail_trackingid']; 
	}
	
	function makeNewsletterSentItem() {
		$objDB = tuksiDB::getInstance();
		
		$sqlInsertSent = "INSERT INTO mail_newslettersent (name, mail_newsletterid, mail_fromid, mail_emaillistid, dateadded, sentto) ";
		$sqlInsertSent.= "VALUES('{$this->arrNewsletterData['name']}','{$this->arrNewsletterData['newsletter_treeid']}', '{$this->arrNewsletterData['fromid']}', '{$this->arrNewsletterData['maillistid']}', NOW(), '".$objDB->realEscapeString($this->arrNewsletterData['emailto']) . "')";
		
		$arrRs = $objDB->write($sqlInsertSent) or print mysql_error();
		
		if ($arrRs['ok']) {
			$this->arrNewsletterData['mail_newslettersentid'] = $arrRs['insert_id'];
			return $arrRs['insert_id'];
		} else {
			return 0;
		}
		
	}
	
	/**
	 * Sends single email
	 *
	 */
	function sendSingelEmail(){
				
		$arrConf = tuksiConf::getConf();
		
		if (!$this->arrNewsletterData['emailto']) {
			return;
		}
		
		if (!$this->arrNewsletterData['mailsource']) {
			return;
		}
		
		$file = $arrConf['newsletter']['path']['spool_single'] . "/" . $this->arrNewsletterData['emailto'];
		
		if(!is_dir($arrConf['newsletter']['path']['spool_single'])) {
			mkdir($arrConf['newsletter']['path']['spool_single'], 0777);
		}
		
		file_put_contents($file, $this->arrNewsletterData['mailsource']);
		
		$cmd = "cat '$file' | /usr/sbin/sendmail {$this->arrNewsletterData['emailto']}";
		shell_exec($cmd);
		
		unlink($file);
	}
	
	function setMailListID($mailListId) {
		$objDB = tuksiDB::getInstance();
		
		$sql = "SELECT * FROM mail_emaillist WHERE id = '$mailListId'";
		$rs = $objDB->fetchItem($sql);
		
		if ($rs['ok'] && $rs['num_rows']) {
			$this->arrNewsletterData['maillistid'] = $mailListId;
			return 0;
		} else {
			$this->bOk = false;
			return 1;
		}
	}

	function setFromID($mailFromId) {
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "SELECT * FROM mail_from WHERE id = '$mailFromId'";
		$rs = $objDB->fetchItem($sql);
		
		if ($rs['ok'] && $rs['num_rows']) {
			$arrFrom = $rs['data'];
			$this->arrNewsletterData['fromid'] = $mailFromId;
			$this->arrNewsletterData['fromname'] = $arrFrom['name'];
			$this->arrNewsletterData['fromemail'] = $arrFrom['email'];
			return 0;
		} else {
			$this->bOk = false;
			return 1;
		}
			
	}
	
	function validate($field) {
		if (isset($this->arrError[$field]))
			return false;
		else
			return true;
	}
	function setFromName($emailName) {
		if ($emailName) {
			$this->arrNewsletterData['emailname'] = $emailName;
			return true;
		} else {
			$this->setError('fromname');
			return false;
		}
	}
	
	function setNewsletterID($newsletterid) {
		if ($newsletterid) {
			$this->arrNewsletterData['newsletter_treeid'] = $newsletterid;
			return 0;
		} else {
			$this->setError('newsletter_treeid');
			return 1;
		}
	}
	function setName($name) {
		if ($name) {
			$this->arrNewsletterData['name'] = $name;
			return 0;
		} else {
			$this->setError('name');
			return 1;
		}
	}
	
	function setToEmail($emailTo) {
		if ($emailTo) {
			$this->arrNewsletterData['emailto'] = $emailTo;
			return 0;
		} else {
			$this->setError('emailto');
			return 1;
		}
	}
	
	function setToName($name) {
		if ($name) {
			$this->arrNewsletterData['nameto'] = $name;
			return 0;
		} else {
			$this->setError('nameto');
			return 1;
		}
	}

	function setDateToSend($date) {
		preg_match("/^([1-9]|0[1-9]|[12][0-9]|3[01])\-([1-9]|0[1-9]|1[012])\-(19[0-9][0-9]|20[0-9][0-9])\ ([0-9]|[0-1][0-9]|2[0-3])$/", $date, $arrDate);

		if (count($arrDate) == 5) {
			$timestamp = mktime($arrDate[4],0,0,$arrDate[2],$arrDate[1],$arrDate[3]);
			if ($timestamp < time()) {
				$this->setError('date', 2);
				return 2;
			}
		} else {
			$this->setError('date');
			return 1;
		}
		$this->arrNewsletterData['datetosend'] = $arrDate[3] . '-' . $arrDate[2] . '-' . $arrDate[1] . ' ' . $arrDate[4] . ':00:00';
					
		return 0;
	}

	function setError($name, $status = 1) {
		$this->bOk = false;
		$this->arrError[$name] = $status;
	}

	function ok() {
		return $this->bOk;
	}
	
	function sendSingleEmail($emailTo) {
		$arrReturn = array('ok' => true);
		
		$this->setToEmail($emailTo);
		
		if ($this->generateMailContent()) {
			$arrReturn['ok'] = false;
			$arrReturn['error'] = 'error_newsletter_office';
			return $arrReturn;
		}
		
		$emailid = $this->addSingleEmailToSystem($emailTo);
		
		$mail_newslettersentid = $this->makeNewsletterSentItem();
		$this->makeTrackingID($mail_newslettersentid, $emailid);
		
		$this->makeMailSource();
		
		$this->sendSingelEmail();
		
		return $arrReturn;
	}

	function spoolMail() {
		$arrReturn = array('ok' => true);

		if ($this->generateMailContent()) {
			$arrReturn['ok'] = false;
			$arrReturn['error'] = 'error_newsletter_office';
			return $arrReturn;
		}

		$this->makeNewsletterSentItem();

		$this->makeMailSource(1);

		$this->setSpoolSettings();

		return $arrReturn;
	}

	function writeLetterFile() {

	}

	/**
	 *  Saves Mail Source and settings in mail_newslettersent
	 * 
	 * @access public
	 * @return void
	 */
	function setSpoolSettings() {
		$objDB = tuksiDB::getInstance();
	
		$sql = "UPDATE mail_newslettersent ";
		$sql.= "SET datetosend = '{$this->arrNewsletterData['datetosend']}:00:00'";
		$sql.= ", mailtemplate= '" . $objDB->realEscapeString($this->arrNewsletterData['mailsource']) . "' ";
		$sql.= "WHERE id = '{$this->arrNewsletterData['mail_newslettersentid']}'";

		$arrRs = $objDB->write($sql);

		if ($arrRs['ok']) {
			return 0;
		} else {
			return 1;
		}
	}
	
	
	function addSingleEmailToSystem($email) {
		$objDB = tuksiDB::getInstance();
		
		//check if the given e-mail is already in the sytem
		$sqlCheck = "SELECT * FROM mail_email WHERE email = '$email' ";
		$rsCheck = $objDB->fetchItem($sqlCheck);

		if($rsCheck['ok'] && $rsCheck['num_rows'] > 0){
			$emailId = $rsCheck['data']['id'];
		} else {
			$sqlNew = "INSERT INTO mail_email ";
			$sqlNew.= "(email,issingle,dateadded) VALUES ('$email',1,NOW()) ";
			$rsNew = $objDB->write($sqlNew);
			$emailId = $rsNew['insert_id'];
		}
		
		return $emailId;
	}
	/**
	 * Generate e-mail template 
	 * 
	 * @param mixed $treeid 
	 * @param int $mail_fromid 
	 * @access public
	 * @return void
	 */
	function generateMailContent($mail_fromid = 0) {
		$invalid = false;
		
		$objDB = tuksiDB::getInstance();
		
		$arrNewsletter = array();

		$systemType = tuksiIni::$arrIni['setup']['system'];
		tuksiIni::setSystemType('newsletter');
		
		$objNewsletter = tuksiNewsletter::getInstance(false, $this->arrNewsletterData['newsletter_treeid']);

		$this->arrNewsletterData['html'] = $objNewsletter->getHtml();
		// Crude check for MS crap
		if (preg_match('%/\* Style Definitions \*/%', $this->arrNewsletterData['html'])) {
			$invalid = true;
		}

//		print $this->arrNewsletterData['html'];
		$this->arrNewsletterData['text'] = $objNewsletter->getText();
		$this->arrNewsletterData['subject'] = $objNewsletter->getTitle();

		tuksiIni::setSystemType($systemType);

//		exit();
		return $invalid;
	}

}

?>
