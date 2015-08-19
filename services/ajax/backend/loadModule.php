<?php
include_once(dirname(__FILE__) . "/../../../include/tuksi_init.php");
header('Content-type: text/html; charset="utf-8"');

tuksiIni::setSystemType('backend');

$objTuksiUser =  tuksiBackendUser::getInstance();
if(!$objTuksiUser->isLogged()){
	die('no access to script (Not logged in!)');
} else {
	$arrUser = $objTuksiUser->getUserFromSession();

	$treeid = $_GET->getInt('treeid');
	$tabid = $_GET->getInt('tabid');
	$areaid = $_GET->getInt('areaid');
	$objModule = new tuksiPageGeneratorElementsHtml($treeid, $tabid, $areaid, false, array('moduleid' => $_GET->getInt('moduleid')));

	$arrModules = $objModule->getInsertedElementsHtml();


	$arrUser['usergroup'] = $objTuksiUser->getUserGroups($arrUser['id']);

	//error_log('UserId: ' . $arrUser['id']);
	//error_log(print_r($arrUser['usergroup'], 8));
	// User must be in "User" group
	if($arrUser['usergroup'][8]) {
		
		$objDB = tuksiDB::getInstance();
		
		print_r(utf8_encode($arrModules));
		
	} else {
		die('no access to script (Wrong usergroup)');
	}
}
?>
