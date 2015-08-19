<?php
include_once(dirname(__FILE__) . '/../../tuksi_init.php');

class imageEditor {
	
	public $pictureid, $arrItem,$objPage,$uploadImageInfo,$action,$delta;
	private $tpl,$tplSrc,$tplMain,$arrImgRotate;
	private $ratio = array();
	private $conf  = array();
	private $arrAllowedMime = array();
	private $arrError = array();
	private $orig = array();
	private $newImage = false;
	private $multi = false;
	private $isSaveable = false;
	private $isCropped = false;
	private $quality = 90;
	private $arrPreviewImg = array();
	private $cropperDim = array('height' => 400,
										 					'width' => 500);

	public $arrExtentions = array("image/jpe" => "jpg",
											"image/pjpe"=> "jpg",
											"image/jpeg"=> "jpg",
											"image/gif"	=> "gif",
											"image/png" => "png",
											"image/x-png" => "png");
	
	function __construct($pictureid,$itemid = 0,$rowid = 0) {
		
		$this->pictureid = $pictureid;
		$this->itemid = $itemid;
		
		$this->action = $_POST->getStr('editorAction');
		
		$this->rowid = $rowid;
		if(!$this->rowid)
			$this->rowid = $_GET->getInt('rowid');
		
		if(!$this->action)
			$this->action = $_GET->getStr('editorAction');
		
		if($_FILES && (is_uploaded_file($_FILES['userfile']['tmp_name']) || is_uploaded_file($_FILES['userfile']['tmp_name'][0]))) {
			$this->newImage = true;
		}
	}
	
	function getHtml() {

		$this->tpl = new tuksiSmarty();
		$this->tplMain = new tuksiSmarty();
		$this->loadItem($this->itemid);
		
		if($this->action == "save") {
			$this->loadConf();
			$this->save();
			$this->beforeClose();
			return $this->tplMain->fetch("fieldtypes/fieldimageeditor/fieldimageeditor_close.tpl");
		}elseif($this->action == 'rotate') {
			$this->loadConf();
			$this->doRotate();
			$this->conf['cropper'] = true;
			$this->btn['crop'] = true;
			$this->btn['rotate'] = true;
			$this->btn['save'] = true;
			$this->btn['adjust'] = true;
			$this->btn['new'] = true;
			$this->tplSrc = "fieldtypes/fieldimageeditor/fieldimageeditor_edit.tpl";	
		}elseif($this->action == 'savecrop'){
			$this->loadConf();
			$this->cropImage();
			$this->save();
			$this->beforeClose();
			return $this->tplMain->fetch("fieldtypes/fieldimageeditor/fieldimageeditor_close.tpl");
		}elseif($this->action == 'resetcrop'){
			$this->reset();
			$this->beforeClose();
			return $this->tplMain->fetch("fieldtypes/fieldimageeditor/fieldimageeditor_close.tpl");
		}elseif($this->action == 'recrop') {
			$this->loadConf();
			$this->reCrop();
			$this->getRotateImages();
			$this->conf['cropper'] = true;
			$this->btn['save'] = true;
			$this->btn['crop'] = true;
			$this->btn['rotate'] = true;
			$this->btn['adjust'] = true;
			$this->btn['new'] = true;
			$this->tplSrc = "fieldtypes/fieldimageeditor/fieldimageeditor_edit.tpl";	
		} elseif($this->action == 'crop') {
			$this->loadConf();
			$this->cropImage();
			$this->btn['save'] = true;
			$this->btn['recrop'] = true;
			$this->btn['new'] = true;
			$this->tplSrc = "fieldtypes/fieldimageeditor/fieldimageeditor_preview.tpl";
			$this->tpl->assign("error",$this->arrError);
		//an image is uploaded
		}else{
			$this->reset();
			if($this->newImage) {
				$this->loadConf();
				if($this->uploadImage()) {
					$this->conf['cropper'] = true;
					$this->btn['save'] = true;
					$this->btn['crop'] = isset($this->isAnimation) ? !$this->isAnimation : true;
					$this->btn['rotate'] = isset($this->isAnimation) ? !$this->isAnimation : true;
					$this->btn['adjust'] = true;
					$this->btn['new'] = true;
					$this->tplSrc = "fieldtypes/fieldimageeditor/fieldimageeditor_edit.tpl";	
				} else {
					$this->tplSrc = "fieldtypes/fieldimageeditor/fieldimageeditor_upload.tpl";
					$arrConf = array(	'rowid' => $this->rowid,
											'itemid' => $this->itemid,
											'sessionid' => session_id(),
											'baseurl' => 'http://'.$_SERVER['HTTP_HOST']);
					$this->tpl->assign("conf",$arrConf);
					$this->tpl->assign('imgConf',$this->conf);
					$arrTypes = explode(",",$this->arrItem['fieldvalue1']);
					$types = "<b>".join("</b>,<b>",$arrTypes)."</b>";
					$this->tpl->assign('imgType',$types);
					$this->tpl->assign("error",$this->arrError);
				}
			//no image present
			} else {
				$this->tplSrc = "fieldtypes/fieldimageeditor/fieldimageeditor_upload.tpl";
				$this->loadConf();
				$this->tpl->assign('imgConf',$this->conf);
				$arrTypes = explode(",",$this->arrItem['fieldvalue1']);
				$types = "<b>".join("</b>,<b>",$arrTypes)."</b>";
				$this->tpl->assign('imgType',$types);
				$arrConf = array(	'rowid' => $this->rowid,
										'itemid' => $this->itemid,
										'sessionid' => session_id(),
										'baseurl' => 'http://'.$_SERVER['HTTP_HOST']);
										
				
				$this->tpl->assign("conf",$arrConf);
			}
		}
		$this->checkSaveable();
			
		$this->setVars();
		$html = $this->tpl->fetch($this->tplSrc);
		
		$this->tplMain->assign('content',$html);
		
		return $this->tplMain->fetch('fieldtypes/fieldimageeditor/fieldimageeditor.tpl');
	}
	
	
	function loadItem($itemid) {

		if($itemid > 0) {
		
			$objDB = tuksiDB::getInstance();
			
			$sqlItem = "SELECT * FROM cmsfielditem WHERE id = '" .  $itemid."'";
			$rsItem = $objDB->fetch($sqlItem);
	
			if($rsItem['num_rows']) {
				$this->arrItem = $rsItem['data'][0];
			} else {
				//die('Kunne ikke finde id\'et');
			}
		} else {
			
			$this->arrItem['fieldvalue1'] = $_GET->getStr('fieldvalue1'); 
			$this->arrItem['fieldvalue2'] = $_GET->getStr('fieldvalue2'); 
			$this->arrItem['fieldvalue3'] = $_GET->getStr('fieldvalue3'); 
			$this->arrItem['fieldvalue4'] = $_GET->getStr('fieldvalue4'); 
			$this->arrItem['fieldvalue5'] = $_GET->getStr('fieldvalue5'); 
			$this->arrItem['hiddenfieldname'] = $_GET->getStr('hiddenfieldname'); 
			$this->arrItem['formname'] = $_GET->getStr('formname'); 
			$this->arrItem['callback'] = $_GET->getStr('callback'); 
			
			//load from querystring
		}
		
	}
	
