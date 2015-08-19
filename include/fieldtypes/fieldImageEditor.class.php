<?php

/**
 * Enter description here...
 *
 * @package tuksiFieldType
 */

class fieldImageEditor extends field{

	public $primaryIDName = 'id';
	
	function __construct($objField) {
		parent::field($objField);
	}

	function getHTML() {
		
		$objPage = tuksiBackend::getInstance();
		$arrConf = tuksiConf::getConf();
		
		$tpl = new tuksiSmarty();
		
		$popupUrl = "/include/fieldtypes/fieldImageEditor/imageeditorpopup.php?rowid=".$this->objField->rowid."&itemid=". $this->objField->fielditemid ."&pictureid=fielditem_" . $this->objField->fielditemid . "_" . $this->objField->rowid."&PHPSESSID=" . session_id();
		
		$onclick = "tuksi.util.setPopup('$popupUrl',500,800);return false;";
		
		$btnUpload = tuksiFormElements::getButton(array(	'color' => 'black',
																								'value' => $objPage->cmstext("new_picture"),
																								'customaction' => $onclick,
																								'icon' => 'uploadImage'));
		$tpl->assign('btnupload',$btnUpload);
		
		
		if(($arrFiles = $this->getImages()) !== false) {
			
			if($this->objField->fieldvalue5) {
				$altinput = $objPage->cmstext('alttext') . " : ";
				
				$altinput.= tuksiFormElements::getInput(array(	'id' => $this->objField->htmltagname . "_ALTTEXT",
																												'value' => $this->getAlt()));
			}
			$tpl->assign("altinput",$altinput);
			
			$strHtml.= tuksiFormElements::getInput(array(	'type' => hidden,
																											'id' =>  $this->objField->htmltagname . "_DELETE"));
			
			$onclick = 'if (confirm(\'' . $objPage->cmstext("delete_picture") . '?\')) {document.forms[0].' . $this->objField->htmltagname . '_DELETE.value=1;saveData();} return false;';
			
			$strHtml.= tuksiFormElements::getButton(array(	'value' => $objPage->cmstext("delete_picture"),
																												'customaction' =>  $onclick,
																												'icon' => 'delete'));
			$tpl->assign('btndelete',$strHtml);
			
			if(count($arrFiles) > 0) {
				$arrImages = array();
				foreach ($arrFiles as $file) {
					$arrImages[] = array('src' => $arrConf['path']['upload'] . "/" . $file);
				}
				$tpl->assign('img',$arrImages);
			}
			
			
			$strHtmlValue = tuksiFormElements::getInput(array(	'type' => hidden,
																													'id' =>  $this->objField->htmltagname,
																													'value' => $this->objField->value));
			
			$tpl->assign("hiddenvalue",$strHtmlValue);
			$startHtml = parent::getHtmlStart();
			$tpl->assign("starthtml", $startHtml);
			
		}
		return parent::returnHtml($this->objField->name,$tpl->fetch('fieldtypes/fieldImageEditor.tpl'));
	}

	function saveData() {
		$objDB = tuksiDB::getInstance();
		
		$alttext = "";
		
		if ($_POST->getStr($this->objField->htmltagname . '_ALTTEXT')) {
			$alttext = "|" . $_POST->getStr($this->objField->htmltagname . '_ALTTEXT');
			$alttext = $objDB->realEscapeString($alttext);
		}
		
		include_once('fieldImageEditor/imageEditor.class.php');
		
		$pictureid = "fielditem_" . $this->objField->fielditemid . "_" . $this->objField->rowid;
		$editor = new imageEditor($pictureid, $this->objField->fielditemid, $this->objField->rowid);
		
		if ($_POST->getStr($this->objField->htmltagname . '_DELETE')) {
			$editor->deleteField($this->objField);
			$sql = $this->objField->colname . " = ''";
			$this->objField->value = '';
		} else if ($value = $editor->saveField($this->objField)) {
			$sql = $this->objField->colname . " = '" . $value . $alttext . "'";
			$this->objField->value = $value . $alttext;
		} else if ($this->objField->value) {
			$arrImages = $this->getImages();
			$sql =  $this->objField->colname . " = '" . join(";", $arrImages) . $alttext ."'";
		}
		return $sql;
	}

	function getListHtml() {
		
		$arrConf = tuksiConf::getConf();
		
		$html = "";
		
		if ($this->objField->value) {
			$arrImages = explode(';', $this->objField->value);
			if (count($arrImages)) {
				$lastImg = $arrConf['path']['upload'] . "/" . array_pop($arrImages);
				$html = "<img src=\"{$lastImg}\" alt=\"\" />";
			}
		}
		
		return $html;
	}
	
	/**
	 * temporary function that returns an array of the uploaded images
	 *
	 * @return string path to image
	 */
	
	function getFrontendValue() {
		
		$value = "";
		
		if($this->objField->value) {
			
			if(substr_count($this->objField->value,"|") > 0) {
				$arrInfo = explode("|", $this->objField->value);	
				$arrFiles = explode(";", $arrInfo[0]);
				$altText = $arrInfo[1];
			} else {
				$arrFiles = explode(";", $this->objField->value);
			}
			
			if(count($arrFiles) > 1) {
				$value = $arrFiles;
			} else {
				$value = $arrFiles[0];
			}
		}
		
		return $value;
	}
	
	
	function getImages(){
		
		$arrConf = tuksiConf::getConf();
		
		//split alt text
		if(substr_count($this->objField->value,"|") > 0) {
			$arrInfo = explode("|", $this->objField->value);	
			$arrFiles = explode(";", $arrInfo[0]);
		} else {
			$arrFiles = explode(";", $this->objField->value);
		}
		
		//get first file
		$file = $arrConf['path']['supload'] . "/" .$arrFiles[0];
		
		if(file_exists($file) && is_file($file)) {
			return $arrFiles;
		} else {
			return false;
		}
	}
	
	function copyData($rowid_to) {
		$objDB = tuksiDB::getInstance();
		$arrConf =tuksiConf::getConf();

		if ($this->objField->value) {
			$filename_from_fullpath = $arrConf['path']['supload'] . '/' . $this->objField->value;

			if (file_exists($filename_from_fullpath)) {
				$ext = tuksiFile::getExt( $this->objField->value );

				$filename_new = $this->objField->tablename . '/' . $rowid_to . '_' . $this->objField->colname . '_' . round(rand(1,10000)) . '.' . $ext;

				$filename_new_fullpath = $arrConf['path']['supload'] . '/' . $filename_new;

				copy($filename_from_fullpath, $filename_new_fullpath);

				$sqlUpdateField = "UPDATE {$this->objField->tablename} SET {$this->objField->colname} = '$filename_new' ";
				$sqlUpdateField.= " WHERE {$this->primaryIDName}  = '{$rowid_to}'";

				$arrReturn = $objDB->write($sqlUpdateField);
			} else {
				error_log('fieldFileUpload::copyData: File from doesnt exists: ' . $filename_from_fullpath);
			}

		} 
	}
	
	function getAlt(){
		if(substr_count($this->objField->value,"|") > 0) {
			$arrInfo = explode("|", $this->objField->value);	
			return $arrInfo[1];
		} 
		return "";
	}
}
?>
