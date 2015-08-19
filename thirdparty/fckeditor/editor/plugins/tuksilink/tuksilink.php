<?
include(dirname(__FILE__) . '/../../../../../include/tuksi_init.php');
tuksiIni::setSystemType('backend');

// All users have access
$objDB = tuksiDB::getInstance();


$tpl = new tuksiSmarty();

$arrHidden = array();

$objModule->value = '';
$objModule->colname = 'link';
$objModule->htmltagname = 'popup';
$objTuksiLink = new fieldLink($objModule);
$objTuksiLink->editorMode();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$objTuksiLink->setValue('file:', '');
	$objTuksiLink->saveData();

	$objModule->value = $objTuksiLink->objField->value;
	$tpl->assign('post', 1);
} else {
	$objTuksiLink->setValue($_GET->getStr('link'), $_GET->getStr('target'));
}


$arrData = $objTuksiLink->getHtml();

$tpl->assign('linkinput', $arrData['html']);

print $tpl->fetch('fieldtypes/fieldFCKEditor_linkpopup.tpl');


?>
