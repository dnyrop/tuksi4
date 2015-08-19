<?

/**
 * ??
 *
 * @uses tuksiDebug
 * @uses tuksiSmarty
 * 
 * @package tuksiFrontend
 * 
 */

class mFrontendContact extends mFrontendBase {

	//return the html for the module
	function __construct(&$objMod){

		parent::__construct($objMod);

		$this->tpl = new tuksiSmarty();

	}
	/**
	 * Henter HTML
	 */

	function getHTML() {
		
		$objDB = tuksiDB::getInstance();
		
		if(isset($_SESSION['form_'.$this->objMod->id])) {

			if($_POST->getStr("mContact_sec_" . $this->objMod->id) == $_SESSION['form_'.$this->objMod->id]) {
			
				$form = new tuksi_form("mContact","error",$this->objMod->id);
				
				$form->validate("string","name","Navn skal indtastes",1,256);
				$form->validate("email","email","E-mail skal indtastes");
				$form->validate("string","message","Besked skal indtastes",1,256);
				
				if($form->ok()) {
					$values = $form->getFormValues();
					
					$msg = "Navn: ".$values['mContact_name']['value']."<br>";
					$msg.= "E-mail: ".$values['mContact_email']['value']." <br>";
					$msg.= "Besked: ".$values['mContact_message']['value']." <br>";
					
					$toEmail = $this->objMod->value1;
					$fromEmail = $this->objMod->value1;
					$errorEmail = $this->objMod->value1;
					
					$objEmail = new tuksi_email("",$fromEmail);
					$objEmail->to_email		= $toEmail;
					$objEmail->errors_to    = $errorEmail;
					$objEmail->returnemail  = $errorEmail;
					$objEmail->subject		= $this->objMod->value2;
					
					// Indsætter html og tekst version
					$objEmail->email_text   = strip_tags(stripslashes($msg));
					$objEmail->email_html   = stripslashes($msg);
					
					// Laver e-mail kilde som kan tilgås via $objEmail->emailsourceall
					$objEmail->make_email();
	
					// Sender e-mail
					$objEmail->send();			
					
					if(($arrLink = fieldpagedropdown::makeUrl($this->objMod->value3)) !== false) {
						header("location: ".$arrLink['url']);
						exit();
					}		
				} else {
					$form->updateFormErrors($this->tpl);
					$form->updateForm($this->tpl);
					$this->tpl->assign("submitNumber",$_SESSION['form_'.$this->objMod->id]);
				}
			} else {
				$_SESSION['form_'.$this->objMod->id] = rand(0,100000);
				$this->tpl->assign("submitNumber",$_SESSION['form_'.$this->objMod->id]);
			}
		} else {
			$_SESSION['form_'.$this->objMod->id] = rand(0,100000);
			$this->tpl->assign("submitNumber",$_SESSION['form_'.$this->objMod->id]);
		}
		
		return parent::getHTML();
	}
}
?>
