<?php
include_once(dirname(__FILE__) . "/../../include/tuksi_init.php");

tuksiIni::setSystemType('backend');
$tpl = new tuksiSmarty();

$strType = $_GET->getStr('debugtype');

$arrDebug = array();

if($strType == 'frontend') {
	if (!empty($_SESSION['frontend_debug']))
		$arrDebug = $_SESSION['frontend_debug'];
} else {
	$arrDebug = $_SESSION['debug'];
}

if(!empty($arrDebug['error']) && count($arrDebug['error'])) {
	$showtab = 'error';
} elseif(!empty($arrDebug['warning']) && count($arrDebug['warning'])){
	$showtab = 'warning';
} else {
	$showtab = 'log';
}
$tpl->assign('showtab',$showtab);
$tpl->assign('tuksi_debug',$arrDebug);
$tpl->display('debug_full.tpl');
?>
