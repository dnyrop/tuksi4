<?php
include_once(dirname(__FILE__) . "/../include/tuksi_init.php");

tuksiIni::setSystemType('frontend');
tuksiIni::setSiteLangID(3);
//$objP = tuksiBackend::getInstance();

$d = new tuksiSmarty();
$d->fetch("test/test.tpl");

print "<pre>";
print_r($_SESSION['debug']);

$objDebug = tuksiDebug::getInstance();

//print_r($objDebug->fetch());

print_r(tuksiConf::getConf());
?>