<?php

class tuksiNewsletterStat {
	
	
	static function getWaiting($numrows = 0){
		
		$objPage = tuksiBackend::getInstance();
		$objDB = tuksiDB::getInstance();
		
		$arrWaiting = array();
		
		$sqlWaiting = "SELECT ms.*, date_format(datetosend, '%d.%m.%y %H:%i') AS dato, t.name AS newslettername, ml.name AS listname ";
		$sqlWaiting.= "FROM mail_newslettersent ms ";
		$sqlWaiting.= "INNER JOIN mail_emaillist ml ON ms.mail_emaillistid = ml.id ";
		$sqlWaiting.= "INNER JOIN cmstree t ON ms.mail_newsletterid = t.id ";
		$sqlWaiting.= "WHERE ms.datetosend > NOW() AND t.isdeleted = '0' AND t.cmssitelangid = '{$objPage->arrTree['cmssitelangid']}' ";
		$sqlWaiting.= "ORDER BY ms.datesent ASC ";

		if ($numrows > 0) {
			$sqlWaiting.= "LIMIT $numrows";
		}
		
		$rsWaiting = $objDB->fetch($sqlWaiting);

		$arrWaiting = array();
		foreach ($rsWaiting['data'] as &$arrNewsletter) {
			$arrWaiting[] = array(
				'id' => $arrNewsletter['id'],
				'name' => $arrNewsletter['name'],
				'url' => $url,
				'newsletter' => $arrNewsletter['newslettername'],
				'emaillist' => $arrNewsletter['listname'],
				'dato' => $arrNewsletter['dato'],
				'sentto' => $arrNewsletter['sentto']
			);
		}
		
		return $arrWaiting;
	}

	static function deleteStats($newsletterId) {
			$objDB = tuksiDB::getInstance();

			$sqlDeleteMail = "DELETE FROM mail_newslettersent WHERE id = '{$newsletterId}'";

			$objDB->write($sqlDeleteMail);

			$sql = "SELECT id FROM mail_tracking WHERE mail_newslettersentid = '{$newsletterId}'";

			$arrTracking = $objDB->fetch($sql);
			if ($arrTracking['ok'] && $arrTracking['num_rows']) {
				foreach ($arrTracking['data'] as &$arrTData) {
					$sqlDeleteTracking = "DELETE FROM mail_trackingelement WHERE mail_trackingid= '{$arrTData['id']}'";
					$objDB->write($sqlDeleteTracking);
					$sqlDeleteTracking = "DELETE FROM mail_linktracking WHERE mail_trackingid= '{$arrTData['id']}'";
					$objDB->write($sqlDeleteTracking);
				}
			}
			
			$sqlDeleteTracking = "DELETE FROM mail_tracking WHERE mail_newslettersentid = '{$newsletterId}'";
	}
	
	static function getSent($numrows = 0){
		
		$objPage = tuksiBackend::getInstance();
		$objDB = tuksiDB::getInstance();

		$sqlSent = "SELECT ms.*, t.name AS newsletter_name, date_format(datesent, '%d.%m.%y %H:%i') AS dato, me.name AS email_list ";
		$sqlSent.= "FROM mail_newslettersent ms ";
		$sqlSent.= "INNER JOIN mail_emaillist me ON ms.mail_emaillistid = me.id ";
		$sqlSent.= "INNER JOIN cmstree t ON ms.mail_newsletterid = t.id ";
		$sqlSent.= "WHERE ms.datesent < NOW() AND t.isdeleted = '0' AND t.cmssitelangid = '{$objPage->arrTree['cmssitelangid']}' ";
		$sqlSent.= "ORDER BY ms.datesent DESC ";

		if ($numrows > 0) {
			$sqlSent.= "LIMIT $numrows";
		}
		
		$rsSent = $objDB->fetch($sqlSent);
		
		$arrSent = array();
		if ($rsSent['ok'] && $rsSent['num_rows']) {
			foreach ($rsSent['data'] as &$arrNewsletter) {
				$sqlTrack = "SELECT COUNT(id) AS sentto, SUM(isviewed) AS viewed FROM mail_tracking WHERE mail_newslettersentid = '{$arrNewsletter['id']}'";
				$rsTrack = $objDB->fetchItem($sqlTrack);

				$sentto = 0;
				$viewed = 0;
				if ($rsTrack['ok'] && $rsTrack['num_rows']) {
					$sentto = (int) $rsTrack['data']['sentto'];
					$viewed = (int) $rsTrack['data']['viewed'];
				}
				
				$arrSent[] = array(
					'name' => $arrNewsletter['name'],
					'id' => $arrNewsletter['id'],
					'newsletter' => $arrNewsletter['newsletter_name'],
					'emaillist' => $arrNewsletter['email_list'],
					'dato' => $arrNewsletter['dato'],
					'sentto' => $sentto,
					'viewed' => $viewed,
					'url' => tuksiTools::getBackendUrl($objPage->treeid, $objPage->tabid) . "&newsletterid=" . $arrNewsletter['id']
				);
			}
		}
		
		return $arrSent;
	}
	
}
?>
