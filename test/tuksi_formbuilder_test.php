<?
require_once "include/tuksi_frontend.class.php";	
require_once "include/tuksi_formbuilder.class.php";


$objPage = new tuksi_frontend();

$objForm = tuksi_formbuilder::getInstance(6);
print $objForm->getHtml();


?>