<?
// * -------------------------------------------------------------------------------------------------- *
// Test og eksempel på tuksi_form
// * -------------------------------------------------------------------------------------------------- *
include('../include/tuksi_init.php');

$tplForm = new tuksi_smarty();

$objForm = new tuksi_form('form');

if ($_POST->getStr('btnsend')) {
	
	$objForm->validate("string", "name", "String length over 0 chars", 1,9999);
	$objForm->validate("string", "password", "String length over 8 and under 20 chars", 8, 20);
	$objForm->validate("int", "number", "Number between 1 & 50", 1, 50);
	$objForm->validate("email", "email", "Valid e-mail");
	
	if ($objForm->ok()) {

	} else {
		print_r($objForm->getFormErrors());
	}

}

if ($_POST->hasdata()) {
	$objForm->updateForm($tplForm);
}


$tplForm->display("testscripts/tuksi_form.tpl");
?>