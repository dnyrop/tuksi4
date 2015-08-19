<?

class tuksiCleanup {
	
	function __construct(){}
	
	function getNBModules(){
		
		$objDB = tuksiDB::getInstance();
		
		$sqlPC = "SELECT pc.id, t.id AS tid ";
		$sqlPC.= "FROM pg_content pc ";
		$sqlPC.= "LEFT JOIN cmstree t ON pc.cmstreeid = t.id";
		$rsPC = $objDB->fetch($sqlPC);
		
		$pccountclean = 0;
		
		foreach($rsPC['data'] as $arrPC) {
			if(!$arrPC['tid']){
				$pccountclean++;
			}
		}
		return $pccountclean;
	}
	
	function getNBTree(){
		
		$objDB = tuksiDB::getInstance();
		
		$sqlTree = "SELECT t1.id, t2.id AS parent ";
		$sqlTree.= "FROM cmstree t1 ";
		$sqlTree.= "LEFT JOIN cmstree t2 ON t1.parentid = t2.id ";
		//$sqlTree.= "WHERE t1.id <> 107 order by t1.id";
    $arrRsTree = $objDB->fetch($sqlTree);

    $tcountclean = 0;

    if ($arrRsTree['ok'] && $arrRsTree['num_rows']) {
      foreach($arrRsTree['data'] as $arrTree){
        if (!$arrTree['parent']) {
          $tcountclean++;
        }
      }
    }
		return $tcountclean;
	}
	
	function getNBTab(){
		
		$objDB = tuksiDB::getInstance();
		
		$sqlTT = "SELECT tt.id, t.id AS tid ";
		$sqlTT.= "FROM cmstreetab tt ";
		$sqlTT.= "LEFT JOIN cmstree t ON tt.cmstreeid = t.id";
		$rsTT = $objDB->fetch($sqlTT);

		$ttcountclean = 0;
		
		foreach ($rsTT['data'] as $arrTab){
			if(!$arrTab['tid']){
				$ttcountclean++;
			}
		}
		return $ttcountclean;
	}
	
	function getNBElement(){
		
		$objDB = tuksiDB::getInstance();
		
		$sqlCTE = "SELECT p.id, t.id AS tid ";
		$sqlCTE.= "FROM cmstreeelement p ";
		$sqlCTE.= "LEFT JOIN cmstree t ON p.cmstreeid = t.id";
		$rsCTE = $objDB->fetch($sqlCTE);
		
		$ctecountclean = 0;
		
		foreach ($rsCTE['data'] as $arrCTE){
			if (!$arrCTE['tid']){
				$ctecountclean++;
			}
		}
		return $ctecountclean;
	}
	
	function getnbItem(){
		
		$objDB = tuksiDB::getInstance();
		
		$sqlFI = "SELECT id, tablename FROM cmsfielditem WHERE itemtype='table'"; 
		$rsFI=$objDB->query($sqlFI);
		$fiCount=mysql_num_rows($rsFI);
		$fiCountClean=0;
		while($objFI=mysql_fetch_object($rsFI)){
			if(!tableExists($objFI->tablename)) {
				$fiCountClean++;
			}
		}
		
	}
	
}

?>
