<?php

/**
 * Enter description here...
 *
 * @todo PHP doc missing
 * @package tuksiBackendModule
 */

class mBackendRelease extends mBackendBase {
	
	function __construct(&$objMod){
		parent::__construct($objMod);

		$this->tpl = new tuksiSmarty();
	}	

	function getHtml(){
		
		$objDB = tuksiDB::getInstance();
		$objPage = tuksiBackend::getInstance();
		
		$objStdTpl = new tuksiStandardTemplateControl();
		$objStdTpl->addHeadline($objPage->cmstext('release_overview'));
		
		
		if($this->userActionIsSet("RELEASESINGLE") && $objPage->arrPerms['RELEASE']) {
			$tableName = $_POST->getStr('releaseSingleTable');
			tuksiRelease::releaseTableRaw($tableName);
			tuksiRelease::setTableReleaseInfo($tableName);
		}
		if($this->userActionIsSet("RELEASE") && $objPage->arrPerms['RELEASE']) {
				// Getting tables to release
				$sqlTables = "SELECT name FROM cmsrelease";
				$rsTables = $objDB->fetch($sqlTables);
				foreach($rsTables['data'] as $arrRelease) {
					tuksiRelease::releaseTableRaw($arrRelease['name']);
					tuksiRelease::setTableReleaseInfo($arrRelease['name']);
				} 
				$objPage->status($objPage->cmstext('sitereleased'));
		}
		if($this->userActionIsSet("SYNCHRONIZE") && $objPage->arrPerms['ADMIN']) {
			
			$arrLiveTables = $_POST->getArray('livetables');
			$arrExists = array();
			
			$sqlCheckLive = "SELECT * FROM cmsrelease ";
			$rsCheckLive = $objDB->fetch($sqlCheckLive) or print mysql_error();
			if($rsCheckLive['num_rows'] > 0) {
				foreach ($rsCheckLive['data'] as $arrRelease) {
					if(!in_array($arrRelease['name'],$arrLiveTables)) {
						//delete from list
						$sqlDel = "DELETE FROM cmsrelease WHERE id = '{$arrRelease['id']}'";
						$objDB->write($sqlDel);
					} else {
						$arrExists[] = $arrRelease['name'];
					}
				}
			}
			$arrNew = array_diff($arrLiveTables,$arrExists);
			
			foreach ($arrNew as $tablename) {
				
				$sqlInsert = "INSERT INTO cmsrelease (name,namelive) VALUES ('".$tablename."','".$tablename."live')";
				$objDB->write($sqlInsert);
				
				$sql="SHOW CREATE TABLE $tablename";
				$rs = $objDB->fetchItem($sql);
				
				if ($rs['num_rows'] > 0) {
					$sqlCreate = $rs['data']['Create Table'];
					$sqlCreate = str_replace("`" . $tablename . "`", "`" .$tablename . "live`", $sqlCreate);
					$objDB->write($sqlCreate);
				}
			}
		}
		
		$arrTables = $objDB->getTables();
		$arrTablesPassed = array();
		
		$error = false;
		
		foreach ($arrTables as $tablename) {
			
			$arrTable = array();
			$arrTable['name'] = $tablename;
			
			if (!preg_match("/live$/", $tablename, $m)) {
				
				$arrTable['error'] = false;
				
				if (in_array($tablename . "live", $arrTables)) { 
					
					$nbRows = $this->getNumRows($tablename);
					$nbRowsLive = $this->getNumRows($tablename. "live");
					
					$arrTable['rows'] = $nbRows - $nbRowsLive;
					
					$arrTable['islive'] = true;
					
					$arrFields = $objDB->getFields($tablename);
					$arrFieldsLive = $objDB->getFields($tablename."live");
					
					$arrTable['nbfields'] = $arrFields['num_rows'];
					$arrTable['nblivefields'] = $arrFieldsLive['num_rows'];
					
					$arrNotLive = $this->getDiffFields($arrFields['data'],$arrFieldsLive['data']);
					$arrLive = $this->getDiffFields($arrFieldsLive['data'],$arrFields['data']);
					
					if(count($arrNotLive['missing']) > 0) {
						$arrTable['livestatus'] = "Missing fields:<br /> " . join("<br />",$arrNotLive['missing']); 
						$arrTable['error'] = true;
						$error = true;
					}
					
					if(count($arrNotLive['diff']) > 0){
						$arrTable['error'] = true;
						$error = true;
						$arrTable['diffstatus'] = "Following fields are different: <br />";
						foreach ($arrNotLive['diff'] as $name => $arrField) {
							foreach ($arrField as $arrKeys) {
								$arrTable['diffstatus'].= "$name: " . $arrKeys['key'] . " (".$arrKeys['self']." != ".$arrKeys['live'].")<br />";
							}
						}
					}
				} else {
					
					$sqlCheckLive = "SELECT * FROM cmsrelease WHERE name = '$tablename'";
					$rsCheckLive = $objDB->fetch($sqlCheckLive) or print mysql_error();
					
					if($rsCheckLive['num_rows'] == 1) {
						$sqlDel = "DELETE FROM cmsrelease WHERE id = {$rsCheckLive['data'][0]['id']}";
						$objDB->write($sqlDel);
					}
					
					$arrTable['islive'] = false;	
				} 
				$arrTablesPassed[] = $arrTable;
			} 
		}
		
		if($error) {

			$objStdTpl->addHtml($objPage->cmstext('release_error'));
		
		} else {
			
			if($objPage->arrPerms['RELEASE']){
				$this->addButton("RELEASE",$objPage->cmstext('release'),"RELEASE");
			}
			
			$strText = $objPage->cmstext('release_text');	
			$objStdTpl->addHtml($strText);
		
		}
		
		if($objPage->arrPerms['ADMIN']) {
			$this->tpl->assign("tables",$arrTablesPassed);	
			$this->addButton("SYNCHRONIZE",$objPage->cmstext('synchronizetables'),"ADMIN");
		}
		
		$html = $objStdTpl->fetch();
		$this->tpl->assign('content',$html);
		
		$returnHtml = parent::getHTML();
		return $returnHtml;
	}
	
	function getDiffFields($arr1,$arr2){
		
		$cleanArr1 = array();
		$cleanArr2 = array();
		$arrReturn = array();
		
		foreach ($arr2 as $arrField) {
			$cleanArr2[$arrField['Field']] = $arrField;
		}
		
		foreach ($arr1 as $arrField) {
			//check if the field is the same
			if(isset($cleanArr2[$arrField['Field']])) {
				$arrDiff = array_diff($arrField,$cleanArr2[$arrField['Field']]);	
				if(count($arrDiff) > 0) {
					foreach ($arrDiff as $key => $value) {
						$arrReturn['diff'][$arrField['Field']][] = array('key' => $key,
																												'self' => $value,
																												'live' => $cleanArr2[$arrField['Field']][$key]);
					}
				}
			} else {
				$arrReturn['missing'][] = $arrField['Field'];
			}
		}
		return $arrReturn;
	}
	
	function saveData(){
		
	}
	
	
	function getNumRows($tablename){
		$objDB = tuksiDB::getInstance();
		$sqlNb = "SELECT count(*) as nbRows FROM $tablename";
		$rsNb = $objDB->fetchItem($sqlNb);
		return $rsNb['data']['nbRows'];
	}
}
?>
