<?
include('../include/tuksi_init.php');

//$objPage = tuksi::getInstance();
print "<pre>";
$objDB = tuksi_db::getInstance();
$objDB->setDebug(1);

$objDB1 = tuksi_db::getInstance('meer1');

print_r($objDB->fetch('SELECT id, name FROM cmsuser'));

$arr['name'] = "test";
$arrNq['date'] = "NOW()";
$arrInsert = $objDB->insert("cmstestscript",$arr,$arrNq);
print_r($arrInsert);
$arr['name'] = "testupdated";
$arrNq['date'] = "NOW()";
$objDB->update("cmstestscript",$arr,$arrNq," id = ".$arrInsert['insert_id']);
print "<br><br>";

 

print_r($objDB->fetchItem('SELECT * FROM db_beemark.beemarkusers LIMIT 1'));
print "<br><br>";

//print_r($objDB->getHistory());
print "<br><br>";
//print_r($objDB1->getHistory());
//print "<br><br>";
//
tuksi_db::end();
print "</pre>";
?>
