<?php
header('Content-Type: text/html; charset=utf-8');

include_once(dirname(__FILE__) . "/../../../include/tuksi_init.php");

tuksiIni::setSystemType('backend');

$objTuksiUser = tuksiBackendUser::getInstance();
if(!$objTuksiUser->isLogged()) {
	die('no access to script');
} 

switch ($_GET->getStr('action')) {
	case 'search':
		$value = fieldRelatedContentAjaxSearch::ajaxGetValue(urldecode($_GET->getStr('query')), $_GET->getInt('fieldid'), $_GET->getStr('htmltagname'));
		echo utf8_encode($value);
	break;
	
	case 'addRelation':
		$arrData = fieldRelatedContentAjaxSearch::ajaxAddRelation($_GET->getStr('elementId'), $_GET->getInt('fieldid'), $_GET->getStr('htmltagname'), $_GET->getStr('value'), $_GET->getStr('active'));
		echo json_encode($arrData);
	break;
	
	case 'removeRelation':
		$arrData = fieldRelatedContentAjaxSearch::ajaxRemoveRelation($_GET->getStr('elementId'), $_GET->getInt('fieldid'), $_GET->getStr('htmltagname'), $_GET->getStr('value'));
		echo json_encode($arrData);
	break;
	
	case "moveRelation":
		$arrData = fieldRelatedContentAjaxSearch::ajaxChangeSeq($_GET->getStr('elementId'), $_GET->getInt('fieldid'), $_GET->getStr('htmltagname'), $_GET->getStr('value'), $_GET->getStr('direction'));
		echo json_encode($arrData);
	break;
}

//echo $value;


?>