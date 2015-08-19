<?php
//Hent Tuksi grundklassen
include_once(dirname(__FILE__) . "/../../tuksi_init.php");

//create new base object
tuksiIni::setSystemType('backend');

include_once("imageEditor.class.php");

//set new editorp

$editor = new imageEditor($_GET->getStr('pictureid'),$_GET->getInt('itemid'));

if($_GET->getStr('library')) {
	$editor->setInitLibray();
}
//get and print html
echo $editor->getHtml();
?>