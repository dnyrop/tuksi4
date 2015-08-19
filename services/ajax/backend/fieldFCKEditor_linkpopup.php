<?php
include_once(dirname(__FILE__) . "/../../../include/tuksi_init.php");

tuksiIni::setSystemType('backend');

$objTuksiUser = tuksiBackendUser::getInstance();
if(!$objTuksiUser->isLogged()){
	die('no access to script');
} 


switch ($_GET->getStr('action')) {
	case('parselink') : 
						$value = fieldLink::ajaxGetValue($_GET->getStr('link'));
						print_r($value);
						print "<br>";
	
	default;
}

echo json_encode($value);

?>