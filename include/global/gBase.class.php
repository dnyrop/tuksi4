<?php

	/**
	 * Base global class
	 *
	 */
	class gBase {
		
		/**
		 * Current site language
		 *
		 * @var string
		 */
		protected $lang;
		
		/**
		 * Current site language ID
		 *
		 * @var integer
		 */
		protected $langId;	
			
		/**
		 * Current site ID
		 *
		 * @var integer
		 */
		protected $siteId;
		
		/**
		 * Current site configuration
		 *
		 * @var array
		 */
		protected $arrConf;
		
		/**
		 * Current site locale
		 *
		 * @var string
		 */
		protected $locale;
		
		/**
		 * Current table extension (live/dev)
		 *
		 * @var string
		 */
		protected $tableExt;
		
		/**
		 * Tuksi MySQL Database object
		 *
		 * @var object
		 */
		protected $objDB;
		
		/**
		 * Constructor
		 * 
		 * @return object gBase
		 */
		public function __construct() {
			// Store DB instance
			$this->objDB = tuksiDB::getInstance();
			
			// Store conf array
			$this->arrConf = tuksiConf::getConf();
			
			// Store language
			$this->lang = $this->arrConf['site']['lang'];
			
			// Set language ID
			$this->langId = $this->arrConf['site']['cmssitelangid'];
			
			// Set site ID
			$this->siteId = $this->arrConf['site']['id'];
			
			// Set locale
			$this->setLocale();
			
			// Set table extension
			$this->tableExt = $this->arrConf['setup']['tableext'];
			
		} // function __construct
		
		/**
		 * Sets current site locale
		 *
		 */
		private function setLocale() {
			switch ($this->lang) {
				case 'en': // Engelsk
					$this->locale = 'en_GB';
					break;
					
				case 'se': // Svensk
					$this->locale = 'sv_SE';
					break;
					
				case 'no': // Norsk
					$this->locale = 'no_NO';
					break;
					
				case 'is': // Islandsk
					$this->locale = 'is_IS';
					break;
					
				case 'de': // Tysk
					$this->locale = 'de_DE';
					break;
					
				case 'nl': // Hollandsk
					$this->locale = 'nl_NL';
					break;
					
				case 'es': // Spansk
					$this->locale = 'es_ES';
					break;
					
				case 'lv': // Lettisk
					$this->locale = 'lv_LV';
					break;
					
				case 'lt': // Litauisk
					$this->locale = 'lt_LT';
					break;
					
				case 'ru': // Russisk
					$this->locale = 'ru_RU';
					break;

				case 'gr': // Græsk
					$this->locale = 'gr_GR';
					break;

				case 'it': // Italiensk
					$this->locale = 'it_IT';
					break;

				case 'ie': // Irsk
					$this->locale = 'en_IE';
					break;

				case 'pl': // Polsk
					$this->locale = 'pl_PL';
					break;

				case 'no': // Norsk
					$this->locale = 'no_NO';
					break;

				case 'fi': // Finsk
					$this->locale = 'fi_FI';
					break;	
					
				case 'jp': // Japansk
					$this->locale = 'ja_JP';
					break;	
					
				case 'pt': // Portugisisk
					$this->locale = 'pt_PT';
					break;	
					
				default: // Dansk
					$this->locale = 'da_DK';
					break;
			} // switch
		} // function setLocale
		
		/**
		 * Cleans a string converting &nbsp; in normal spaces, trims and slims whitespaces
		 *
		 * @param string $str
		 * @return string $str
		 */
		public static function strClean($str) {
			// Sanity check
			if (is_string($str)) {
				$str = str_replace('&nbsp;', ' ', $str);
				$str = preg_replace('/\s+/', ' ', trim($str));
			} // if
			
			return $str;
		} // function strClean

		/**
		 * Returns the real IP
		 * 
		 * @return string $ip
		 */
		public static function getIP() {
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			} // if

			return $ip;
		} // function getIP
		
		/**
		 * Generates pagination and slices data array
		 *
		 * @param array $arrData Data to slice
		 * @param array $arrParams Option parameters eg. array('page' => 2, 'step' => 10)
		 * @param array $arrAddParams Additional parameters to be added to the GET string
		 * @return array array('data' => $arrSlice, 'pager' => $arrPager). If no paging 'pager' => false.
		 */
		public static function getPagination($intAmount, &$arrParams, $arrAddParams = array()) {			
			// Sanity check
			if (!(is_int($intAmount) && is_array($arrParams) && is_array($arrAddParams))) {
				array('pager' => false);
			} // if
			
			// Check params
			if (!isset($arrParams['page']) || !is_numeric($arrParams['page']) || $arrParams['page'] < 1) {
				$arrParams['page'] = 1;
			} // if
			
			if (!isset($arrParams['step']) || !is_numeric($arrParams['step']) || $arrParams['step'] < 1) {
				$arrParams['step'] = 10;
			} // if
			
			if (!isset($arrParams['display']) || !is_numeric($arrParams['display']) || $arrParams['display'] < 1) {
				$arrParams['display'] = 5;
			} // if
			
			// Find total pages count
			$total_pages = ceil($intAmount / $arrParams['step']);
			
			// If only 1 page exists, don't show pagination
			if ($total_pages <= 1) {
				return array('pager' => false);
			} // if
			
			// Ensure that current page doesn't exceed limit
			if ($arrParams['page'] > $total_pages) {
				$arrParams['page'] = $total_pages;
			} // if
			
			// Determine range
			$start_range = $arrParams['page'] - 2;
			if ($start_range < 1 || $start_range == 3) {
				$start_range = 1;
			} // if
			$end_range = $arrParams['page'] + 2;
			if ($end_range > $total_pages || $end_range == $total_pages - 2) {
				$end_range = $total_pages;
			} // if
			
			// Fill range
			$arrRange = range($start_range, $end_range);
			
			// Inital dots state
			$dots_prev = false;
			$dots_next = false;
			
			// Initial pagination array
			$arrPager = array('links' => array());
			
			// Assign prev
			if ($arrParams['page'] > 1) {
				$arrPager['prev'] = array('get' => self::getPagLink($arrParams['page'] - 1, $arrAddParams));
			} // if
			
			// Assign next
			if ($arrParams['page'] < $total_pages) {
				$arrPager['next'] = array('get' => self::getPagLink($arrParams['page'] + 1, $arrAddParams));
			} // if
			
			for ($i = 1; $i <= $total_pages; $i++) {
				// Add first, last and range pages
				if ($total_pages <= $arrParams['display'] || $i == 1 || $i == $total_pages || in_array($i, $arrRange)) {
					if ($i == $arrParams['page']) {
						$arrPager['links'][] = array('num' => $i, 'get' => self::getPagLink($i, $arrAddParams), 'active' => true);
					} else {
						$arrPager['links'][] = array('num' => $i, 'get' => self::getPagLink($i, $arrAddParams));
					} // if
				} else {
					// Render spacing dots
					if ($i < $arrParams['page'] && !$dots_prev) {
						$dots_prev = true;
						$arrPager['links'][] = array('num' => false);
					} elseif ($i > $arrParams['page'] && !$dots_next) {
						$dots_next = true;
						$arrPager['links'][] = array('num' => false);
					} // if
				} // if
			} // for

			return array('pager' => $arrPager);
		} // function getPagination
		
		/**
		 * Formats GET parameters eg. ?page=3&q=dwarf
		 *
		 * @param int $page
		 * @param array $arrAddParams Additional parameters
		 * @return string $strGet
		 */
		public static function getPagLink($page, $arrAddParams) {
			$strGet = '?page=' . $page;
			
			// If $arrAddParams isn't empty
			if (count($arrAddParams)) {
				$strGet .= '&' . join('&', $arrAddParams);
			} // if
			
			return $strGet;
		} // function getPagLink
		
	} // class gBase
	
?>
