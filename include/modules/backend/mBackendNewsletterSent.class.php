<?php
/**
 * Enter description here...
 *
 * @todo PHP doc missing
 * @package tuksiBackendModule
 */
class mBackendNewsletterSent extends mBackendBase {
	
	function __construct(&$objMod){
		parent::__construct($objMod);

		$this->tpl = new tuksiSmarty();
	}	

	function getHtml() {
		
		$objDB = tuksiDB::getInstance();
		$objPage = tuksiBackend::getInstance();
		$objText = tuksiText::getInstance('modules/backend/mBackendNewsletterSent.tpl');
		
		if($objPage->arrPerms['DELETE'] && $objPage->action == "DELETE" && $_POST->getInt('deleteNewsletterId')) {
			
			$newsletterId = $_POST->getInt('deleteNewsletterId');
			
			$sqlDel = "SELECT *, date_format(datesent, '%Y.%m.%d.%H') as dato ";
			$sqlDel.= "FROM mail_newslettersent WHERE id = '{$newsletterId}'";
			$rsDel = $objDB->fetch($sqlDel);
		
			if ($rsDel['ok'] && $rsDel['num_rows'] > 0) {
				
				tuksiNewsletterStat::deleteStats($newsletterId);

				$strMsg = $objText->getText('newsletter_sent_deleted');
				$strMsg = str_replace("{name}",$arrDel['name'],$strMsg);
				$objPage->status($strMsg);
				
			} else {
							$objPage->alert($objText->getText('newsletternotfound'));
			}
		} elseif ($_GET->getInt('newsletterid') > 0) {
			
			$newsletterId = $_GET->getInt('newsletterid');
			
			$sql = "SELECT mn.id as newsid,mn.*,mns.id as sentid ";
			$sql.= "FROM cmstree as mn, mail_newslettersent mns ";
			$sql.= "WHERE mn.id = mns.mail_newsletterid AND mns.id = '{$newsletterId}'";
			$rs = $objDB->fetchItem($sql);
			
			if ($rs['ok'] && $rs['num_rows']) {
				
				$arrNewsletter = $rs['data'];
				
				$sqlMx = "SELECT sum(isviewed) as nb ";
				$sqlMx.= "FROM mail_tracking WHERE mail_newslettersentid = '{$newsletterId}' ";
				$rsMx = $objDB->fetchItem($sqlMx);
				$max = $rsMx['data']['nb'];
				
				$sqlSplit = "SELECT year(dateviewed_first) as y, MONTH(dateviewed_first) as m, DAYOFMONTH(dateviewed_first) as d, sum(isviewed) as nb ";
				$sqlSplit.= "FROM mail_tracking WHERE mail_newslettersentid = '{$newsletterId}' AND isviewed = 1 ";
				$sqlSplit.= "GROUP BY y, m, d ORDER BY y desc, m desc, d desc";
				$rsSplits = $objDB->fetch($sqlSplit);

				foreach ($rsSplits['data'] as &$arrData) {
					if ($arrData['d'] < 10) {
						$arrData['d'] = '0' . $arrData['d'];
					}
					if ($arrData['m'] < 10) {
						$arrData['m'] = '0' . $arrData['m'];
					}
					$arrView[] = array(	'nb' => $arrData['nb'],
															'date' =>  $arrData['d'] . "." .  $arrData['m'] . "." . $arrData['y'],
															'width' => (500 / $max) * $arrData['nb']);
				}
				
				$arrStats[] = array('headline' => $objText->getText('nb_read') . " ($max)",
														'stats' => array( 
															array(	'name' => '',
																			'lines' => $arrView)));
				
				$allMax = 0;
				
				
				$sqlModule = "SELECT * FROM pg_content WHERE cmstreeid = '".$arrNewsletter['newsid'] . "'";
				$rsModule = $objDB->fetch($sqlModule);
				
				//print $sqlModule;
				
				$arrModLink = array();
				
				foreach($rsModule['data'] as &$arrModule) {
					
					if($arrModule['link']) {
					
						$sqlMax = "SELECT COUNT(te.isclicked) AS nb ";
						$sqlMax.= "FROM mail_trackingelement te, mail_tracking t ";
						$sqlMax.= "WHERE t.id = te.mail_trackingid AND te.mail_newsletterelementid =".$arrModule['id']." AND ";
						$sqlMax.= "t.mail_newslettersentid = '{$newsletterId}' ";
						$sqlMax.= "ORDER BY nb desc LIMIT 1";	

						$rsMax = $objDB->fetchItem($sqlMax);
						$modMax = $rsMax['data']['nb'];
						
						$allMax+= $modMax;
						
						$sqlModStat = "SELECT YEAR(te.dateclicked_first) AS y, MONTH(te.dateclicked_first) AS m,";
						$sqlModStat.= "DAYOFMONTH(te.dateclicked_first) as d, COUNT(te.isclicked) as nb ";
						$sqlModStat.= "FROM mail_trackingelement te, mail_tracking t ";
						$sqlModStat.= "WHERE t.id  = te.mail_trackingid AND te.mail_newsletterelementid =".$arrModule['id']." ";
						$sqlModStat.= "AND t.mail_newslettersentid = '{$newsletterId}' ";
						$sqlModStat.= "GROUP BY y, m, d ";
						$sqlModStat.= "ORDER BY y desc, m desc, d desc";	
						
						$rsModStat = $objDB->fetch($sqlModStat);
						
						$arrModStat = array();
						
						foreach ($rsModStat['data'] as $arrLink) {
							if ($arrLink['d'] < 10) {
								$arrLink['d'] = '0' . $arrLink['d'];
							}
							if ($arrLink['m'] < 10) {
								$arrLink['m'] = '0' . $arrLink['m'];
							}
							$arrModStat[] =  array(	'nb' => $arrLink['nb'],
																			'date' =>  $arrLink['d'] . "." .  $arrLink['m'] . "." . $arrLink['y'],
																			'width' => (500 / $modMax) * $arrLink['nb']);
						}
						
						$arrModLink[] = array('name' => $arrModule['headline'] . " (".$modMax.")",
																	'lines' => $arrModStat);
						
					}
				}
				
				$arrStats[] = array('headline' => $objText->getText('module_statistic') . " ($allMax)",
														'stats' => $arrModLink);

				$maxLinkTrackingAll = 0;
																	
				
				$sqlLinks = "SELECT * FROM mail_link ";
				$sqlLinks.= "WHERE pg_page_templateid = '{$arrNewsletter['pg_page_templateid']}' AND cmssitelangid = '{$arrNewsletter['cmssitelangid']}'";
				$rsLinks = 	$objDB->fetch($sqlLinks);
				
				if($rsLinks['ok'] && $rsLinks['num_rows'] > 0) {
				
					foreach($rsLinks['data'] as &$arrLink) {
						//calc max
						$sqlLinkTrackingMax = "SELECT COUNT(*) as nb ";
						$sqlLinkTrackingMax.= "FROM mail_linktracking ";
						$sqlLinkTrackingMax.= "WHERE mail_linkid = '".$arrLink['id']."' AND mail_newslettersentid = '".$arrNewsletter['sentid']."' ";
						$rsLinkTrackingMax = $objDB->fetchItem($sqlLinkTrackingMax);
							
						$maxLinks = $rsLinkTrackingMax['data']['nb'];
						$maxLinkTrackingAll+= $maxLinks;
						
						if($maxLinks > 0) {

							$arrLinkTrackingStat = array();
							
							$sqlLinkTracking = "SELECT YEAR(dateclicked_first) AS y, MONTH(dateclicked_first) AS m,";
							$sqlLinkTracking.= "DAYOFMONTH(dateclicked_first) as d, COUNT(*) as nb ";
							$sqlLinkTracking.= "FROM mail_linktracking ";
							$sqlLinkTracking.= "WHERE mail_linkid = '".$arrLink['id']."' AND mail_newslettersentid = '".$arrNewsletter['sentid']."' ";
							$sqlLinkTracking.= "GROUP BY y, m, d ";
							$sqlLinkTracking.= "ORDER BY y desc, m desc, d desc";	
							
							$rsLinkTracking = $objDB->fetch($sqlLinkTracking);
							
							foreach ($rsLinkTracking['data'] as &$arrLinkTracking) {
								if ($arrLinkTracking['d'] < 10) {
									$arrLinkTracking['d'] = '0' . $arrLinkTracking['d'];
								}
								if ($arrLinkTracking['m'] < 10) {
									$arrLinkTracking['m'] = '0' . $arrLinkTracking['m'];
								}
								$arrLinkTrackingStat[] =  array(	'nb' => $arrLinkTracking['nb'],
																					'date' =>  $arrLinkTracking['d'] . "." .  $arrLinkTracking['m'] . "." . $arrLinkTracking['y'],
																					'width' => (500 / $maxLinks) * $arrLinkTracking['nb']);
							}
							$arrLinkStat[] = array(	'name' => $arrLink['name'] . " (".$maxLinks.")",
																			'lines' => $arrLinkTrackingStat);
						}
					}
				}
				
				$arrStats[] = array('headline' => $objText->getText('base_links') . " ($maxLinkTrackingAll)",
														'stats' => $arrLinkStat);
			
				$this->tpl->assign('arrStats',$arrStats);
			}
		} else {
		
			$arrWaiting = tuksiNewsletterStat::getWaiting();
			
			$this->tpl->assign('waiting',$arrWaiting);
			
			$arrSent = tuksiNewsletterStat::getSent();
			
			$this->tpl->assign('sent',$arrSent);
		}

		$returnHtml = parent::getHTML();
		return $returnHtml;
	}
	
	function saveData() {
		
	}
}
?>
