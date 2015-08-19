<?
include('../include/tuksi_init.php');


print "<pre>";
$objSitemap = new tuksi_sitemap();
$objSitemap->setLoadAll(true);
//$this->objSitemap->setLoadLevels(1);
//
$arrSitemap = $objSitemap->makeSitemap();


print_r($arrSitemap);
print "</pre>";
?>