	function loadConf() {
		
		$arrType = explode(",",$this->arrItem['fieldvalue1']);
		
		foreach ($this->arrExtentions as $mime => $ext) {
			if(in_array($ext,$arrType)) {
				$this->arrAllowedMime[] = $mime;
			}
		}
		
		if(strpos($this->arrItem['fieldvalue3'],";") !== false) {
			$arrTmp = explode(";",$this->arrItem['fieldvalue3']);
			$arrSize = explode('x',$arrTmp[0]);
		} else {
			$arrSize = explode('x',$this->arrItem['fieldvalue3']);
		}
		if(is_numeric($this->arrItem['fieldvalue4']) && 0 < $this->arrItem['fieldvalue4']) {
			$this->quality = $this->arrItem['fieldvalue4'];
		}
		

		// har ændret til at den tjekker på arrTmp i stedet for arrsize og Byttet om så > 1 giver multi = true - CHA
		if(count($arrTmp) > 1) {
			$this->multi = true;
		} else {
			$this->multi = false;
		}
		
		if($this->arrItem['fieldvalue2']) {
			
			//finder ratio, ? bliver til 0
			$this->conf['ratio']['x'] = is_numeric($arrSize[0]) ? $arrSize[0] : 0;
			$this->conf['ratio']['y'] = is_numeric($arrSize[1]) ? $arrSize[1] : 0;
			
			//tjekker for min. bredde
			if(is_numeric($arrSize[0])) {
				$this->conf['minwidth'] = $arrSize[0];
			}
			//tjekker for min. højde
			if(is_numeric($arrSize[1])) {
				$this->conf['minheight'] = $arrSize[1];	
			}	
		
		} else {
			
			//tjekker for min. bredde
			if(is_numeric($arrSize[0])) {
				$this->conf['minwidth'] = $arrSize[0];
			}	
			//tjekker for min. højde
			if(is_numeric($arrSize[1])) {
				$this->conf['minheight'] = $arrSize[1];	
			}	
		}
		
		$this->orig = $this->getOrgImageinfo();
		
		$this->checkRotate();
	}
	
	function cropImage() {
		
		$arrCrop = array();
		$arrConf = tuksiConf::getConf();
		
		$arrCrop['delta'] = $_GET->getStr('delta');
		$arrCrop['width'] = $_GET->getStr('width');
		$arrCrop['height'] = $_GET->getStr('height');
		$arrCrop['x1'] = $_GET->getStr('x1');
		$arrCrop['y1'] = $_GET->getStr('y1');
		
		$objDB = tuksiDB::getInstance();
		
		//try load original image
		$sqlOrg = "SELECT id,imagepath FROM cmsimageupload WHERE imageid = '{$this->pictureid}' AND imagetype = '1'";
		$rsOrg = $objDB->fetchItem($sqlOrg);
		if($rsOrg['num_rows'] > 0 ) {
			$arrData = $rsOrg['data'];
			if(file_exists($arrConf['path']['supload'] . "/cmsimageupload/" . $arrData['imagepath']) && is_file($arrConf['path']['supload'] . "/cmsimageupload/" . $arrData['imagepath'])) {
				
				$orgSize = getimagesize($arrConf['path']['supload'] . "/cmsimageupload/" . $arrData['imagepath']);
				
				if($arrCrop['delta'] == 1) {
					
					$orgImage['width'] = $arrCrop['width'];
					$orgImage['height'] = $arrCrop['height'];
					$orgImage['x'] = $arrCrop['x1'];
					$orgImage['y'] = $arrCrop['y1'];
					$orgImage['src'] = $arrConf['path']['supload'] . "/cmsimageupload/" . $arrData['imagepath'];
					
					$this->uploadImageInfo = $this->performCrop($orgImage,3);
					
				} else {
					
					$this->reCrop();
					
					if($arrCrop['width'] == $this->uploadImageInfo['width']) {
						$orgImage['width'] = $orgSize[0];
					} else {
						$orgImage['width'] = round($arrCrop['width'] / $arrCrop['delta']);	
					}
					if($arrCrop['height'] == $this->uploadImageInfo['height']) {
						$orgImage['height'] = $orgSize[1];
					} else {
						$orgImage['height'] = round($arrCrop['height'] / $arrCrop['delta']);	
					}	
					
					$orgImage['x'] = round($arrCrop['x1'] / $arrCrop['delta']);
					$orgImage['y'] = round($arrCrop['y1'] / $arrCrop['delta']);
					$orgImage['src'] = $arrConf['path']['supload'] . "/cmsimageupload/" . $arrData['imagepath'];
					
					$this->uploadImageInfo = $this->performCrop($orgImage,3);
				}
				//check if the cropped image can be in the cropper
				$cropDelta = $this->getDelta($this->uploadImageInfo['width'],$this->uploadImageInfo['height'],$this->cropperDim['width'],$this->cropperDim['height']);
				if($cropDelta != 1){
					$arrImg = $this->uploadImageInfo;
					$arrImg['x'] = 0;
					$arrImg['y'] = 0;
					$arrImg['src'] = $arrConf['path']['supload'] .$arrImg['src'];
					$this->arrPreviewImg = $this->performCrop($arrImg,20,array(	'minwidth' => $this->cropperDim['width'],
																																'minheight' => $this->cropperDim['height']));
				}
				$this->isCropped = true;
				
			} else {
				$this->arrError[] = "Original filen kunne ikke findes";
			}
		} else {
			$this->arrError[] = "Original filen kunne ikke findes";
		}
	}
	
