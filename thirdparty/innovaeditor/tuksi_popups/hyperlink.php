<?
include(dirname(__FILE__) . '/../../../include/tuksi_init.php');
tuksiIni::setSystemType('backend');

// All users have access
$objDB = tuksiDB::getInstance();

$htmlPageOptions = "";
$arrUploadedFiles = array();

$arrUploadedFiles = getCMSFileUpload();

//load site configuration

$htmlPageOptions = "";

if($arrConf = tuksiConf::getPageConf($_GET->getStr('treeid'))) {
	$htmlPageOptions = getPages($arrConf['rootid']);	
} else {
	if($arrSites = tuksiConf::getAllSitesConf()) {
		foreach ($arrSites as $site) {
			$htmlPageOptions.= "<option disabled='disabled' style='color:#0DB8E2;'>".$site['name']." - ".$site['langtitle']."</option>";
			$htmlPageOptions.= getPages($site['rootid']);
		}
	}
}

$tpl = new tuksiSmarty();

$tpl->assign("htmlpageoption", $htmlPageOptions);
$tpl->assign("arr_uploadedfiles", $arrUploadedFiles);

$tpl->assign("use_files", true);
$tpl->assign("use_pages", true);
$tpl->assign("use_tabs", true); 

$tpl->display("fieldtypes/fieldInnovaeditor/innova_hyperlink.tpl");

/**
 * Laver side struktur
 *
 * @param unknown_type $parentid
 * @param unknown_type $base_text
 * @return unknown
 */
function getPages($parentid, $base_text = "") {
	
	$objDB = tuksiDB::getInstance();

	$sql = "SELECT t.id, t.pg_urlpart_full, t.name ";
	$sql.= "FROM cmstree t ";
	$sql.= "WHERE t.parentid = '{$parentid}' AND isdeleted = 0 ";
	$sql.= "ORDER BY t.seq";
	
	$htmlOptions = "";
	
	if ($base_text)
		$base_text.= "&nbsp;&nbsp;";
	else
		$base_text.= "";
	
	$rsAllTrees  = $objDB->fetch($sql,array('type' => 'object'));
	
	foreach ($rsAllTrees['data'] as $objTreeItem) {
		
		$htmlOptions .= "<option value=\"cmstree:{$objTreeItem->id}\">{$base_text} {$objTreeItem->name}</option>\n";	
				
		$htmlOptions .= getPages($objTreeItem->id, $base_text."&nbsp;&nbsp;");
	}
	return $htmlOptions;
}


/**
 * Henter liste af uploaded filer
 *
 * @return array
 */
function getCMSFileUpload() {
	$objDB = tuksiDB::getInstance();

	$returnArray = array();
	
	$sql = "SELECT * ";
	$sql.= "FROM cmslinkupload ORDER BY filename ASC ";	
	
	$rsAllFiles = $objDB->fetch($sql) ;
	
	foreach ($rsAllFiles['data'] as $arrFileItem) {
		$arrData = array();
		$arrData["id"] = $arrFileItem["id"];
		$arrData["filepath"] = "/downloads/".$arrFileItem["id"]."/".urlencode($arrFileItem["filename"]);
		$arrData["filename"] = $arrFileItem["filename"];
		
		$returnArray[] = $arrData;		
	}
	return $returnArray;
}
?>