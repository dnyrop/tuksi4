<?php
class tuksiUpload {
	
	private $arrAllowed;
	private $id,$uploadedId,$strError;
	private $tablename = "cmsfileupload";
	
	
	private $arrDenylist = array(	"php",
																"php3",
																"php4",
																"php5",
																"phtml",
																"phps",
																"cgi",
																"pl");
	
	function __construct($id){
		$this->id = $id;
	}
	
	function upload(){

		
		if(is_array($_FILES) && count($_FILES) > 0) {
		
			foreach ($_FILES as $arrFile) {
				if(is_uploaded_file($arrFile['tmp_name'])) {
					$this->cleanupCurrent();
					//get extension
					$ext = substr($arrFile['name'], strpos($arrFile['name'], '.')+1); 
					if($this->checkExtension($ext)) {
						$objDB = tuksiDB::getInstance();
						$arrValues = array(	'original_filename' => $arrFile['name'],
																'extension' => $ext,
																'size' => $arrFile['size'],
																'uploadid' => $this->id);
																
						$arrRawValues = array('dateadded' => 'now()');
																
						$arrRs = $objDB->insert($this->tablename,$arrValues,$arrRawValues);
						
						$this->uploadedId = $arrRs['insert_id'];
						
						$newFilename = $this->uploadedId . "_filepath_" . rand(1000,9999) . "." . $ext;
						
						$arrConf = tuksiConf::getConf();
						$uploadPath = $arrConf['path']['supload'] . "/" . $this->tablename."/";
						
						if(!is_dir($uploadPath)) {
							mkdir($uploadPath,0777);
						}
						
						if(move_uploaded_file($arrFile['tmp_name'],$uploadPath . $newFilename)) {
							$objDB->update($this->tablename,array('filepath' => $this->tablename."/".$newFilename),array(),"id = '".$this->uploadedId."'");
							return true;
						} else {
							$sqlDel = "DELETE FROM " . $this->tablename . " WHERE id = " . $this->uploadedId ;
							$objDB->write($sqlDel);
							$this->strError = "Could not copy file to server";
							return false;
						}
					} else {
						$this->strError = "Illigal extension : " . $ext;
						return false;
					}
				}
			}
		} else {
			$this->strError = "No files to upload";
			return false;
		}
	}
	function cleanup(){
		
		$objDB = tuksiDB::getInstance();
		$objPage = tuksiBackend::getInstance();
		
		$strOneHourAgo = date("Y-m-d H:i:s",time() - 3600);
		$sqlFiles = "SELECT * FROM ".$this->tablename. " WHERE dateadded < '$strOneHourAgo'";
		$arrRsFiles = $objDB->fetch($sqlFiles);
		if($arrRsFiles['ok'] && $arrRsFiles['num_rows'] > 0) {
			foreach ($arrRsFiles['data'] as &$arrFile) {
				$fullfilepath = dirname(__FILE__) ."/../../uploads/". $arrFile['filepath'];
				if(file_exists($fullfilepath) && is_file($fullfilepath)) {
					unlink($fullfilepath);
				}
				$arrRs = $objDB->delete($this->tablename,array('id' => $arrFile['id']));
			}
		}
	}
	function cleanupCurrent(){
		
		$objDB = tuksiDB::getInstance();
		
		$sqlFiles = "SELECT * FROM ".$this->tablename. " WHERE uploadid = '{$this->id}' ";
		$arrRsFiles = $objDB->fetch($sqlFiles);
		if($arrRsFiles['ok'] && $arrRsFiles['num_rows'] > 0) {
			foreach ($arrRsFiles['data'] as &$arrFile) {
				$fullfilepath = dirname(__FILE__) ."/../../uploads/". $arrFile['filepath'];
				if(file_exists($fullfilepath) && is_file($fullfilepath)) {
					unlink($fullfilepath);
				}
				$arrRs = $objDB->delete($this->tablename,array('id' => $arrFile['id']));
			}
		}
	}
	function getFileFromId(){
		$objDB = tuksiDB::getInstance();
		$arrRow = $objDB->fetchRow($this->tablename,array('uploadid' => $this->id));
		return $arrRow;
	}
	function getError(){
		return $this->strError;
	}
	function getUploadInfo(){
		return $this->uploadedId;
	}
	function setAllowedTypes($arrAllowed){
		$this->arrAllowed = $arrAllowed;
	}
	function checkExtension($ext) {
		if(in_array($ext,$this->arrDenylist)) {
			return false;
		} elseif (count($this->arrAllowed) > 0) {
			if(in_array($ext,$this->arrAllowed)) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}
}
?>