	function reset($specific = NULL) {
		
		$objDB = tuksiDB::getInstance();
		$arrConf = tuksiConf::getConf();
		
		//clean up db
		$sqlClean = "SELECT * FROM cmsimageupload WHERE imageid = '{$this->pictureid}' ";
		$rsClean = $objDB->fetch($sqlClean);
		if($rsClean['num_rows'] > 0 ) {
			foreach($rsClean['data'] as $arrData) {
				if(is_array($specific)) {
					if(in_array($arrData['imagetype'],$specific)) {
						if(file_exists($arrConf['path']['supload'] . "/cmsimageupload/" . $arrData['imagepath']) && is_file($arrConf['path']['supload'] . "/cmsimageupload/" . $arrData['imagepath'])) {
							unlink($arrConf['path']['supload'] . "/cmsimageupload/" . $arrData['imagepath']);
						}
						$sqlDel = "DELETE FROM cmsimageupload WHERE id = '{$arrData['id']}' ";
						$objDB->write($sqlDel);
					}
				} else {
					if(file_exists($arrConf['path']['supload'] . "/cmsimageupload/" . $arrData['imagepath']) && is_file($arrConf['path']['supload'] . "/cmsimageupload/" . $arrData['imagepath'])) {
						unlink($arrConf['path']['supload'] . "/cmsimageupload/" . $arrData['imagepath']);
					}
				}
			}
			if(!$specific) {
				$sqlDel = "DELETE FROM cmsimageupload WHERE imageid = '{$this->pictureid}'";
				$objDB->write($sqlDel);
			}
		}
		
		return false;
	}
	function beforeClose(){
		$this->tplMain->assign('itemInfo',$this->arrItem);
		if($this->arrItem['hiddenfieldname']) {
			$filename = $this->getSavedFilename();	
			$this->tplMain->assign('filename',$filename);
		}
		
	}
	function setVars () {
		
		$this->uploadImageInfo['src'].= "?" . time();
		
		$this->tpl->assign('imgInfo',$this->uploadImageInfo);
		
		$this->tpl->assign('previewBtnWidth',$this->uploadImageInfo['width']-50);
		
		$this->tpl->assign('pictureid',$this->pictureid);
		$this->tpl->assign('itemid',$this->itemid);
		$this->tpl->assign('rowid',$this->rowid);
		
		$this->tpl->assign('saveable',$this->isSaveable);
		
		$this->tpl->assign('doFullRotate',$this->doFullRotate ? 1 : 0);
		
		$this->tpl->assign('editorAction','crop');
		
		$delta = $_POST->getStr('delta');
		if(!$delta)
			$delta = $_GET->getStr('delta');
		
		if(!$this->delta) {
			if($delta && isset($this->orig['width'])) {
				$this->delta = $this->getDelta($this->orig['width'],$this->orig['height'],$this->cropperDim['width'],$this->cropperDim['height']);	
			} else {
				$this->delta = $delta;
			}
		}
		
		$this->tpl->assign('itemInfo',$this->arrItem);
		
		$this->tpl->assign('cropper',true);
		$this->tpl->assign('displayOnInit',true);
		
		$this->tpl->assign('orig',$this->orig);
		
		if($this->conf['ratio']) {
			$this->tpl->assign('ratio',$this->conf['ratio']);
			$info['ratio'] = 'låst';
		} else {
			$info['ratio'] = 'frit';
		}
		$this->getInitCropperSize();
		
		if($this->conf['minwidth']) {
			$this->tpl->assign('minwidth',$this->conf['minwidth']*$this->delta);
			$info['minwidth'] = $this->conf['minwidth'];
		}
		if($this->conf['minheight']) {
			$this->tpl->assign('minheight',$this->conf['minheight']*$this->delta);
			$info['minheight'] = $this->conf['minheight'];
		}
		$this->tpl->assign('skabelon',$info);
			

		$this->tpl->assign('delta',$this->delta);
		
		if($this->arrImgRotate) {
			$this->tpl->assign('rotate',$this->arrImgRotate);
		}
		
		$this->tplMain->assign("btn",$this->btn);
		
		if(count($this->arrPreviewImg) > 0) {
			$this->tpl->assign("previewImg",$this->arrPreviewImg);
		}
		
		
	}
	
