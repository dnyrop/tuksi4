<?php

/**
 * Enter description here...
 *
 * @package tuksiFieldType
 */

class fieldPollStatistics extends field{

	function __construct($objField) {
		parent::field($objField);
		$this->tpl = new tuksiSmarty();
		
	}

	function getHTML() {
		
		$objDB = tuksiDB::getInstance();
		
		$html = "";
		
		$sqlVotes = "SELECT *,count(v.id) as votes FROM cmspollanswer a ";
		$sqlVotes.= "LEFT JOIN cmspollvote v on (a.id = v.cmspollanswerid) ";
		$sqlVotes.= "WHERE a.cmspollid = '{$this->objField->rowid}' ";
		$sqlVotes.= "GROUP BY a.id ORDER BY a.seq";
		
		$arrReturn = $objDB->fetch($sqlVotes);
		//print_r($arrReturn);
		
		if ($arrReturn['num_rows'] > 0) {
			
			$all = 0;
			$maxWidth = 470;
			$arrAllVotes = $arrReturn['data'];
			
			foreach ($arrAllVotes as $arrData) {
				$all+= $arrData['votes'];
			}
			
			foreach ($arrAllVotes as $q) {
				$q['percent'] = $q['votes'] / $all;
				$q['width'] = round($q['percent'] * $maxWidth, 0);
				$q['percent'] = round($q['percent'] * 100, 0);
				$arrVotes[] = $q;
			}
			$this->tpl->assign('votes', $arrVotes);
		}
		/*
		$rsVotes = $objDB->query($sqlVotes);
		
		if(mysql_num_rows($rsVotes) > 0) {
				
			$all= 0;
			$maxWidth = 470;
			
			while($arrData = mysql_fetch_assoc($rsVotes)) {
				$arrAllVotes[] = $arrData;
				$all+= $arrData['votes'];
			}
			
			foreach ($arrAllVotes as $q) {
				$q['percent'] = $q['votes'] / $all;
				$q['width'] = round($q['percent'] * $maxWidth,0);
				$q['percent'] = round($q['percent'] * 100,0);
				$arrVotes[] = $q;
			}
			$this->tpl->assign('votes',$arrVotes);
		}*/
		
		return parent::returnHtml($this->objField->name,$this->tpl->fetch("fieldtypes/fieldPollStatistics.tpl"),array('fullwidth' => true));
	}

	function saveData() {
		return;
	}

	function getListHtml() {
		return;
	}
}
?>