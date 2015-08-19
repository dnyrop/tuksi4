<?php
include_once(dirname(__FILE__) . "/../../include/tuksi_init.php");
tuksiIni::setSystemType('frontend');

$objDB = tuksiDB::getInstance();

$trackingid = $_GET->getInt('trackingid');
$md5 = $_GET->getStr('md5');
$moduleid = $_GET->getInt('moduleid');

$sqlTracking = "SELECT t.*, ns.mail_newsletterid as newsletterid,ns.name as sentname ";
$sqlTracking.= "FROM mail_tracking t, mail_newslettersent ns ";
$sqlTracking.= "WHERE t.id = '{$trackingid}' AND t.md5 = '{$md5}' AND ns.id = t.mail_newslettersentid";

$rsTracking = $objDB->fetchItem($sqlTracking) or print mysql_error();

if ($rsTracking['ok'] && $rsTracking['num_rows'] > 0) {
	
	$arrTracking = $rsTracking['data'];
	
	if (!$arrTracking['dateviewed_first']) 
		$datenow = ", dateviewed_first = now()";

	$sqlUpdTracking = "UPDATE mail_tracking SET timeslinked = timeslinked +1,dateviewed_last = now() ";
	$sqlUpdTracking.= "WHERE id = '{$trackingid}'";
	$objDB->write($sqlUpdTracking) or print mysql_error();

	$sqlTrackingElement = "SELECT * FROM mail_trackingelement ";
	$sqlTrackingElement.= "WHERE mail_newsletterelementid = '{$moduleid}' AND mail_trackingid = '{$trackingid}'";
	$rsTrackingElement = $objDB->fetch($sqlTrackingElement) or print mysql_error();
	
	if ($rsTrackingElement['ok'] && $rsTrackingElement['num_rows'] > 0) {
		$sqlUpdate = "UPDATE mail_trackingelement SET timesclicked = timesclicked + 1, dateclicked_last = now() ";
		$sqlUpdate.= "WHERE mail_newsletterelementid = '{$moduleid}' AND mail_trackingid = '{$trackingid}'";
		$r = $objDB->write($sqlUpdate);
		
	} else {
		$sqlInsert = "INSERT INTO mail_trackingelement (mail_newsletterelementid, mail_trackingid, isclicked, dateclicked_first) ";
		$sqlInsert.= " VALUES('{$moduleid}','{$trackingid}', 1, now())";
		$objDB->write($sqlInsert);
	}
	//Hent oplysninger om nyhedsbrevet
	$sqlNewsletter = "SELECT * FROM cmstree WHERE id = '{$arrTracking['newsletterid']}'";
	$rsNewsletter = $objDB->fetchItem($sqlNewsletter) or print mysql_error();
	if ($rsNewsletter['num_rows'] > 0) {
		$arrNewsletter = $rsNewsletter['data'];
	}
}

$sqlContent ="SELECT * FROM pg_content WHERE id = '{$moduleid}'";

$rsContent = $objDB->fetchItem($sqlContent) or print mysql_error();
if ($rsContent['ok'] && $rsContent['num_rows'] > 0) {
  
	$arrElement = $rsContent['data'];
	
	$arrLink = fieldLink::makeUrl($arrElement['link']);
	
 	$arrGets = $_GET->getData();
	unset($arrGets['trackingid']);
	unset($arrGets['md5']);
	unset($arrGets['moduleid']);
	$urlAppend = '';
	foreach ($arrGets as $key => &$value) {
		$urlAppend.= $key . "=" . urlencode($value) . "&";
	} // foreach
  		
	//Ekstra urchin tracking
	$urlAppend.= "utm_source=".urlencode($arrNewsletter['name'])."&";
	$urlAppend.= "utm_medium=email&";
	$urlAppend.= "utm_campaign=".urlencode($arrTracking['sentname'])."&";
	$urlAppend.= "utm_term=".urlencode($arrElement['headline']);
	
	if(empty($arrLink['url']))
		$url = "/";
	else	
		$url = $arrLink['url'];
	
	if(strpos($url,"?")){
		$url.="&".$urlAppend;	
	}else{
		$url.="?".$urlAppend;
	}
	header("Location: ".$url);
	exit();
} else { 
	header("location: /");
	exit();
}
?>