	function getInitCropperSize(){
		
		if($this->conf['minwidth'] > 0 && $this->conf['minheight'] > 0) {
			
			$dW = $this->uploadImageInfo['width'] / $this->conf['minwidth'];
			$dH = $this->uploadImageInfo['height'] / $this->conf['minheight'];
			
			if($dH < $dW) {
				$arrSize = array(	'width' => $this->conf['minwidth'] * $dH,	
										'height' => $this->conf['minheight'] * $dH);
			} else if($dH > $dW){
				$arrSize = array(	'width' => $this->conf['minwidth'] * $dW,	
										'height' => $this->conf['minheight'] * $dW);
			} else {
				$arrSize = array(	'width' => $this->uploadImageInfo['width'] / 2,	
										'height' => $this->uploadImageInfo['height'] / 2);
			}
		} else {
			$arrSize = array(	'width' => $this->uploadImageInfo['width'] / 2,
									'height' => $this->uploadImageInfo['height'] / 2);
		}
		
		$this->tpl->assign('initDim',$arrSize);
		//print_r($arrSize);
	}
	
	function getOrgImageinfo(){
		
		if(empty($this->orig)) {
			
			$objDB = tuksiDB::getInstance();
			
			//image already uploaded and cropped for editor
			$sqlCrop = "SELECT orig_info FROM cmsimageupload ";
			$sqlCrop.= "WHERE imageid = '{$this->pictureid}' AND imagetype = 1";
			$rsCrop = $objDB->fetch($sqlCrop) or print mysql_error() . $sqlCrop;
			if($rsCrop['num_rows'] > 0 ) {
				$arrImg = $rsCrop['data'][0];
				$arrInfo = unserialize($arrImg['orig_info']);
				return $arrInfo;
			} else {
				return false;
			}
		} else {
			return $this->orig;
		}
	}
	
	function uploadImage() {
		
		$arrConf = tuksiConf::getConf();
		
		//insert multi upload here
		if(is_array($_FILES['userfile']['tmp_name'])) {
			$_FILES['userfile']['tmp_name'] = $_FILES['userfile']['tmp_name'][0];
			$_FILES['userfile']['name'] = $_FILES['userfile']['name'][0];
			$_FILES['userfile']['size'] = $_FILES['userfile']['size'][0];
		}
		
		if($_FILES && is_uploaded_file($_FILES['userfile']['tmp_name'])) {
			
			$arrSizeInfo = getimagesize($_FILES['userfile']['tmp_name']);
			
			$arrInfo = array('width' => $arrSizeInfo[0],'height' => $arrSizeInfo[1],'mime' => $arrSizeInfo['mime'], 'src' => $_FILES['userfile']['tmp_name'],'filesize' => $_FILES['userfile']['size']);
			
			//tjekker om billedet kan bruges med de indstillinger der er givet for det
			if($this->validateImage($arrInfo)) {
			
				$this->checkUploadFolder('cmsimageupload');
				
				$objDB = tuksiDB::getInstance();
					
				$this->orig = $arrInfo;
				$this->orig['size'] = round($this->orig['filesize'] / 1024);
				$this->orig['name'] = $_FILES['userfile']['name'];
				
				//upload original billedet
				$sqlNewRow = "INSERT INTO cmsimageupload (imageid,dateadded,imagetype,orig_info) VALUES ";
				$sqlNewRow.= "('{$this->pictureid}',now(),'1','". serialize($this->orig)."')";
				$rsNew = $objDB->write($sqlNewRow);
				
				$newRowId = $rsNew['insert_id'];
				
				$filename = $newRowId . "_content_" . rand(1000,9999) . "." . $this->arrExtentions[$arrInfo['mime']];
				$filepath = $arrConf['path']['supload'] . "/cmsimageupload/";
				
				if (move_uploaded_file($_FILES['userfile']['tmp_name'],$filepath . $filename)) {
					$sqlUpdate = "UPDATE cmsimageupload SET imagepath = '$filename'  WHERE id = $newRowId";
					$objDB->write($sqlUpdate);
					
					if (!$this->resizeToCropper(array('src' => $filepath . $filename, 'name' => $filename))) {
						$this->arrError[] = "Filen kunne ikke oploades (formentlig ikke 72dpi eller cmyk farver). Prøv igen eller kontakt dwarf.";
						return false;
					}
					$this->checkRotate();
					$this->makeRotateImages($filepath . $filename); 
				} else {
					$this->arrError[] = "Filen kunne ikke oploades(fejl 14). Prøv igen eller kontakt dwarf.";
					return false;
				}
				return true;
			} else {
				return false;
			}
		}
	}
	
	function validateImage($arrInfo) {
		
		$arrError = array();
		
		$cmd = "identify " . $arrInfo['src']. " -format \"%r\"";
		$str = shell_exec($cmd);
		if(substr_count($str,"DirectClassCMYK") > 0) {
			$this->arrError[] = array('error' => 'picture_is_cmyk'); 
		}
		
		if(!in_array($arrInfo['mime'],$this->arrAllowedMime)) {
			$this->arrError[] = array('error' => 'picture_wrong_type', 'value1' => $this->arrItem['fieldvalue1']); 
		}
		
		if ($arrInfo['mime'] == 'image/gif' && $this->isGifAnimation($arrInfo)) {
			if ($arrInfo['width'] != $this->conf['minwidth'] || $arrInfo['height'] != $this->conf['minheight']) {
				$this->arrError[] = array('error' => 'animation_invalid', 'value1' => $this->conf['minwidth'] . 'x' . $this->conf['minheight']);
			}
		}
		
		if(!count($this->arrError) && $this->conf['minwidth']) {
			if($arrInfo['width'] < $this->conf['minwidth']) {
				$this->arrError[] = array('error' => 'picture_width', 'value1' => $this->conf['minwidth'], 'value2' => $arrInfo['width']); 
			}	
		}
		
		if(!count($this->arrError) && $this->conf['minheight']) {
			if($arrInfo['height'] < $this->conf['minheight']) {
				$this->arrError[] = array('error' => 'picture_height', 'value1' => $this->conf['minheight'], 'value2' => $arrInfo['height']); 
			}	
		}
		if(count($this->arrError) > 0) {
			return false;
		} else {
			return true;
		}
		
	}

