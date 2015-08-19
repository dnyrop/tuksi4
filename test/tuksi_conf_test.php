<?
include('../include/tuksi_init.php');

print "<pre>";


$arrConf = tuksiConf::getConf();
print_r($arrConf);

$objC = tuksiConf::getInstance();

$objC->arrConf['tslkfjslfkjsdfklest'] = '123123';

$arrPageConf = tuksiConf::getPageConf(13);

print "Page Conf ID = 13<br>";
print_r($arrPageConf);
print "</pre>";
?>
