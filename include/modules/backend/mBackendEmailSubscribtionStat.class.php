<?php

/**
 * Enter description here...
 *
 * @todo PHP doc missing
 * @todo Language problem $arrMonth
 * @package tuksiBackendModule
 */

class mBackendEmailSubscribtionStat extends mBackendBase {
	
	function __construct(&$objMod){
		
		$objPage = tuksiBackend::getInstance();
		
		if ($objPage->action == 'BACK') {
			$url = tuksiTools::getBackendUrl($objPage->treeid,$objPage->tabid);
			header("Location: $url");
			exit();
		}
		
		parent::__construct($objMod);
		
		$this->dateField = $this->objMod->value1;
		$this->WhereClause = str_replace('#CMSSITELANGID#', (int) $objPage->arrTree['cmssitelangid'], $this->objMod->value2);
		
		$this->tpl = new tuksiSmarty();
	}	

	function getHtml(){
		
		$objDB = tuksiDB::getInstance();
		$objPage = tuksiBackend::getInstance();
		
		$arrMonth = array('Januar','Februar','Marts','April','Maj','Juni','Juli','August','September','Oktober','November','December');
		
		$this->prMonth = false;
		
		if($_GET->getStr('year') && $_GET->getStr('month')) {
			
			$this->prMonth = true;
			
			$year = $_GET->getStr('year');
			$month = $_GET->getStr('month');
		
			$sql = "SELECT DAYOFMONTH({$this->dateField}) as d, YEAR({$this->dateField}) as y, MONTH({$this->dateField}) as m, count(email) as count ";
			$sql.= "FROM mail_email WHERE {$this->WhereClause} GROUP BY y,m,d having y = '$year' AND m = '$month' ORDER BY d desc"; 			
			$rs = $objDB->fetch($sql);
			
		} else {
			
			$sql = "SELECT year({$this->dateField}) as y, MONTH({$this->dateField}) as m, count(email) as count ";
			$sql.= "FROM mail_email WHERE {$this->WhereClause} GROUP BY y,m ";
			$sql.= "ORDER BY y desc, m desc"; 
			$rs = $objDB->fetch($sql);
		
		}
		
		$arrTest = array();
		foreach($rs['data'] as &$row){
			$arrTest[] = $row['count'];
		}
		
		if (count($arrTest))
			$max = max($arrTest);
		else
			$max = 0;
		
		foreach($rs['data'] as &$arrData) {
			
			$width = $arrData['count'] / $max * 500;
			
			if($this->prMonth) {
		
				$arrData['d'] = $arrData['d'] < 9 ? 0 . $arrData['d'] : $arrData['d']; 
				$arrData['m'] = $arrData['m'] < 9 ? 0 . $arrData['m'] : $arrData['m']; 
				$arrData['y'] = substr($arrData['y'],2,2);
				
				$name = $arrData['d'] . "." . $arrData['m'] .".". $arrData['y'];
			
				$arrList[] = array(	'name' => $name,
														'width' => $width,
														'count' => $arrData['count']);
					
			} else {
				
				$name = $arrMonth[$arrData['m'] - 1] . " " . $arrData['y'];
			
				$arrList[] = array(	'name' => $name,
														'width' => $width,
														'year' => $arrData['y'],
														'month' => $arrData['m'],
														'count' => $arrData['count'],
														'link' => true,
														'url' => tuksiTools::getBackendUrl($objPage->treeid,$objPage->tabid) . "&year=".$arrData['y'] . "&month=" . $arrData['m']);
			}
			
			
			
		}
		
		if($this->prMonth) {
			$this->addButton('BACK',"","READ");
		}
		
		$this->tpl->assign('list',$arrList);
		
		$returnHtml = parent::getHTML();
		$objStd = new tuksiStandardTemplateControl();
		$objStd->addHtml($returnHtml);
		
		return $objStd->fetch();
	}
	
	function saveData(){
		
	}
}
?>