	function isGifAnimation($arrInfo) {
		$success = false;
		
		if ($arrInfo['mime'] == 'image/gif') {
			if ($fh = fopen($arrInfo['src'], 'rb')) {
				$count = 0;
				// an animated gif contains multiple "frames", with each frame having a
				// header made up of:
				// * a static 4-byte sequence (\x00\x21\xF9\x04)
				// * 4 variable bytes
				// * a static 2-byte sequence (\x00\x2C)
				   
				// We read through the file til we reach the end of the file, or we've found
				// at least 2 frame headers
				while (!feof($fh) && $count < 2) {
					$chunk = fread($fh, 1024 * 100); //read 100kb at a time
					$count += preg_match_all('#\x00\x21\xF9\x04.{4}\x00\x2C#s', $chunk, $matches);
				} // while

				$success = $count > 1;
				fclose($fh);
			} // if
		} // if
		
		$this->isAnimation = $success;
		
		return $success;
	}
	
	function reCrop(){
		
		$objDB = tuksiDB::getInstance();
		$arrConf = tuksiConf::getConf();
		
		//image already uploaded and cropped for editor
		$sqlCrop = "SELECT id,imagepath,imagetype FROM cmsimageupload ";
		$sqlCrop.= "WHERE imageid = '{$this->pictureid}' AND (imagetype = '2' OR imagetype = '1') order by imagetype desc";
		$rsCrop = $objDB->fetch($sqlCrop);
		if($rsCrop['num_rows']  > 0 ) {
			foreach ($rsCrop['data'] as $arrImg) {
				if($arrImg['imagetype'] > $arrCrop['imagetype']) {	
					$arrCrop = $arrImg;
				}
			}
			
			$cropSize = getimagesize($arrConf['path']['supload'] . "/cmsimageupload/" .  $arrCrop['imagepath']);
			
			$this->uploadImageInfo = array('width' => $cropSize[0],'height' => $cropSize[1],'src' => "/cmsimageupload/" . $arrCrop['imagepath']);
			
			$delta = $_POST->getStr('delta');
			if(!$delta)
				$delta = $_GET->getStr('delta');
			$this->delta = $delta;
		}
	}
	
	function resizeToCropper($arrSrcImage) {
		
		$srcImage = $arrSrcImage['src'];
		$srcName = $arrSrcImage['name'];
		
		//sætter korrekt størrelse
		$srcSize = getimagesize($srcImage);		
		$srcWidth = $srcSize[0];
		$srcHeight = $srcSize[1];
		
		if($srcWidth > $this->cropperDim['width'] || $srcHeight > $this->cropperDim['height']) {
			
			$orgImage['width'] = $srcWidth;
			$orgImage['height'] = $srcHeight;
			$orgImage['x'] = 0;
			$orgImage['y'] = 0;
			$orgImage['src'] = $arrSrcImage['src'];
			
			$this->uploadImageInfo = $this->performCrop($orgImage,2,array('minwidth' => $this->cropperDim['width'], 'minheight' => $this->cropperDim['height']));
			
			$this->delta = $this->uploadImageInfo['delta'];
			return true;
		} else {
			
			//no resize needed
			$this->delta = 1;
			$this->uploadImageInfo = array('width' => $srcSize[0],'height' => $srcSize[1],'src' => "/cmsimageupload/" . $srcName);
			return true;
		}
	}
	
	function makeRotateImages($srcImg) {
		
		if (isset($this->isAnimation) && $this->isAnimation) {
			return;
		}
		
		$objDB = tuksiDB::getInstance();
		$arrConf = tuksiConf::getConf();
		
		//delete current
		$this->reset(array(5,6,7,8));
		
		$thumb = array('minwidth' => 100,'minheight' => 100);
		$srcSize = getimagesize($srcImg);
		
		$orgImage['width'] = $srcSize[0];
		$orgImage['height'] = $srcSize[1];
		$orgImage['x'] = 0;
		$orgImage['y'] = 0;
		$orgImage['src'] = $srcImg;
		$degrees = 90;
		$doFullRotate = false;
		
		//test gif resize using convert
		$arrImg = $this->performCrop($orgImage,5,$thumb);
		
		$nb = 1;
		
		$this->arrImgRotate['degrees' . $nb] = $arrImg['src'];
		
		for($i= 6;$i < 9;$i++) {
			
			$nb++;
 			
			if(!$this->doFullRotate && ($i == 6 || $i == 8)) {
				$degrees += 90;
				continue;
			}
			
			//save the cropped image to database
			$sqlNewRow = "INSERT INTO cmsimageupload (imageid,dateadded,imagetype) VALUES ";
			$sqlNewRow.= "('{$this->pictureid}',now(),'$i')";
			$rsNewRow = $objDB->write($sqlNewRow);
			
			$newRowId = $rsNewRow['insert_id'];
			
			$filename = $newRowId . "_content_" . rand(1000,9999) . "." . $this->arrExtentions[$srcSize['mime']];
			$filepath = $arrConf['path']['supload'] . "/cmsimageupload/";
			
			$this->arrImgRotate['degrees' .$nb] = "/cmsimageupload/$filename";
			
			$sqlUpdate = "UPDATE cmsimageupload SET imagepath = '$filename' WHERE id = $newRowId";
			$objDB->write($sqlUpdate);
			$systemCmd = "convert -rotate $degrees -quality 80 " . $arrConf['path']['supload'] . $arrImg['src'] . " " . $filepath . $filename;			
			shell_exec($systemCmd);
			
			$degrees += 90;
			
		}
	}
	
	function checkRotate(){
		
		$doRotate = true;
		
		//check if we can do a full rotate
		if($this->conf['minwidth']) {
			if($this->orig['height'] < $this->conf['minwidth']) {
				$doRotate = false;	
			}	
		}
		if($this->conf['minheight']) {
			if($this->orig['width'] < $this->conf['minheight']) {
				$doRotate = false;
			}	
		}

		if (isset($this->isAnimation) && $this->isAnimation) {
			$doRotate = false;
		}
		
		$this->doFullRotate = $doRotate;
	}
	
