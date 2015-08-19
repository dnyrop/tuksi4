<?php
include_once(dirname(__FILE__) . "/../include/tuksi_init.php");

tuksiIni::setSystemType('newsletter');

$objNews = tuksiFrontend::getInstance(false,144);
print $objNews->getHtml();

?>