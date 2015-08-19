#!/pack/bin/php
<?

include(dirname(__FILE__) . '/../../include/tuksi_init.php');

$objShell = new tuksiShell(1, 2);

$objDB = tuksiDB::getInstance(); 

// First get relation between source and destination template files
$objShell->log('Getting templates from source database ', 1);

$objSearch = new tuksiPageGeneratorSearch();
$objSearch->saveTreeDataChildren(185);

$objShell->end();

exit();
?>