	function getRotateImages() {
		
		$objDB = tuksiDB::getInstance();
		
		$arrRotatedId = array(5 => 1,6 => 2,7 => 3, 8 => 4);
		
		$sqlRotated = "SELECT * FROM cmsimageupload ";
		$sqlRotated.= "WHERE imageid = '{$this->pictureid} '";
		$rsRotated = $objDB->fetch($sqlRotated);
		if($rsRotated['num_rows'] > 0) {
			foreach($rsRotated['data'] as $arrRotated) {
				if($arrRotatedId[$arrRotated['imagetype']]) {
					$this->arrImgRotate['degrees' .$arrRotatedId[$arrRotated['imagetype']]] = "/cmsimageupload/" . $arrRotated['imagepath'];
				}
			}
		}
	}
	
	/**
	 * function doRotate
	 * 
	 * rotates an image
	 *
	 */
	
	function doRotate(){
		
		$arrConf = tuksiConf::getConf();
		
		$this->reset(array(2,3,4,5,6,7,8,9,10));
		
		$degrees = $_GET->getStr('rotatedegrees');
		if($degrees > 0 && $degrees < 5) {
			
			$arrRotate = array(1 => 0 , 2 => 90,3 => 180,4 => 270);
			
			$objDB = tuksiDB::getInstance();
			
			$sqlCrop = "SELECT id,imagepath FROM cmsimageupload ";
			$sqlCrop.= "WHERE imageid = '{$this->pictureid}' AND imagetype = '1'";
			$rsCrop = $objDB->fetchItem($sqlCrop);
			if($rsCrop['num_rows'] > 0 ) {
				$arrImg = $rsCrop['data'];
				$cmd = "convert -quality ".$this->quality." -rotate " . $arrRotate[$degrees] . " " .$arrConf['path']['supload'] . "/cmsimageupload/" . $arrImg['imagepath'] . " " . $arrConf['path']['supload'] . "/cmsimageupload/" . $arrImg['imagepath'];
				shell_exec($cmd);
				$this->resizeToCropper(array('src' => $arrConf['path']['supload'] . "/cmsimageupload/" . $arrImg['imagepath'] , 'name' => $arrImg['imagepath']));
				$this->makeRotateImages($arrConf['path']['supload'] . "/cmsimageupload/" . $arrImg['imagepath']); 
			}
		}
	}
	
	function getDelta($srcWidth,$srcHeight,$maxWidth,$maxHeight){
		
		if($maxWidth != 0){
			$delta_w = $maxWidth/$srcWidth;
		}else{
			$delta_w = 1;
		}
		if($maxHeight != 0){
			$delta_h = $maxHeight/$srcHeight;
		}else{
			$delta_h = 1;
		}
		
		if($delta_w < 1 || $delta_h < 1){
			if($delta_w > $delta_h){
				return $delta_h;
			} else {
				return $delta_w;
			}
		}else { 
			return 1;
		}
	}
	
	function checkSaveable() {
		
		if($this->isCropped) {
			$this->isSaveable = true;
		} else{
			//check dim
			if(is_numeric($this->conf['minwidth']) && is_numeric($this->conf['minheight']) && $this->orig['width'] > 0 && $this->orig['height'] > 0) {
				
				$delta_w = $delta_h = 0;
				
				if($this->conf['minwidth'] > 0){
					$delta_w = $this->conf['minwidth']/$this->orig['width'];
				}
				if($this->conf['minheight'] > 0){
					$delta_h = $this->conf['minheight']/$this->orig['height'];
				}	
				if($delta_w == $delta_h) {
					$this->isSaveable = true;
				}
			} else {
				
				$this->isSaveable = true;
			
			}
		}
	}
	
	function performCrop($orgImage,$type,$arrSize = ''){
		
		$objDB = tuksiDB::getInstance();
		
		$arrConf = tuksiConf::getConf();
		
		//time to resize the image
		$srcSize = getimagesize($orgImage['src']);
		$srcType = $srcSize['mime'];
		
		if(is_array($arrSize)) {
			$minwidth = $arrSize['minwidth'];
			$minheight = $arrSize['minheight'];
		} else {
			$minwidth = $this->conf['minwidth'];
			$minheight = $this->conf['minheight'];
		}
		
		//save the cropped image to database
		$sqlNewRow = "INSERT INTO cmsimageupload (imageid,dateadded,imagetype) VALUES ";
		$sqlNewRow.= "('{$this->pictureid}',now(),'$type')";
		$rsNewRow = $objDB->write($sqlNewRow);
		
		$newRowId = $rsNewRow['insert_id'];
		
		$filename = $newRowId . "_content_" . rand(1000,9999) . "." . $this->arrExtentions[$srcSize['mime']];
		$filepath = $arrConf['path']['supload'] . "/cmsimageupload/";
		
		$sqlUpdate = "UPDATE cmsimageupload SET imagepath = '$filename' WHERE id = $newRowId";
		$objDB->write($sqlUpdate);
		
		//perform crop
				
		$x =  $orgImage['x'];
		$y = $orgImage['y'];
		$x2 = $srcSize[0] - $orgImage['width'];
		$y2 =  $srcSize[1] - $orgImage['height'];
		
		if($srcSize['mime'] == "image/gif" && $y2 == 0) {
			$y2 = 1;
		}
		if($srcSize['mime'] == "image/gif" && $x2 == 0) {
			$x2 = 1;
		}

		// Imagemagick doesn't change canvas size of a gif when cropping - use repage to perform correct crop [Jonas]
		if($srcSize['mime'] == "image/gif") {
			// First crop - try to fit automatically (usually works)
			$repage = "-repage ".$x."x".$y;
			// Second crop - must be specified 
			$repage2 = "-repage ".($srcSize[0]-$x2)."x".($srcSize[1]-$y2)."+0+0";
		}
		
		$cmd = "convert -quality ".$this->quality." -crop " . $srcSize[0] . "x" . $srcSize[1] . "+" . $x . "+" . $y . " " . $repage . " " . $orgImage['src'] . " " . $filepath . $filename;
		$r = system($cmd);
		$cmd = "convert -quality ".$this->quality." -crop " . $srcSize[0] . "x" . $srcSize[1] . "-" . $x2 . "-" . $y2 . " " . $repage2 . " " . $filepath . $filename . " " . $filepath . $filename;
		$r = system($cmd);
		
		//perfom resize
		$delta = $this->getDelta($orgImage['width'],$orgImage['height'],$minwidth,$minheight);
		
		$newImage['width'] = round($orgImage['width'] * $delta);
		$newImage['height'] = round($orgImage['height'] * $delta);
		
		$cmd = "convert -quality ".$this->quality." -resize " . $newImage['width'] . "x" . $newImage['height'] . " " . $filepath . $filename . " " . $filepath . $filename;
		shell_exec($cmd);
		return array('width' => $newImage['width'],'height' => $newImage['height'], 'src' => "/cmsimageupload/" . $filename,'delta' => $delta); 
	}
	
