<?
/**
 * tuksi_release class
 * 
 * Provides a number of static functions for releasing data thru table and  standaolone rows
 * It handles file release automaticly for every release type
 * 
 * Main functions:
 * 
 * releaseTableRow -> releasing a single row thru fieldtypes
 * releaseTableRowRaw -> releasing a single row NOT using fieldtypes
 * releaseTable -> releasing a table using fieldtypes
 * releaseTableRaw -> releasing a table NOT using fieldtypes
 *
 * help functions
 * 
 * getLiveTableStatus -> gives information about the difference bestween an udv and live tabel
 * 
 */
class tuksiRelease {

	/**
	 * Releases a single row thru fieldtypes
	 * Firsts check if everything is set for releasing the row
	 * livetable exists, tablelayout exists
	 * Then loads every fieldtype for the columns in the row to check for special release functions
	 * finally it releases files from the uploads dir
	 *
	 * @param string $tablename name of the table
	 * @param int $rowid the id of the row
	 * @param int $tablelayoutid if not set tablelayout it tries to find the first 
	 * @return boolean
	 */
	
	static function releaseTableRow($tablename,$rowid,$tablelayoutid = null) {
		
		$arrStatus = self::getLiveTableStatus($tablename);
		
		if(!$arrStatus['ok'])
			return false;
			
		if (!isset($tablelayoutid)) {
			$tablelayoutid = self::getTableLayout($tablename); 
		}
		
		if (!$tablelayoutid) {
			return false;
		}
		
		if(self::checkTableLayout($tablename,$tablelayoutid)) {
		
			if($arrFieldtypes = self::getFieldtypes($tablename,$tablelayoutid)) {
				
				$objDB = tuksiDB::getInstance();
				$arrField = $objDB->getRow($tablename,$rowid);
				
				self::releaseTableRowFields($arrField,$rowid,$arrFieldtypes);
				
				$tablenamelive = $tablename . "live";
				
				$sqlRelease = "REPLACE INTO $tablenamelive ";
				$sqlRelease.= "SELECT * FROM $tablename WHERE id = '{$rowid}'";
				$objDB->write($sqlRelease);
				
				self::releaseFiles($tablename,$rowid);
				
				return true;
				
			} else {
				return false;
			}
		
		} else {
			return false;
		}
	}
	
	static function releaseTable($tablename,$tablelayoutid = null,$sqlAppend = '') {
		
		
		$arrStatus = self::getLiveTableStatus($tablename);
		
		if(!$arrStatus['ok'])
			return true;
		
		if(!isset($tablelayoutid)) {
			$tablelayoutid = self::getTableLayout($tablename); 
		}
		
		if(!$tablelayoutid) {
			return false;
		}
		
		if(self::checkTableLayout($tablename,$tablelayoutid)) {

			if($arrFieldtypes = self::getFieldtypes($tablename,$tablelayoutid)) {
				
				$tablenamelive = $tablename . "live";
				
				$objDB = tuksiDB::getInstance();
				
				if(!$sqlAppend) {
					self::releaseFiles($tablename);
					$singleFileRelease = false;
				} else {
					$singleFileRelease = true;
				}
				
				//delete live data
				$sqlDel = "DELETE FROM $tablenamelive " . $sqlAppend;
				$objDB->write($sqlDel);

				$sqlRows = "SELECT * FROM $tablename " . $sqlAppend;
				$rsRows = $objDB->fetch($sqlRows);
				
				if($rsRows['num_rows']) {
					
					foreach($rsRows['data'] as $arrRow) {
						
						self::releaseTableRowFields($arrRow,$arrRow['id'],$arrFieldtypes);
						
						$sqlRelease = "REPLACE INTO $tablenamelive ";
						$sqlRelease.= "SELECT * FROM $tablename WHERE id = '{$arrRow['id']}'";
						$objDB->write($sqlRelease);
						
						if($singleFileRelease) {
							self::releaseFiles($tablename,$arrRow['id']);
						}
					}
				} 
				
				return true;
				
			} else {
				return false;
			}
		
		} else {
			return false;
		}

	}
	
	
	/**
	 * Releases a single row
	 * Firsts check if livetable exists
	 * copies data to the liverow
	 * finally it releases files from the uploads dir
	 *
	 * @param string $tablename name of the table
	 * @param int $rowid the id of the row
	 * @return boolean
	 */
	
