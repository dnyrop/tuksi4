<?php
/**
 * Download script for files uploaded through the htmleditor
 * Keeps original filename
 */
include_once(dirname(__FILE__) . "/../include/tuksi_init.php");

$objDB = tuksiDB::getInstance();

$arrConf = tuksiConf::getConf();

$FileID = $_GET->getInt('fileid');

$filepath = "";
$filename = "";

if ($FileID != "") {
	if(($arrRow = $objDB->fetchRow("cmslinkupload" . $arrConf['setup']['tableext'] ,$FileID)) !== false) {
		/* We got the file - get the info to use! */
		$filepath = $arrRow["file"];
		$filename = $arrRow["filename"];			
	}
} elseif ($_GET->getStr('file_src') && $_GET->getStr('filename')) {
	$filepath = $_GET->getStr('file_src');
	$filename = urldecode($_GET->getStr('filename'));
	$filename = tuksiTools::fixname($filename) . '.' . tuksiFile::getExt($filepath);
}

if ($filepath != "" && $filename != "") {
	
	$real_filepath = $arrConf['path']['supload'] . "/{$filepath}";
	$temp_path = dirname(__FILE__) . "/../download/{$FileID}";
	
	$url_path = "/download/{$FileID}";
	
	if (file_exists($real_filepath)) {
		
				 // print $temp_path;
		if (!file_exists($temp_path)) {			
			mkdir($temp_path);
		}
		
		$bFileExists = false;
		$link_path = str_replace('//', '/', $temp_path . '/' . $filename);
		if (is_link($link_path)) {
			if (($bFileExists = file_exists($link_path)) === false || $real_filepath != readlink($link_path)) {
				unlink($link_path);		
				$bFileExists = false;
			}
		}		
		
		/* Make a softlink to the file... */
		if (!$bFileExists && symlink($real_filepath, $link_path)) {
			/* Yes - symlink created! */
			$bFileExists = true;
		}
		
		if ($bFileExists) {
			/* Yes - symlink created! */
			$url = $url_path . "/" . rawurlencode($filename);
			$url = str_replace('//', '/', $url);
			header("location: $url");
			exit();
		}		
	} else {
		die("file not found");	
	}
}
?>