	function performMultiResize($arrData,$arrInfo,$objField){

		$arrImages = array();
		
		$arrConf = tuksiConf::getConf();
		
		$arrSize = explode(';',$this->arrItem['fieldvalue3']);
	
		if(count($arrSize) > 1) {
			
			$intCounter = 1;
			
			$origSrc = $arrConf['path']['supload'] . "/cmsimageupload/" . $arrData['imagepath'];
			$toPath = $arrConf['path']['supload'] . "/" . $objField->tablename . "/";
			
			foreach ($arrSize as $dim){
				if($intCounter == 1){
					++$intCounter;
					continue;
				}
				
				$arrDim = explode("x", $dim);
				if (!trim($arrDim[0])) {
					continue;
				}
				
				$xDim = "";
				if($arrDim[0] != '?')
					$xDim = $arrDim[0];
				$yDim = "";
				if($arrDim[1] != '?')
					$yDim = $arrDim[1];
				
				$newName = $this->rowid . "_".$this->arrItem['colname']."_".rand(100,9999)."_".$intCounter++.".".$arrInfo['extension'];
				$arrImages[] = $objField->tablename . "/" .$newName;
				
				$cmd = "convert -quality ".$this->quality." -resize " . $xDim . "x" . $yDim . " " .$origSrc . " " . $toPath . $newName;
				shell_exec($cmd);
			}
		}
		return $arrImages;
	}
	
	function save() {
		
		$objDB = tuksiDB::getInstance();
		
		$sqlsave = "UPDATE cmsimageupload SET saveimage = 1 ";
		$sqlsave.= "WHERE imageid = '{$this->pictureid}' AND imagetype = '3'";
		$rs = $objDB->write($sqlsave);
	}
	
	/**
	 * function saveField
	 *
	 * @param $objField
	 * @return path to uploaded file
	 */	
	
