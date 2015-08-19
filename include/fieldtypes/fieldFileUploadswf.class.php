<?php
/**
 * Checkbox input field
 *
 * Checkbox input field
 * Fieldvalues:
 * Fieldvalues:
 * None
 *
 * @package tuksiFieldType
 */
class fieldFileUploadswf extends field{

	function __construct($objField) {
		parent::field($objField);
		
		$this->objField = $objField;
	}

	function getHTML() {
		
		$objPage = tuksiBackend::getInstance();

		$objPage->addJavascript("/javascript/backend/libs/tuksi.swfupload.js");
		$objPage->addJavascript("/thirdparty/swfupload/swfupload.js");
		
		$tpl = new tuksiSmarty();
		$tpl->assign("id",$this->objField->htmltagname);
		
		//make url
		if($_SERVER['HTTPS']) {
			$url = "https://";	
		} else {
			$url = "http://";
		}
		$url.= $_SERVER['SERVER_NAME'] . "/services/upload.php";
		
		$tpl->assign("uploadpath",$url);
		
		if($this->objField->value) {
			$tpl->assign("filename",$this->objField->value);
		}
		
		return parent::returnHtml($this->objField->name,$tpl->fetch("fieldtypes/fieldFileUploadSWF.tpl"));
	}

	function saveData() {
		
		$sql = "";
		
		if($_POST->getInt('fileUploaded'.$this->objField->htmltagname)) {
			
			$uploadedId = $_POST->getInt('fileUploaded'.$this->objField->htmltagname);
			$uploader = new tuksiUpload($this->objField->htmltagname);
			$uploader->cleanup();
			if(($arrFile = $uploader->getFileFromId()) !== false) {
				$arrConf = tuksiConf::getConf();
				
				$fullfilepath = $arrConf['path']['supload'] . "/" .$arrFile['filepath'];
				if(file_exists($fullfilepath) && is_file($fullfilepath)) {
					$newFilename = $this->objField->tablename . "/" . $this->objField->rowid . "_" . $this->objField->colname . "_" . rand(1000,9999) . "." . $arrFile['extension'];
					$newFilePath = $arrConf['path']['supload'] . "/" . $newFilename;
					copy($fullfilepath,$newFilePath);
					unlink($fullfilepath);
					$sql = $this->objField->colname . " = '$newFilename'";
				}
			}
		}
		return $sql;
	}

	function getListHtml() {
		return $html;
	}
	
	function deleteData(){
	}
}
?>
