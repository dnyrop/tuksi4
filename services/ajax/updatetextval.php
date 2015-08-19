<?php
include_once(dirname(__FILE__) . "/../../include/tuksi_init.php");

tuksiIni::setSystemType('backend');

$objTuksiUser =  tuksiBackendUser::getInstance();
if(!$objTuksiUser->isLogged()){
	die('no access to script');
} else {
	$arrUser = $objTuksiUser->getUserFromSession();
	$arrUser['usergroup'] = $objTuksiUser->getUserGroups($arrUser['id']);
	if($arrUser['usergroup'][1]) {
		
		$objDB = tuksiDB::getInstance();
		
		$id = $objDB->escapeString($_GET->getInt('id'));
		$lang = $objDB->escapeString($_GET->getStr('lang'));
		$value = $objDB->escapeString($_GET->getStr('value'));
		
		$sqlLang = "SELECT langcode FROM cmslanguage WHERE isactive = 1 AND langcode = '$lang' ";
		$rsLang = $objDB->fetch($sqlLang);
		if($rsLang['ok'] && $rsLang['num_rows'] == 1) {
			$sqlUpd = "UPDATE cmstext SET value_$lang = '$value' WHERE id = '$id'";
			$arrReturn = $objDB->write($sqlUpd);
			if($arrReturn['ok']) {
				die("1");
			} else {
				die("0");
			}	
		} else {
			die("0");
		}
		
	} else {
		die('no access to script');
	}
}
?>