<?
include(dirname(__FILE__) . '/../include/tuksi_init.php');

tuksiIni::setSystemType('backend');


$objText = tuksiText::getInstance();

print $objText->getText('headline');
?>