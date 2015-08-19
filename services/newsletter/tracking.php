<?php
include_once(dirname(__FILE__) . "/../../include/tuksi_init.php");
tuksiIni::setSystemType('frontend');

$objDB = tuksiDB::getInstance();

$tid = $_GET->getInt('trackingid');
$md5 = $_GET->getStr('md5');

$sql = "SELECT * FROM mail_tracking WHERE id = '{$tid}' AND md5 = '{$md5}'";

$rs = $objDB->fetchItem($sql) or print mysql_error();
if ($rs['ok'] && $rs['num_rows'] > 0) {
	$arrTracking = $rs['data'];
	
	if (!$arrTracking['dateviewed_first']) 
		$datenow = ", dateviewed_first = now()";

	$sql = "UPDATE mail_tracking ";
	$sql.= "SET timesviewed = timesviewed +1 , isviewed = 1, dateviewed_last = now() $datenow  ";
	$sql.= "WHERE id = '{$_GET->getInt('trackingid')}'";
	$objDB->write($sql) or print mysql_error();

}
header("Location: /images/graphics/gx_blank.gif");
exit();
?>
