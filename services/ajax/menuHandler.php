<?php
include_once(dirname(__FILE__) . "/../../include/tuksi_init.php");

tuksiIni::setSystemType('backend');

$arrUser = tuksiBackendUser::getUserInfo();

$currentNode = $_GET->getInt('treeid');

$objSitemap = new tuksiBackendSitemap($currentNode);
$objSitemap->loadMenu();

$arrOpenNodes = array();

$openNode = $_GET->getInt('open');

if($openNode > 0) {
	$arrOpenNodes[] = $openNode;
}

$objSitemap->setOpenNodes($arrOpenNodes);

$closeNode = $_GET->getInt('close');
if($closeNode > 0) {
	$objSitemap->closeNode($closeNode);
}

$objSitemap->loadOpenFromSession();

$arrNodes = $objSitemap->getMenu($_SESSION['USERID']);

$tpl = new tuksiSmarty();

$tpl->assign("nodes",$arrNodes);
$tpl->assign("treeid",$currentNode);

$html = $tpl->fetch("ajax/menu.tpl");
if (tuksiIni::$arrIni['setup']['charset'] != 'iso-8859-1')
	echo utf8_encode($html);
else
	echo $html;
?>
