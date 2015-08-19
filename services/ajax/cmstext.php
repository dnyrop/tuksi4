<?php
include_once(dirname(__FILE__) . "/../../include/tuksi_init.php");

tuksiIni::setSystemType('backend');

$objDB = tuksiDB::getInstance();

$strStart = $_POST->getStr('token');

$sqlTokens = "SELECT * FROM cmstext ";
$sqlTokens.= "WHERE token LIKE '".$objDB->realEscapeString($strStart)."%' ORDER BY token";
$rsTokens = $objDB->fetch($sqlTokens);
if($rsTokens['ok'] && $rsTokens['num_rows'] > 0) {
	echo "<ul>";
	foreach ($rsTokens['data'] as $arrToken) {
		if (tuksiIni::$arrIni['setup']['charset'] != 'iso-8859-1') {
			$token = utf8_encode($arrToken['token']);
			$value = utf8_encode($arrToken['value_da']);	
		} else {
			$token = $arrToken['token'];
			$value = $arrToken['value_da'];	
		}

		echo "<li><span class='testtest'>" . $token . "</span> (". $value.")</li>";
	}
	echo "</ul>";
}
?>
