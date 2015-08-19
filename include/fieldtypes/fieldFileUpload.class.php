<?

/**
 * Simpel felttype til uploading af filer 
 *
 * fieldvalue1: filetypes to allow (jpg, gif, pdf..)
 * fieldvalue2: Path to files on local disk. (full path) These are shown in selecbox
 * fieldvalue3: Picture must have this size (123x456) 
 * fieldvalue4: Use media db via selectbox. Files area uploaded to global area.
 *							1: Checkbox with choice to insert uploaded file in mediaDB. 
 *							2: No checkbox with choice (No insert)
 *							3: Always insert to MediaDB

 * Speciel mode for FCK Editor exist. And is set via setEditorMode() function. Editormod does not show preview
 * @package tuksiFieldType
 */
class fieldFileUpload extends field {

	public $primaryIDName = 'id';
	private $mediaDBType= 0;
	private $editorMode = false;

	function __construct($objField) {

		parent::field($objField);
		
		$this->denylist = array("php","php3","php4","php5","phtml","phps","cgi","pl");
		
		$this->objText = tuksiText::getInstance('fieldtypes/fieldFileUpload.tpl');
		
		if ($this->objField->fieldvalue2) {
			
			$this->pathFiles = $this->objField->fieldvalue2;
			if (!file_exists($this->pathFiles)) {
				$this->setError("Fieldvalue2", $this->objText->getText('error_ftpfolder') ." ({$this->pathFiles})");
			}
		}

		$this->mediaDBType = $this->objField->fieldvalue4;

	} // End fieldFileupload();

	/**
	 * editorMode is used by FCK Editor fieldtype. 
	 * FCK needs to get the file returned via JS.
	 * 
	 * @access public
	 * @return void
	 */
	function editorMode() {
		$this->editorMode = true;
	}

	function makeDownloadUrl($id, $filename) {
		$arrConf = tuksiConf::getConf();

		$url = '';

		// If in the backend editing newsletter page
		if ($arrConf['setup']['system'] == 'backend') {
			$objPage = tuksiBackend::getInstance();

			if ($objPage->arrTree['cmscontextid'] == 3) {
		 	 $url = '/newsletter';
			} 
		}
		$url.= '/downloads/' . $id . '/';
		$url.= tuksiTools::fixname(tuksiFile::getBaseNoExt($filename)) . '.' . tuksiFile::getExt($filename);

		return $url;
	}
	function getHTML() {
		$objPage = tuksiBackend::getInstance();
		$arrConf =tuksiConf::getConf();
		$objDB = tuksiDB::getInstance();
		
		$tpl = new tuksiSmarty();
		$tpl->assign('media_type', $this->mediaDBType);
		$tpl->assign('editor_mode', $this->editorMode);
		
		if ($arrReturn = $this->checkFieldvalues())
			return $arrReturn;
				
		$help_tag = parent::getHtmlStart();
		
		$tpl->assign("helptag",$help_tag);
		
		if ($this->objField->fieldvalue2) {
						
			$pathFiles = $this->objField->fieldvalue2;
			if (file_exists($this->pathFiles ))
			
			$tpl->assign("ftp","1");
			
			if ($handle = opendir($this->pathFiles )) {
				$ftp_files = array();
				while (false !== ($file = readdir($handle))) { 
					if (is_file($this->pathFiles  . "/" . $file) && !preg_match("/^\./", $file)) {
						$ftp_files[] = $file;
					}
				}

				$tpl->assign("ftp_file",$ftp_files);
			}
		}	

		if ($this->mediaDBType) {
			$sql = "SELECT * ";
			$sql.= "FROM cmslinkupload ";
			$sql.= "ORDER BY filename";
			$arrRetList = $objDB->fetch($sql);

			$arrSelected = array();

			foreach ($arrRetList['data'] as &$arrFile) {
				if ($this->editorMode) {
								$arrFile['file'] = $this->makeDownloadUrl($arrFile['id'], $arrFile['filename']);
				}
				
				if ($arrFile['id'] == $this->objField->value || $arrFile['file'] == $this->objField->value) {
						$arrFile['selected'] = true;
				}

				if ($arrFile['selected']) {
					$arrSelected = $arrFile;
					$arrFile['selected'] = true;
				}
			}

			$tpl->assign('media', $arrRetList['data']);
			$tpl->assign('media_selected', $arrSelected);
			
		}

		if ($this->objField->value) {

			$value = $this->objField->value;

			// If mediafile selected. Get filename from $arrSelected
			if ($arrSelected) {
				$value = $arrSelected['file'];
			}
			
			$tpl->assign("value_old","1");
			
			$tpl->assign("slet_msg",$objPage->cmstext("slet_fil"));
		  
			
			preg_match("/\.([a-zA-Z0-9+]+)$/", $value,$m);
			if($m[1]) {
				$ext = $m[1];
			}
			
			$files = explode(";", $value);
			
			foreach ($files as $file) {
				if (preg_match("/(gif|jpg|png)$/", $ext, $m) && file_exists($arrConf['path']['supload'] . "/" . $file)) {
					list($x,$y)= getimagesize($arrConf['path']['supload'] . "/" . $file);
					if ($x > 600)  
						$size = "width=\"600\"";
					else
						$size = "width=\"$x\" height=\"$y\"";
					
					$path = $arrConf['path']['upload'] . '/' .$file;
					$tpl->assign("size",$size);
					$tpl->assign("image_path",$path);
				} else { 
					
					$category = tuksiFile::getFileCategory($ext);
					
					$icon = $arrConf['path']['vimages'] . "icons/$category.gif";
					
					$path = $icon ;
					$tpl->assign("image_path",$path);
				}
			}
		}
		
		$tpl->assign("value",$this->objField->value);
		$tpl->assign("tagname",$this->objField->htmltagname);
		$error = "";
		if(count($this->arrError)) {
			$error = join($this->arrError,"<br />");
		}
		$tpl->assign("error",$error);
		
		$HtmlTag = $tpl->fetch("fieldtypes/fieldFileUpload.tpl");
		
		return parent::returnHtml($this->objField->name, $HtmlTag);
	}
	