	static function releaseTableRowRaw($tablename,$rowid){
	
		$objDB = tuksiDB::getInstance();
		
		$arrStatus = self::getLiveTableStatus($tablename);
		
		if(!$arrStatus['ok'])
			return false;
		
		$tablenamelive = $tablename . "live";
		
		$sqlRelease = "REPLACE INTO $tablenamelive ";
		$sqlRelease.= "SELECT * FROM $tablename WHERE id = '{$rowid}'";
		
		$objDB->write($sqlRelease);
		
		self::releaseFiles($tablename,$rowid);
		
		return true;
	
	}
	
	static function releaseTableRaw($tablename,$sqlAppend = ''){
	
		$objDB = tuksiDB::getInstance();
		
		$arrStatus = self::getLiveTableStatus($tablename);
		
		if(!$arrStatus['ok'])
			return false;
		
		$tablenamelive = $tablename . "live";
		
		//delete live data
		$sqlDel = "DELETE FROM $tablenamelive " . $sqlAppend;
		$objDB->write($sqlDel);
		
		$sqlRows = "SELECT * FROM $tablename " . $sqlAppend;
		$rsRows = $objDB->fetch($sqlRows);
		
		if(!$sqlAppend) {
			self::releaseFiles($tablename);
			if($rsRows['num_rows']) {
				
				$sqlRelease = "REPLACE INTO $tablenamelive ";
				$sqlRelease.= "SELECT * FROM $tablename";
				$objDB->write($sqlRelease);
			
			}
		} else {
			if($rsRows['num_rows']) {
			
				foreach($rsRows as $arrRow) {
				
					$sqlRelease = "REPLACE INTO $tablenamelive ";
					$sqlRelease.= "SELECT * FROM $tablename WHERE id = '{$arrRow['id']}'";
					$objDB->write($sqlRelease);
				
					self::releaseFiles($tablename,$arrRow['id']);
				}
			}
		}
		return true;
	
	}
	
	static function releaseTableRowFields($arrField,$rowid,$arrFieldtypes) { 
				
		//setting up field
		if(is_array($arrField) && count($arrField) > 0) {
		
			foreach ($arrField as $colname => $value) {
			
				if ($arrFieldtypes[$colname]->special_release) {
					
					$objFieldItem = $arrFieldtypes[$colname];
					$objFieldItem->rowid = $rowid;
					$objFieldItem->value = $value;
					
					//opretter og gemmer værdierne i feltet
					$objNewField = new $objFieldItem->classname($objFieldItem);
					
					if(is_callable(array($objNewField,'releaseData'))) {
						
						$objNewField->releaseData();	
					}
				}
			}
		}
	}
	
	/**
	 * Copies files from uploads to uploadslive
	 * If rowid is set then only copies files with this id
	 *
	 * @param string $tablename
	 * @param int $rowid
	 */
	
	static function releaseFiles($tablename,$rowid = 0) {
		
		$arrConf = tuksiConf::getConf();
		
		$uploadFolder = $arrConf['path']['supload'] . "/" . $tablename;
		$uploadFolderLive =  $arrConf['path']['suploadlive'] . "/" . $tablename;
		
		if(is_dir($uploadFolder)) {
			
			if(!is_dir($uploadFolderLive)) {
 				mkdir($uploadFolderLive,0777);
 			}
			
			if($rowid > 0) {
				
				$handle = opendir($uploadFolder);
				
				while(false !== ($file = readdir($handle))) {
					
					if ($file != "." && $file != "..") {
						if(preg_match("/^".$rowid."_/",$file)) {
	          	$fromPath = $uploadFolder . "/" . $file;
							$toPath = $uploadFolderLive . "/" . $file;
	          	$cmdCopy = "rsync -a --delete  {$fromPath} {$toPath}";
	          	
	          	if(!is_dir($uploadFolderLive)) {
	          		mkdir($uploadFolderLive,0777);
	           	}
           		system($cmdCopy);
	          }
					}
				}	
			} else {
				if(!is_dir($uploadFolderLive)) {
     				mkdir($uploadFolderLive,0777);
     			}
     			$cmdCopy = "rsync -a --delete  {$uploadFolder}/ {$uploadFolderLive}/";
     			system($cmdCopy);
			}
		}
	}
	
	/**
	 * Compares the udv and livetable
	 * if any diffences it finds these and returns them in an array
	 *
	 * @param String $tablename
	 * @return array
	 */
	
