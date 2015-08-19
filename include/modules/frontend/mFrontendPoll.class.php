<?

/**
 * mFrontendPoll 
 * 
 * @package tuksiFrontendModule 
 * @author Henrik Jochumsen <hjo@dwarf.dk> 
 */
class	mFrontendPoll extends mFrontendBase {

	//return the html for the module
	function __construct(&$objMod){

		parent::__construct($objMod);

		$this->tpl = new tuksiSmarty();

	}
	/**
	 * Henter HTML
	 */

	function getHTML() {
		
		$objDB = tuksiDb::getInstance();
		
		$pollId = $this->objMod->value1;
		$showResult = false;
		$maxWidth = 100;
		
		$arrPoll = $this->getPollInfo($pollId);
		
		if($_POST->getStr('pollSubmit_'.$pollId) && $_POST->getStr('question')) {
			$answer = $_POST->getInt('question');
			
			$sqlSave = "INSERT INTO cmspollvote ";
			$sqlSave.= "(cmspollid,cmspollanswerid,dateadded,ip) VALUES ('$pollId','$answer',now(),'{$_SERVER['REMOTE_ADDR']}')";
			$rsSave = $objDB->write($sqlSave);
			$showResult = true;
			
			setcookie("tuksi_poll_".$pollId,true,time()+360000000);
		}
		
		if(isset($_COOKIE['tuksi_poll_'.$pollId])) {
			$showResult = true;
		}
		
		if($showResult) {
			
			$sqlVotes = "SELECT *,count(v.id) as votes FROM cmspollanswer a ";
			$sqlVotes.= "LEFT JOIN cmspollvote v on (a.id = v.cmspollanswerid) ";
			$sqlVotes.= "WHERE a.cmspollid = '$pollId' ";
			$sqlVotes.= "GROUP BY a.id ORDER BY a.seq";
			$rsVotes = $objDB->fetch($sqlVotes);
			
			if($rsVotes['ok'] && $rsVotes['num_rows'] > 0) {
				$all= 0;
				
				foreach ($rsVotes['data'] as $q) {
					$all+= $q['votes'];
				}
				$arrPoll['votes'] = $all;
				foreach ($rsVotes['data'] as $q) {
					$q['percent'] = $q['votes'] / $all;
					$q['width'] = round($q['percent'] * $maxWidth,0);
					$q['percent'] = round($q['percent'] * 100,0);
					$arrVotes[] = $q;
				}
				$this->tpl->assign('votes',$arrVotes);
			}
			
		} else {
			
			//getPoll
			$sqlPoll = "SELECT q.* FROM cmspollanswer q ";
			$sqlPoll.= "WHERE q.cmspollid = '$pollId' ";
			$sqlPoll.= "ORDER BY q.seq";
			$rsPoll = $objDB->fetch($sqlPoll);
			
			if($rsPoll['ok'] && $rsPoll['num_rows'] > 0) {
				$this->tpl->assign('poll',$rsPoll['data']);
			}
		}
		$this->tpl->assign('pollInfo',$arrPoll);
		$this->tpl->assign('pollid',$pollId);
		$this->tpl->assign('showResult',$showResult);
		
		return parent::getHTML();
	}
	
	private function getPollInfo($id){
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "SELECT * FROM cmspoll WHERE id = '$id'";
		$rs= $objDB->fetchItem($sql);
		return $rs['data'];
	}
}
?>
