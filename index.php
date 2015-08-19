<?php
// * ---------------------------------------------------------------------------------- *
//   Indgangsscript for alle websider, frontend og backend
// * ---------------------------------------------------------------------------------- *
include('include/tuksi_init.php');


if (tuksiIni::$arrIni['setup']['system'] == 'backend') {
	$objPage = tuksiBackend::getInstance();
	
} elseif (tuksiIni::$arrIni['setup']['system'] == 'newsletter') {
	$objPage = tuksiNewsletter::getInstance();
	$objPage->setPreviewMode( true );
} else {

	$objPage = tuksiFrontend::getInstance();
	
	$objDebug = tuksiDebug::getInstance();
	
	//print_r($objDebug->fetch());
	//print_r(tuksiIni::$arrIni);
}

if ($objPage) {
	print $objPage->getHTML();
	//print $objPage->getText();
} else {
	header("Location: /services/error.php?error=nopagefound");
	exit();
}

?>