	static function getLiveTableStatus($tablename){
		
		$arrReturn = array('ok' => true,'errors' => array(),'exists' => true);
		
		$objDB = tuksiDB::getInstance();
		
		//check for live table
		$livename = $tablename . "live";
		$sqlChk = "DESCRIBE " . $livename;
		
		$arrRs = $objDB->fetch($sqlChk);
		
		if($arrRs['num_rows'] > 0) {

			foreach ($arrRs['data'] as $arr) {
				$arrFieldsLive[$arr['Field']] = $arr;
			}
		
			$sqlUdv = "DESCRIBE " . $tablename;
			$rsUdv = $objDB->fetch($sqlUdv);
			
			foreach ($rsUdv['data'] as $arr) {
				$arrFieldsUdv[$arr['Field']] = $arr;
			}
		
			foreach ($arrFieldsUdv as $key => $field) {
				if (!isset($arrFieldsLive[$key])) {
					$arrError[] = "field: " . $key . " doesn't exist";
				} elseif($diff = array_diff($field,$arrFieldsLive[$key])) {
					
					$arrError[] = "Error in field: ".$key;
					
					foreach ($diff as $fieldkey => $data) {
						$arrError[] = "$fieldkey : $data  != ". $arrFieldsLive[$key][$fieldkey] . "\n";
					}
				}
			}
		} else {
			$arrReturn['exists'] = false;
			$arrReturn['errors'][] =  "Live table doesn't exist";
		}
		
		if(count($arrReturn['errors']) > 0) {
			$arrReturn['ok'] = false;
		}
		
		return $arrReturn; 
	}
	
	
	static function setTableReleaseInfo($tablename){
		
		self::setReleaseInfo($tablename);
		
	}
	
	static function setTableRowReleaseInfo($tablename,$rowid){
		
		self::setReleaseInfo($tablename,$rowid);
		
	}
	
	static function setReleaseInfo($tablename,$rowid = null){
		
		if(self::checkTableReleaseDataColumns($tablename)) {
			$objDB = tuksiDB::getInstance();
		
			$sqlRelease = "UPDATE $tablename SET datepublished = now()";
			
			if(isset($rowid) && $rowid > 0) {
				$sqlRelease.= " WHERE id = '$rowid'";
			}
			
			$objDB->write($sqlRelease);
		}
		
	}
	
	private static function checkTableReleaseDataColumns($tablename){
		
		$objDB = tuksiDB::getInstance();
		
		$sqlChk = "SHOW COLUMNS FROM $tablename WHERE Field = 'datepublished'";
		$rsChk = $objDB->fetch($sqlChk);
		if($rsChk['num_rows'] > 0) {
			return true;
		} else {
			return false;
		}
		
	
	}
	
	static function checkTableLayout($tablename,$tablelayoutid) {
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "SELECT * FROM cmstablelayout WHERE tablename = '{$tablename}' AND id = '{$tablelayoutid}'";
		$rs =  $objDB->fetch($sql);
		if($rs['num_rows'] > 0) {
			return true;
		} else {
			return false;
		}
		
	}
	
	static function getFieldTypes($tablename,$tablelayoutid) {
		
		$objDB = tuksiDB::getInstance();
		
		$sqlFieldTypes = "SELECT distinct(fi.id), ft.id as cmsfieldtypeid,ft.classname, ";
		$sqlFieldTypes.= "fi.colname, fi.name, fi.fieldvalue1, fi.fieldvalue2, ";
		$sqlFieldTypes.= "fi.fieldvalue3, fi.fieldvalue4, fi.fieldvalue5, fi.helptext,fi.cmsfieldgroupid,special_release ";
		$sqlFieldTypes.= "FROM cmsfielditem fi, cmsfieldtype ft ";
		$sqlFieldTypes.= "WHERE fi.itemtype = 'table' AND fi.tablename = '{$tablename}' AND ";
		$sqlFieldTypes.= "fi.relationid = '{$tablelayoutid}' AND fi.cmsfieldtypeid = ft.id ";
		$rsFieldTypes = $objDB->fetch($sqlFieldTypes,array('type' => 'object'));
		
		if($rsFieldTypes['num_rows'] > 0) {
			
			$arrFieldTypes = array();
			
			foreach($rsFieldTypes['data'] as $obj) {
				$arrFieldTypes[$obj->colname] = $obj;
			}
			
			return $arrFieldTypes;
		
		} else {
			return false;
		}
	}
	
	static function getTableLayout($tablename){
		
		$objDB = tuksiDB::getInstance();
		
		// if table layout notfilled, lets see if we got one
		$sqlLayout = "SELECT id FROM cmstablelayout WHERE tablename = '{$tablename}'";
		$rsLayout = $objDB->fetch($sqlLayout) or print mysql_error();
		if ($rsLayout['num_rows'] == 1) {
			return $rsLayout['data'][0]['id'];
		} else {
			return false;
		}
	}
}
?>
