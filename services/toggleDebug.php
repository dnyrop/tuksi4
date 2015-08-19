<?php
include_once(dirname(__FILE__) . "/../include/tuksi_init.php");

tuksiIni::setSystemType('backend');

$objTuksiUser =  tuksiBackendUser::getInstance();
if(!$objTuksiUser->isLogged()){
	die('no access to script');
} else {
	if(isset($_SESSION['backend_debug_active']) && $_SESSION['backend_debug_active']){
		$_SESSION['backend_debug_active'] = false;
	} else {
		$_SESSION['backend_debug_active'] = true;
	}
}
?>