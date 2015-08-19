#!/pack/php/bin/php -q
<?

// * ------------------------------------------------------------------------------------ *
//   Tjekker om der skal genereres e-mail udfra dato
//   Hvis dato er rigtig genereres e-mail som skrives til spool mappen.
//   Scriptet shell_send_email.php sendes derved e-mails.
//  hjo@dwarf.dk
// * ------------------------------------------------------------------------------------ *
include_once(dirname(__FILE__)."/../../include/tuksi_init.php");

tuksiIni::setSystemType('backend');

$objShell = new tuksiShell(18, 0);

$objSendNewsletter = new tuksiNewsletterSend();

$arrReturn = $objSendNewsletter->checkNewsletterQueue();

if ($arrReturn['ok']) {
	if ($arrReturn['error'])
		$objShell->log($arrReturn['error'], 1);
} else {
	if ($arrReturn['error'])
		$objShell->log($arrReturn['error'], 2);
}


$objShell->end();
?>
