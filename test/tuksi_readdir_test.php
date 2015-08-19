<?
/**
 * Test af tuksi_readdir.
 * 
 * @uses tuksiReadDir
 * @package tuksiText
 */

include('../include/tuksi_init.php');

$objDir = new tuksiReadDir(dirname(__FILE__));

// not files with shell
$objDir->addFilterNotLike("/shell/");

// only files with tuksi
$objDir->addFilterLike("/tuksi/");

print "<pre>";
print_r($objDir->getFileNames());
print "</pre>";
?>