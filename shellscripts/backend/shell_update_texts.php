#!/pack/bin/php
<?

include(dirname(__FILE__) . '/../../include/tuksi_init.php');

$objShell = new tuksiShell(1, 2);

$objDB = tuksiDB::getInstance(); 

if (!$objDBSync = tuksiDB::getInstance('sync')) {
	print "error connection to sync DB\n";
	$objShell->end();
	exit();
}
 

// First get relation between source and destination template files
$objShell->log('Getting templates from source database ', 1);

$sql = "SELECT * FROM cmstemplate WHERE website = 'backend'"; 

$arrReturn = $objDBSync->fetch($sql); 

$arrTemplates = array();
foreach ($arrReturn['data'] as $arrTemplate) {

	$sql = "SELECT * FROM cmstemplate WHERE website = 'backend' AND name = '{$arrTemplate['name']}'";

	$arrReturnDes = $objDB->fetchItem($sql);

	if ($arrReturnDes['ok']) {
		if ($arrReturnDes['num_rows']) {
			//$objShell->log('Row found: ' . $arrTemplate['name'], 1);
			$arrTemplates[$arrTemplate['id']] = (int)$arrReturnDes['data']['id'];
		} else {
			$objShell->log('New template on source: ' . $arrTemplate['name'], 1);
			$arrTemplates[$arrTemplate['id']] = $arrTemplate['name']; 
		}
	} else {
		$objShell->log('Destination template error', 2);
		
	}

}
//$objShell->log(print_r($arrTemplates, 1));

$sql = "SELECT text.* FROM cmstemplatetext text, cmstemplate t ";
$sql.= "WHERE t.id = text.cmstemplateid AND text.website = 'backend' "; 
//$sql.= "LIMIT 2";

$arrReturn = $objDBSync->fetch($sql); 

foreach ($arrReturn['data'] as $arrText) {
	$desTemplateName = '';
	$desTemplateID = 0;

	$desTemplateID = $arrTemplates[$arrText['cmstemplateid']];
	if (!is_numeric($desTemplateID)) {
		$desTemplateName = $desTemplateID;
		$desTemplateID = 0;
	}

	$sql = "SELECT id, name, (value_da = '" . $objDB->escapeString($arrText['value_da']) . "') AS compare_dk, ";
	$sql.= "(value_en = '" . $objDB->escapeString($arrText['value_en']) . "') AS compare_en FROM cmstemplatetext ";
	$sql.= "WHERE website = 'backend' AND cmstemplateid = '{$desTemplateID}' AND name = '{$arrText['name']}'";
	//$objShell->log('Check local: ' . $sql);

	$arrReturnDes = $objDB->fetchItem($sql);

	if ($arrReturnDes['ok']) {
		if ($arrReturnDes['num_rows']) {
			if ($arrReturnDes['data']['compare_dk'] || $arrReturnDes['data']['compare_en']) {
				//$objShell->log('Row found: ' . print_r($arrReturnDes['data'], 1), 1);
			}
			
		} else {
			$objShell->log('Row Not found: ' . $arrText['name'], 1);

			if ($desTemplateName) {
				$sqlInsert = "INSERT INTO cmstemplate SET website = 'backend', name = '" . $objDB->escapeString($desTemplateName) . "'";
				$objShell->log($sqlInsert);
				$rsInsert = $objDB->write($sqlInsert);

				$desTemplateID = $rsInsert['insert_id'];
			}
			
			if ($desTemplateID) {
				$sqlInsert = "INSERT INTO cmstemplatetext SET ";
				$sqlInsert.= "website = 'backend', ";
				$sqlInsert.= "cmstemplateid = '{$desTemplateID}', ";
				$sqlInsert.= "isglobal = '{$arrText['isglobal']}', ";
				$sqlInsert.= "name = '" . $objDB->escapeString($arrText['name']) . "', ";
				$sqlInsert.= "value_da= '" . $objDB->escapeString($arrText['value_da']) . "', ";
				$sqlInsert.= "value_en= '" . $objDB->escapeString($arrText['value_en']) . "' ";
			
				$objDB->write($sqlInsert);
				$objShell->log('SQL Insert: ' . $sqlInsert, 1);
			} else {
				$objShell->log('UPS template dont exist', 1);
			}
		}
	} else {
		$objShell->log('Destination text error' . $arrReturnDes['error'], 2);
	}

}

$objShell->end();

exit();
?>
