<?
include_once(dirname(__FILE__) . "/../../include/tuksi_init.php");

tuksiIni::setSystemType('backend');

$tablename = $_GET->getStr('tablename');
$layoutid = $_GET->getStr('layoutid');

$objDB = tuksiDB::getInstance();

$objFieldItem = new tuksiFielditemTable($tablename,$layoutid);
$xmlLayout = $objFieldItem->getCompleteLayoutXML();

header('Content-type: application/xml; charset="utf-8"',true);
header('Content-Disposition: attachment; filename="tablelayout_'.$layoutid.'_'.$tablename.'.xml"');
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo $xmlLayout;
?>