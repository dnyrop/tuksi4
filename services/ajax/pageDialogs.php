<?php
include_once(dirname(__FILE__) . "/../../include/tuksi_init.php");

tuksiIni::setSystemType('backend');

$arrUser = tuksiBackendUser::getUserInfo();

$treeid = $_GET->getStr('treeid');
$id = $_GET->getStr('id');
$action = $_GET->getStr('action');

$pDialog = new pageDialogs($treeid,$id);

if (tuksiIni::$arrIni['setup']['charset'] != 'iso-8859-1')
	echo utf8_encode($pDialog->getHtml($action));
else
	echo $pDialog->getHtml($action);

class pageDialogs {
	
	public $rootid;
	private $treeid;
	private $arrUser = array();
	private $getall = false;
	
	function __construct($treeid,$id){
		
		$this->treeid = $treeid;
		
		$objTuksiUser =  tuksiBackendUser::getInstance();
		if (!$objTuksiUser->isLogged()) {
			die('no access to script');
		} else {
			$this->arrUser = $objTuksiUser->getUserFromSession();
			$this->arrUser['usergroup'] = $objTuksiUser->getUserGroups($this->arrUser['id']);
		}
		
		if (($arrConf = tuksiConf::getPageConf($treeid)) === false) {
			if (!isset($this->arrUser['usergroup']) || empty($this->arrUser['usergroup'])) {
				die('no access to backend pages');
			}
		} else {
			$this->rootid = $arrConf['rootid'];
		}
		
		if (isset($this->arrUser['usergroup'][1])) {
			$arrConf = tuksiConf::getConf();
			$this->rootid = $arrConf['link']['cmsroot_treeid'];
			$this->getall = true;
		}

		$this->tpl = new tuksiSmarty();		
		$this->tpl->assign('id',$id);
		$this->tpl->assign('treeid',$treeid);
		
	}
	
	function getHtml($action){
		
		$html = "";
		
		$treeid = $_GET->getStr('treeid');
		$placement = $_GET->getStr('placement');
		
		switch ($action) {
			case 'checkparent':
				$html = $this->checkParent($treeid,$placement);
				break;
			case 'movepagedialog':
				$html = $this->getMovePageDialog();
				break;
			case 'movetabdialog':
				$html = $this->getMoveTabDialog();
				break;
			case 'copypagedialog':
				$html = $this->getCopyPageDialog();
				break;
			case 'addpagedialog':
				$html = $this->getAddPageDialog();
				break;
			case 'releasepagedialog':
				$html = $this->getReleasePageDialog();
				break;
					break;
			case 'arrangemodulesdialog':
				$html = $this->getArrangeModulesDialog();
				break;
			default:
				break;
		}
		return $html;
	}
	
	function getMovePageDialog(){
		$this->setPageSelect();
		return $this->tpl->fetch('ajax/movePageDialog.tpl');
	}

	function checkParent($treeid,$placement){
		
		if($this->getall == true) {
			return 1;
		} else {
		
			$objTree = tuksiTree::getInstance();
			
			if($objTree->checkParent($treeid,$placement)) {
				return 1;
			} else {
				return 0;	
			}
		}
	}
	
	function getMoveTabDialog(){
		$this->setPageSelect();
		$tabid = $_GET->getStr('tabid');
		$this->tpl->assign('tabid',$tabid);
		return $this->tpl->fetch('ajax/moveTabDialog.tpl');
	}
	
	function getCopyPageDialog(){
		$this->setPageSelect();
		return $this->tpl->fetch('ajax/copyPageDialog.tpl');
	}
	
	function getAddPageDialog(){
		$arrTree = $this->setPageSelect();
		
		$isin = false;
		
		foreach($arrTree as $arr) {
			if($arr['id'] == $this->treeid) {
				$isin = true;
				$checkparent = $this->checkParent($this->treeid,1);
				$this->tpl->assign('checkparent',$checkparent);
				continue;
			}
		}
		
		$this->tpl->assign('isin',true);
		
		return $this->tpl->fetch('ajax/addPageDialog.tpl');
	}
	
	function getReleasePageDialog(){
		return $this->tpl->fetch('ajax/releasePageDialog.tpl');
	}
	
	function getArrangeModulesDialog(){
		
		$areaid = $_GET->getStr('areaid');
		$tabid = $_GET->getStr('tabid');
		
		$objPageElements = new tuksiPageGeneratorElements($this->treeid,$tabid,$areaid);
		
		$arrData = $objPageElements->getElementsFromAreaForSelect();
		
		$this->tpl->assign('elements',$arrData);
		
		return $this->tpl->fetch('ajax/arrangeModulesDialog.tpl');
	}
	
	function setPageSelect(){
		
		$objPage = tuksiBackend::getInstance();
		$objPagegenerator = tuksiPageGenerator::getInstance();
		$objDB = tuksiDB::getInstance();		
		
		// check if treeid is eq to newsletter
		$sqlNewsletter = "SELECT id, cmssitelangid FROM cmstree WHERE id = {$this->treeid} AND cmscontextid = 3";
		$arrNewsletter = $objDB->fetchItem($sqlNewsletter);	
		
		if($this->getall) {
			$arrTree = $objPagegenerator->getTreeForSelect($this->rootid,"",0,array(),false);
		} else {
			if($arrNewsletter['ok'] && $arrNewsletter['num_rows']) {
                		$arrConf = tuksiConf::loadSite($arrNewsletter['data']['cmssitelangid']);

				$arrTree = $objPagegenerator->getTreeForSelect($arrConf['newsletter_treeid']);
			} else {
				$arrTree = $objPagegenerator->getTreeForSelect($this->rootid,$objPage->cmstext('frontpage'));
			}
		}
		$this->tpl->assign("pageSelect",$arrTree);	
		return $arrTree;
	}
}

?>
