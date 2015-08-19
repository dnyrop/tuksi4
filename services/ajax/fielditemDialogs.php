<?php
include_once(dirname(__FILE__) . "/../../include/tuksi_init.php");

tuksiIni::setSystemType('backend');

$objTuksiUser = tuksiBackendUser::getInstance();
if(!$objTuksiUser->isLogged()){
	die('no access to script');
} 

$action = $_GET->getStr('action');
$relationid = $_GET->getStr('relationid');
$type = $_GET->getStr('type');
$tablename = $_GET->getStr('tablename');


$objDialog = new fieldItemDialog($tablename,$type,$relationid);

if (tuksiIni::$arrIni['setup']['charset'] != 'iso-8859-1')
	echo utf8_encode($objDialog->getHtml($action));
else
	echo $objDialog->getHtml($action);

class fieldItemDialog{
	
	private $tablename,$type,$relationid;
	
	function __construct($tablename,$type,$relationid){
		$this->tablename = $tablename;
		$this->type = $type;
		$this->relationid = $relationid;
		$this->tpl = new tuksiSmarty();		
	}
	
	function getHtml($action) {
		
		$html = "";
		
		switch ($action) {
			case 'arrangedialog':
				$html = $this->arrangeDialog();
				break;
			default:
				break;
		}
		return $html;
	}
	
	function arrangeDialog(){
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "SELECT * FROM cmsfielditem ";
		$sql.= "WHERE relationid = '".$this->relationid."' AND itemtype = '".$this->type."' AND tablename = '".$this->tablename."' ";
		$sql.= "ORDER BY seq";
		
		$arrItems = $objDB->fetch($sql);
		
		if($arrItems['ok'] && $arrItems['num_rows'] > 0) {
			$this->tpl->assign('items',$arrItems['data']);
		}
		
		return $this->tpl->fetch('ajax/arrangeItems.tpl');
	}
}
?>
