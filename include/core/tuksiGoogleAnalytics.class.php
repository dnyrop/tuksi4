<?

/**
 * Enter description here...
 *
 * @todo php doc
 * @package tuksiBase
 */

class tuksiGoogleAnalytics {

	static $instance;
	private $accountId;
	
	function __contruct() {
		self::$instance = $this;
	}

	static function getInstance() {
		if (self::$instance) {
			return self::$instance;
		} else {
			return new tuksiGoogleAnalytics();
		}
	}

	public function getHTML() {	

		$arrConf = tuksiConf::getConf();
		$this->setLocal = $arrConf['site']['ga_domain'];
		
		$tplGoogle = new tuksiSmarty();
		$tplGoogle->assign('accountid', $arrConf['site']['gacode']);
		$tplGoogle->assign('setLocal', $arrConf['site']['ga_locale']);
		$tplGoogle->assign('domain', $arrConf['site']['ga_domain']);
		if (strlen($arrConf['site']['urlpart_prefix']) && $arrConf['site']['ga_replace']) {
			$tplGoogle->assign('replace', preg_replace('%^/' . preg_quote($arrConf['site']['urlpart_prefix'], '%') . '%i', '', $_SERVER['REQUEST_URI']));
		} // if

		return $tplGoogle->fetch('googleAnalytics.tpl');
	}

	function setAccountID($accountId) {
		$this->accountId = $accountId;
	}
} // End class

?>