	function saveData() {
		$arrConf =tuksiConf::getConf();
		$objPage = tuksiBackend::getInstance();
		$objDB = tuksiDB::getInstance();

		$bookOk = 1;

		$filename 		= $_FILES[$this->objField->htmltagname]['name'];
		$filename_tmp	= $_FILES[$this->objField->htmltagname]['tmp_name'];
		$filetype 		= $_FILES[$this->objField->htmltagname]['type'];
		$file_old 		= $_POST->getStr($this->objField->htmltagname . "_OLD");
		$file_delete	= $_POST->getStr($this->objField->htmltagname . "_DELETE");
		$file_reset 	= $_POST->getStr($this->objField->htmltagname . "_RESET");
		$filename_ftp	= $_POST->getStr($this->objField->htmltagname . "_ftp");
		$media_save		= $_POST->getStr($this->objField->htmltagname . "_media_save");

		//Check for illegal files
		$validExtension=true;
		$fil_rev=strrev($filename);
		$file_ext=strrev(substr($fil_rev,0,strpos($fil_rev,".")));
		
		if(in_array($file_ext,$this->denylist)){
			$validExtension=false;
			$this->arrError[] = $objPage->cmstext('illegal_extension');
		}
		
		
		if ($filename_ftp) {
			$filename_tmp =  $this->objField->fieldvalue2 . "/" . $filename_ftp;
			$filename 	= $filename_ftp;
			$filetype 	= "application/octet-stream";
		}
		

		if ($file_reset) {
			// Making return Sql
			$ReturnSQL = $this->objField->colname . " = ''";
			return $ReturnSQL;
		}

		if ($file_delete) {
			unset($filename_tmp);
							
			$this->deleteData($file_old);

			// Making return Sql
			$ReturnSQL = $this->objField->colname . " = ''";

			return $ReturnSQL;
		}
						
		$this->objField->fieldvalue1 = str_replace(" ", "", $this->objField->fieldvalue1);
		if ((is_uploaded_file($filename_tmp) || $filename_ftp) && $validExtension) {
			//$objPage->addDebug("File upload (3), " . $this->objField->htmltagname . " -> " . $filename . ", Filetype:" . $filetype);
			
			$extension = tuksiFile::extByFileType($filetype, $filename);
			
			if ($extension) {
				$allowExtensions = explode(",", $this->objField->fieldvalue1);

				if (!$this->objField->fieldvalue1	|| in_array($extension, $allowExtensions)) {
	
					if ($this->objField->fieldvalue3 && ($extension == "gif" || $extension == "jpg" || $extension == "png")) {
						
						list($x,$y)= getimagesize($filename_tmp);
						
						list($req_x, $req_y) = explode("x", $this->objField->fieldvalue3);
						
						if ($x != $req_x || $y != $req_y) {
							$bookOk = 0;
							$this->arrError[] = $objPage->cmstext('wrong_picture_dimensions');
							$this->objField->value = "";
						}
						
					}
					if ($bookOk) {

						$tablename = $this->objField->tablename;
						$rowid = $this->objField->rowid;
						$colname = $this->objField->colname;
						$deleteOld = true;
						$bOk = true;

						$useMediaDB = true;
						// Only save to media i user choose to or set to always insert
						if (($this->mediaDBType == 1 && $media_save) ||
									$this->mediaDBType == 3) {

							$useMediaDB = true;
							$tablename = 'cmslinkupload';
							$colname = 'file';
							$deleteOld = false;

							$objUser = tuksiBackendUser::getInstance();

							$arrUser = tuksiBackendUser::getUserInfo();

							$sql = "INSERT INTO $tablename (filename, uploaded, cmsuserid) ";
							$sql.= "VALUES('{$filename}', NOW(), '{$arrUser['id']}')";

							$arrReturnInsert = $objDB->write($sql);

							if ($arrReturnInsert['ok']) {
								$rowid = $arrReturnInsert['insert_id'];
							} else {
								$bookOk = false;
								$this->arrError[] = $objPage->cmstext('insert_media');
							}

						}

						if ($bookOk) {

							$dir = $arrConf['path']['supload']. "/" . $tablename;
							if (!file_exists($dir)) 
								mkdir($dir, 0777);
 	  
							// Deleting old files
							if ($deleteOld) {
								$cmd = "rm -rf " . $dir . "/" . $rowid . "_" . $colname . "*";
								system($cmd);
							}
   
							$file_original = $rowid . "_" . $colname . "_" . round(rand(1,10000)) . "." . $extension;
					
							copy($filename_tmp, $dir . "/" . $file_original);
							$this->objField->value	= $tablename . "/". $file_original;

							if ($useMediaDB) {
								$sqlUpdMedia = "UPDATE cmslinkupload SET file = '{$this->objField->value}' WHERE id = '{$rowid}'";
								$objDB->write($sqlUpdMedia);
								
								if ($this->editorMode) {
									$this->objField->value = $this->makeDownloadUrl($rowid, $filename);
								}
							}
							
						}
					}
   
				} else {
					$this->arrError[] = $objPage->cmstext('wrong_file_type');
				$this->objField->value = "";
				}
			} else {
				$this->arrError[] = $objPage->cmstext('wrong_file_type');
				$this->objField->value	= "";
			}
		} elseif ($_POST->getStr($this->objField->htmltagname . '_media')) {
			$value = $_POST->getStr($this->objField->htmltagname . '_media');
			$this->objField->value = $value;
		}

		if ($this->objField->value) 
			$ReturnSQL = $this->objField->colname . " = '" . mysql_escape_string ($this->objField->value). "'";
			
			
		return $ReturnSQL;
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

	function deleteData($value_old = '') {
		$arrConf =tuksiConf::getConf();

		if ($value_old)
			$this->objField->value = $value_old;

		if ($this->objField->value) {
			$filename_path = $arrConf['path']['supload'] . '/' . $this->objField->value;

			if (file_exists($filename_path)) {
				unlink($filename_path);
			}
		}
	}

	function getListHtml() {
		$arrConf =tuksiConf::getConf();

		$file = $this->objField->value;
			if (preg_match("/.(gif|jpg|png)$/", $file, $m)) {
					list($x,$y)= getimagesize($arrConf['path']['supload'] . "/" . $file);
					if ($x > 600)  
						$size = "width=\"600\"";
					else
						$size = "width=\"$x\" height=\"$y\"";
	
			$htmlTag = "<img $size src=\"{$arrConf['path']['upload']}/{$file}\"><br>";
		}
		return $htmlTag;
	}

} // END Class
?>
