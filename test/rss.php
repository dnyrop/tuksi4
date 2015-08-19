<?php
include_once(dirname(__FILE__) . "/../include/tuksi_init.php");
tuksiIni::setSystemType('backend');

$objRss = new tuksiRss();

$objRss->setTitle("Tuksi nyheder");
$objRss->setDescription("The latest news from the Tuksi");
$objRss->setLink("http://www.tuksi.com");

$objRss->enableAutoPermaLink();


$sqlNews = "SELECT * FROM news";

$objRss->addItem(array(	'title' => 'Helt ny html editor',
												'description' => 'Tuksi har nu fået en helt ny editor',
												'link' => "tuksi.com/",
												'pubDate' => date("r",mktime(12,0,0,7,15,2008))));
												
$objRss->addItem(array(	'title' => 'Upload har aldrig været nemmere',
												'description' => 'Splinter ny upload så du kan uploade direkte fra skrivebordet',
												'link' => "tuksi.com/",
												'pubDate' => date("r")));

if($objRss->displayRss() === false) {
	print_r($objRss->arrError);
}
?>