	function saveField($objField) {
		
		$this->loadItem($this->itemid);
		$arrConf = tuksiConf::getConf();
		
		$objDB = tuksiDB::getInstance();
		
		$sqlCrop = "SELECT id,imagepath FROM cmsimageupload ";
		$sqlCrop.= "WHERE imageid = '{$this->pictureid}' AND imagetype = '3' AND saveimage = 1";
		$rsCrop = $objDB->fetchItem($sqlCrop);

		if($rsCrop['num_rows'] > 0 ) {
		
			$arrData = $rsCrop['data'];
			if(file_exists($arrConf['path']['supload'] . "/cmsimageupload/" . $arrData['imagepath']) && is_file($arrConf['path']['supload'] . "/cmsimageupload/" . $arrData['imagepath'])) {
				
				$arrInfo = pathinfo($arrData['imagepath']);
				$this->loadConf();
			
				$arrImages = array();
					
				$this->checkUploadFolder($objField->tablename);
				
				if($this->multi) {
					$newName = $this->rowid . "_".$this->arrItem['colname']."_".rand(100,9999)."_1.".$arrInfo['extension'];
					$arrImages = $this->performMultiResize($arrData,$arrInfo,$objField);
				} else {
					$newName = $this->rowid . "_".$this->arrItem['colname']."_".rand(100,9999).".".$arrInfo['extension'];
				}
				
				if(copy($arrConf['path']['supload'] . "/cmsimageupload/" . $arrData['imagepath'],$arrConf['path']['supload'] . "/" . $objField->tablename . "/" . $newName)){
					$returnData = $objField->tablename . "/" . $newName;
					
					if(count($arrImages) > 0) {
						$returnData.= ";" .  join(";",$arrImages);
					}
					$this->reset();
					
					return $returnData;	
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			//check if we can save anyway
			$this->loadConf();
			$this->checkSaveable();
			
			if($this->isSaveable) {
				
				//try load original image
				$sqlOrg = "SELECT id,imagepath FROM cmsimageupload ";
				$sqlOrg.= "WHERE imageid = '{$this->pictureid}' AND imagetype = '1'";
				$rsOrg = $objDB->fetchItem($sqlOrg);
				
				if($rsOrg['num_rows'] > 0 ) {
					$arrData = $rsOrg['data'];
					
					if(file_exists($arrConf['path']['supload'] . "/cmsimageupload/" . $arrData['imagepath']) && is_file($arrConf['path']['supload'] . "/cmsimageupload/" . $arrData['imagepath'])) {
						
						$orgSize = getimagesize($arrConf['path']['supload'] . "/cmsimageupload/" . $arrData['imagepath']);
						$arrOrgInfo = array('width' => $orgSize[0],'height' => $orgSize[1],'mime' => $orgSize['mime'], 'src' => $arrConf['path']['supload'] . "/cmsimageupload/" . $arrData['imagepath']);
						$delta = $this->getDelta($orgSize[0],$orgSize[1],$this->conf['minwidth'],$this->conf['minheight']);
						
						$newImage['width'] = round($orgSize[0] * $delta);
						$newImage['height'] = round($orgSize[1] * $delta);
						
						$arrInfo = pathinfo($arrData['imagepath']);
						
						$this->loadConf();
						
						$this->checkUploadFolder($objField->tablename);
						
						if($this->multi) {
							$newName = $this->rowid . "_".$this->arrItem['colname']."_".rand(100,9999)."_1.".$arrInfo['extension'];
							$arrImages = $this->performMultiResize($arrData,$arrInfo,$objField);
						} else {
							$newName = $this->rowid . "_".$this->arrItem['colname']."_".rand(100,9999).".".$arrInfo['extension'];
						}
						
						if ($this->isGifAnimation($arrOrgInfo)) {
							rename($arrOrgInfo['src'], $arrConf['path']['supload'] . "/" . $objField->tablename . "/" . $newName);
						} else {
							//check if we actually need to resize
							$cmd = "convert -quality ".$this->quality." -resize " . $newImage['width'] . "x" . $newImage['height'] . " " . $arrConf['path']['supload'] . "/cmsimageupload/" . $arrData['imagepath'] . " " . $arrConf['path']['supload'] . "/" . $objField->tablename . "/" . $newName;
							shell_exec($cmd);
						}
						
						$returnData = $objField->tablename . "/" . $newName;
					
						if(count($arrImages) > 0) {
							$returnData.= ";" .  join(";",$arrImages);
						}
						
						$this->reset();
						return $returnData;	
					}
				}		
			}
			return false;
		}
	}
	
	function getSavedFilename(){
		
		$this->loadItem($this->itemid);
		$arrConf = tuksiConf::getConf();
		
		$objDB = tuksiDB::getInstance();
		
		$sqlCrop = "SELECT id,imagepath FROM cmsimageupload ";
		$sqlCrop.= "WHERE imageid = '{$this->pictureid}' AND imagetype = '3' AND saveimage = 1";
		$rsCrop = $objDB->fetchItem($sqlCrop);
		
		if($rsCrop['num_rows'] > 0 ) {
		
			$arrData = $rsCrop['data'];
			if(file_exists($arrConf['path']['supload'] . "/cmsimageupload/" . $arrData['imagepath']) && is_file($arrConf['path']['supload'] . "/cmsimageupload/" . $arrData['imagepath'])) {
				return "/cmsimageupload/" . $arrData['imagepath'];
			} else {
				return false;
			}
		} else {
			//check if we can save anyway
			$this->loadConf();
			$this->checkSaveable();
			
			if($this->isSaveable) {
				//try load original image
				$sqlOrg = "SELECT id,imagepath FROM cmsimageupload ";
				$sqlOrg.= "WHERE imageid = '{$this->pictureid}' AND imagetype = '1'";
				$rsOrg = $objDB->fetchItem($sqlOrg);
				if($rsOrg['num_rows'] > 0 ) {
					$arrData = $rsOrg['data'];
					if(file_exists($arrConf['path']['supload'] . "/cmsimageupload/" . $arrData['imagepath']) && is_file($arrConf['path']['supload'] . "/cmsimageupload/" . $arrData['imagepath'])) {
						$orgSize = getimagesize($arrConf['path']['supload'] . "/cmsimageupload/" . $arrData['imagepath']);
						$arrInfo = array('width' => $orgSize[0],'height' => $orgSize[1],'mime' => $orgSize['mime'], 'src' => $arrConf['path']['supload'] . "/cmsimageupload/" . $arrData['imagepath']);
						$delta = $this->getDelta($orgSize[0],$orgSize[1],$this->conf['minwidth'],$this->conf['minheight']);
						
						$newImage['width'] = round($orgSize[0] * $delta);
						$newImage['height'] = round($orgSize[1] * $delta);
						
						$arrInfo = pathinfo($arrData['imagepath']);
						$newName = $arrData['id'] . "_resized_".rand(100,9999).".".$arrInfo['extension'];
						
						if ($this->isGifAnimation($arrInfo)) {
							rename($arrInfo['src'], $arrConf['path']['supload'] . "/cmsimageupload/" . $newName);
						} else {
							$cmd = "convert -quality ".$this->quality." -resize " . $newImage['width'] . "x" . $newImage['height'] . " " . $arrConf['path']['supload'] . "/cmsimageupload/" . $arrData['imagepath'] . " " . $arrConf['path']['supload'] . "/cmsimageupload/" . $newName;
							shell_exec($cmd);
						}
						$this->reset();
						return "/cmsimageupload/" . $newName;
					}
				}		
			}
			return false;
		}
	}
	
	function deleteField($objField){
		 
		$arrConf = tuksiConf::getConf();
		
		$file = $arrConf['path']['supload'] . "/" . $objField->tablename . "/" . $objField->content;
		 if(file_exists($file) && is_file($file)) {
		 	unlink($file);
		 	return "";
		 }
	}
	
	function getErrors(){
		return $this->arrError;
	}
	
	/**
	 * make upload dir if it do not exsist
	 *
	 */
	
	function checkUploadFolder($folder){
		
		$arrConf = tuksiConf::getConf();
		if(!is_dir($arrConf['path']['supload'] . "/$folder/")) {
			mkdir($arrConf['path']['supload'] . "/$folder/");
			chmod($arrConf['path']['supload'] . "/$folder/",0777);
		}
	}
}
?>
