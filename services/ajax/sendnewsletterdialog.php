<?php
include_once(dirname(__FILE__) . "/../../include/tuksi_init.php");

tuksiIni::setSystemType('backend');

$objTuksiUser = tuksiBackendUser::getInstance();
if(!$objTuksiUser->isLogged()){
	die('no access to script');
} 

$tpl = new tuksiSmarty();


$arrUser = $objTuksiUser->getUserInfo();

$emailTo =  $arrUser['email'];

$tpl->assign('emailto', $emailTo);

$tpl->display("ajax/sendnewsletterdialog.tpl");

?>
