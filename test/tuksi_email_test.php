<?
// * ---------------------------------------------------------------------------------- *
//   Test of eksempel script til tuksi_email modulet
// * ---------------------------------------------------------------------------------- *

if (!$_REQUEST['email'])
	$_REQUEST['email'] = "admin@dwarf.dk";
// POST action
if ($_POST) {
	include("../include/tuksi_email.class.php");

	// Load klasse med navn og e-mail
	$objEmail = new tuksi_email("TUKSI_email test", "admin@dwarf.dk");
 	$objEmail->to_email= $_REQUEST['email'];
	//$objEmail->to_email= "hj@noprobs.dk";

	// Ved udsending til ikke eksisterende e-mail sendes de return til errors_to og retuernemail
	$objEmail->errors_to    = $email_errors;
	$objEmail->returnemail  = $email_errors;
	
	$objEmail->subject			= "Test e-mail";
	$_POST['text'] = str_replace("\r",'',$_POST['text']);

	// Indsætter html og tekst version
	$objEmail->email_text   = stripslashes($_POST['text']);
	$objEmail->email_html   = stripslashes($_POST['html']);

	// Laver e-mail kilde som kan tilgås via $objEmail->emailsourceall
	$objEmail->make_email();

	// Sender e-mail
	//$objEmail->send();

	$objEmail->emailsourceall_cmd = str_replace("'", "\'", $objEmail->emailsourceall);
	
	//$objEmail->emailsourceall_cmd = escapeshellcmd ($objEmail->emailsourceall);
	

	// Emulere en udsending via sendmail
	print system("echo '{$objEmail->emailsourceall_cmd}' | /usr/sbin/sendmail {$objEmail->to_email}");

	// Vis e-mail kilde i browser
	print "<textarea cols=80 rows=20>{$objEmail->emailsourceall_cmd}</textarea>";

}

?>
<form method="POST">
<input name="btnsend" type="submit" value="Send"><br>
E-mail:&nbsp;
<input name="email" value="<?=$_REQUEST['email']?>"><br>
Tekst format:<br>
<textarea name="text" cols=80 rows=20><?=stripslashes($_REQUEST['text'])?></textarea><br>
Html format:<br>
<textarea name="html" cols=80 rows=20><?=stripslashes($_REQUEST['html'])?></textarea><br>
</form>
