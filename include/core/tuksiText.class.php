<?php

class tuksiText {

	static private $arrInstance = array();

	public $tpl;
	private $isGlobal = false;
	private $lang,$website;

	public $arrText = array();
	
	static public $arrTextGlobal = array();

	function __construct($tpl) {
		
		$this->tpl = $tpl;
		if($this->tpl == 'global' || $this->tpl == 'table') {
			$this->isGlobal = true;
		}
	}
	
	public function setLanguage($langCode) {
		$this->lang = $langCode;
	}	
	
	public function setWebsite($website) {
		$this->website = $website;
	}

	static function getInstance($tpl = 'global') {
		
		$arrConf = tuksiConf::getConf();
		$isNewsletter = false;
		if ($arrConf['setup']['system'] == 'newsletter') {
			$tpl = 'global';
			$arrConf['setup']['system'] = 'frontend';
			$isNewsletter = true;
		}
		
		if (!isset(self::$arrInstance[$tpl])){
			self::$arrInstance[$tpl] = new tuksiText($tpl);
		}
		
		if ($arrConf['setup']['system'] == 'frontend') {
			if ($isNewsletter) {
				$objPage = tuksiNewsletter::getInstance();
				$arrPageConf = tuksiConf::getPageConf($objPage->arrPage['id']);
				self::$arrInstance[$tpl]->setLanguage($arrPageConf['lang']);
			} else {
			self::$arrInstance[$tpl]->setLanguage($arrConf['site']['lang']);
			}
		} else {
			self::$arrInstance[$tpl]->setLanguage($arrConf['setup']['admin_lang']);
		}
		self::$arrInstance[$tpl]->setWebsite($arrConf['setup']['system']);

		return self::$arrInstance[$tpl];
	}

	function setGlobalTexts() {

		$objDB = tuksiDB::getInstance();
		
		// Tjekker om globale tekster er loadet
		if (count(self::$arrTextGlobal) == 0) {
			$sql = "SELECT name, value_{$this->lang} AS value,id FROM cmstemplatetext WHERE isglobal = 1 AND website = '".$this->website."'";
			$arrReturn = $objDB->fetch($sql, array('expire' => 360));

			foreach ($arrReturn['data'] as &$arrData) {
				self::$arrTextGlobal[$arrData['name']] = array(	'id' => $arrData['id'],
																												'value' => $this->parseValue($arrData['value']));
			}
		}

		// Tilføjer globale tekster til skabelon tekster
		$this->arrText = self::$arrTextGlobal;

		if(!$this->isGlobal) {
		
			// Henter skabelon tekster
			$sql = "SELECT tt.name, tt.value_{$this->lang} AS value,tt.id ";
			$sql.= "FROM cmstemplatetext tt, cmstemplate t ";
			$sql.= "WHERE tt.cmstemplateid = t.id AND t.name = '{$this->tpl}' AND t.website = '".$this->website."'";
	
			$arrReturn = $objDB->fetch($sql, array('expire' => 360));

			foreach ($arrReturn['data'] as &$arrData) {
				$this->arrText[$arrData['name']] = array(	'id' => $arrData['id'],
																									'value' => $this->parseValue($arrData['value']));
			}
		}
	}
	
	function parseValue($value) {
		$value = str_replace("##HOST##", 'http://' . $_SERVER['SERVER_NAME'], $value);
		return $value;
	}

	
	function getText($token, $ignoreEmpty = false) {
		
		if(strlen($token) > 0) {
		
			if (count($this->arrText) == 0) {
				$this->setGlobalTexts();
			}
		
			if (array_key_exists($token, $this->arrText)) {
				if(strlen($this->arrText[$token]['value']) > 0)
					return $this->arrText[$token]['value'];
				else {
					//dirext edit url disabled as token could be inside a link
					return $ignoreEmpty ? "" : "[$token is empty]";
				}
			
			} else {
			
				$objDb = tuksiDB::getInstance();
				
				// Text findes ikke, lad os tilføje den..
				$sql = "SELECT id FROM cmstemplate t ";
				$sql.= "WHERE name = '{$this->tpl}' AND website = '".$this->website."'";
				$arrReturn = $objDb->fetch($sql);
				
				if ($arrReturn['num_rows'] > 0) {
					$cmstemplateid = $arrReturn['data'][0]['id'];
				} else {
					// Template ikke tilføjet. Tilføj ny række
					$sql = "INSERT INTO cmstemplate (name,website) VALUES('{$this->tpl}','".$this->website."')";
					$arrRes = $objDb->write($sql);
					
					$cmstemplateid = $arrRes['insert_id'];
				}
				
				$sql = "INSERT INTO cmstemplatetext (cmstemplateid, name, website,isglobal) VALUES ";
				$sql.= "('$cmstemplateid', '{$token}','".$this->website."','".$this->isGlobal."')";
				$rs = $objDb->write($sql);
				
				$this->arrText[$token] = array(	'id' => $rs['inserted_id'],	'value' => "[token: $token inserted]"); 
				
				return "[$token inserted] ";
			}
		} else {
			return "[no token provided] ";
		}
	}
}


?>
