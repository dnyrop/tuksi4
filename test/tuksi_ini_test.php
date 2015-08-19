<?
include('../include/tuksi_init.php');

print "<pre>";

$objIni = tuksiIni::getInstance();

print_r($objIni->getIni());
print "</pre>";
?>
