<?php
/**
 * Factory class for loading newsletter page
 *
 * @package tuksiNewsletter
 */
class tuksiNewsletter {
	
	public $conf,$id;
	static public $instance;
	
	public function __construct(){
		
		$this->id = $newsletterid;
		
		// Parser konfigurations filen til newsletter
		$this->conf = parse_ini_file(dirname(__FILE__) . "/../../configuration/newsletter.ini", true);   
	
	}

	static function getInstance($if_exist = false, $treeid = 0) {

//		tuksiIni::loadNewsletterConf();
		
		if (self::$instance) {
			return self::$instance;
		}

		// Do not make instance
		if ($if_exist) {
			return;
		}

		if (!$treeid) {
			$treeid = $_GET->getInt('treeid');
		}

		$objDB = tuksiDB::getInstance();
	
		$sql = "SELECT c.classname ";
		$sql.= "FROM cmstree t, cmscontrol c, pg_page_template p ";
		$sql.= "WHERE t.id = '{$treeid}' AND t.pg_page_templateid = p.id AND p.cmscontrolid = c.id";
		
		$arrReturn = $objDB->fetchItem($sql, array('expire' => 360, 'name' => 'Getting main control by treeid'));

		if ($arrReturn['num_rows'] > 0) {
			$classname = $arrReturn['data']['classname'];
			$objPage = new $classname($treeid);
			return $objPage;
		} else {
				return false;
		}

	}
} // End tuksiNewsletter
?>
