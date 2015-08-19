<?php
include_once(dirname(__FILE__) . "/../../include/tuksi_init.php");
tuksiIni::setSystemType('frontend');

$objDB = tuksiDB::getInstance();

$trackingid = $_GET->getInt('trackingid');
$md5 = $_GET->getStr('md5');
$linkid = $_GET->getInt('linkid');

$sqlTracking = "SELECT t.*, ns.mail_newsletterid AS newsletterid, ns.name AS sentname ";
$sqlTracking.= "FROM mail_tracking t, mail_newslettersent ns ";
$sqlTracking.= "WHERE t.id = '{$trackingid}' AND t.md5 = '{$md5}' AND ns.id=t.mail_newslettersentid";

$rsTracking = $objDB->fetchItem($sqlTracking) or print mysql_error();
if ($rsTracking['ok'] && $rsTracking['num_rows'] > 0) {
	
	$arrTracking = $rsTracking['data'];
	
	$sql = "UPDATE mail_tracking SET timeslinked = timeslinked +1 WHERE id = '{$trackingid}'";
	$objDB->write($sql) or print mysql_error();
}


	
	$sqlLink = "SELECT * FROM mail_link WHERE id = '{$linkid}'";
	$rsLink = $objDB->fetchItem($sqlLink);
	if($rsLink['ok'] && $rsLink['num_rows'] > 0) {
		
		$arrLink = $rsLink['data'];
		//update stat
		$sqlLinkChk = "SELECT * FROM mail_linktracking ";
		$sqlLinkChk.= "WHERE mail_trackingid = '{$trackingid}' AND mail_linkid = '{$linkid}' AND ";
		$sqlLinkChk.= "mail_newslettersentid = '{$arrTracking['mail_newslettersentid']}' ";
		$rsLinkChk = $objDB->fetchItem($sqlLinkChk);
		if ($rsLinkChk['ok'] && $rsLinkChk['num_rows'] > 0) {
			$sqlUpd = "UPDATE mail_linktracking SET dateclicked_last = now(), ";
			$sqlUpd.= "timeslinked = timeslinked + 1 WHERE id = '{$rsLinkChk['data']['id']}'";
			$objDB->write($sqlUpd);
		} else {
			$sqlIns = "INSERT INTO mail_linktracking ";
			$sqlIns.= "SET dateclicked_last = now(),dateclicked_first = now(), ";
			$sqlIns.= "timeslinked = 1,mail_trackingid = '{$trackingid}',mail_linkid = '{$linkid}',";
			$sqlIns.= "mail_newslettersentid = '{$arrTracking['mail_newslettersentid']}'";
			$objDB->write($sqlIns);
		}
		
		if(empty($arrLink['url']))
			$url = "/";
		else {	
			$arrUrl = fieldLink::makeUrl($arrLink['url']);
			$url = $arrUrl['url'];
		}
		
		$url = str_replace("[newsletterid]",$arrTracking['newsletterid'],$url);
		$url = str_replace("[emailid]",$arrTracking['emailid'],$url);
		$url = str_replace("[trackingid]",$trackingid,$url);
			
		
		//Hent oplysninger om nyhedsbrevet
		$sqlNewsletter = "SELECT * FROM cmstree WHERE id='".$arrTracking['newsletterid']."'";
		$rsNewsletter = $objDB->fetchItem($sqlNewsletter) or print mysql_error();
		if ($rsNewsletter['num_rows'] > 0) {
			$arrNewsletter = $rsNewsletter['data'];
		}
		
		$arrGets = $_GET->getData();
		unset($arrGets['trackingid']);
		unset($arrGets['md5']);
		unset($arrGets['linkid']);
		$urlAppend = '';
		foreach ($arrGets as $key => &$value) {
			$urlAppend.= $key . "=" . urlencode($value) . "&";
		} // foreach
		
		//Ekstra urchin tracking
		$urlAppend.= "utm_source=".urlencode($arrNewsletter['name'])."&";
		$urlAppend.= "utm_medium=email&";
		$urlAppend.= "utm_campaign=".urlencode($arrTracking['sentname'])."&";
		$urlAppend.= "utm_term=".urlencode($arrLink['name']);
			
		if(strpos($url,"?") !== false){
			$url.="&".$urlAppend;	
		}else{
			$url.="?".$urlAppend;
		}
		//print $url;
		header("Location: ".$url);
		exit();
}
header("location: /");
exit();
?>
