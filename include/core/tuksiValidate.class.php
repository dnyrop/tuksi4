<?php

define('ALL', 0);
define('INT', 1);
define('STR', 2);
define('CUSTOM', 3);
define('MD5', 4);

define('LOW', 1);
define('MEDIUM', 2);
define('STRONG', 3);

class tuksiValidate {
 
	/**
	 * Sikring af værdi.
	 *
	 * @param mixed $value Værdi der skal sikres.
	 * @param int $valuetype Værdi type
	 * @param array $arr Ved CUSTOM, laves array med tilladte værdier.
	 * @return mixed
	 */
	static function secVal($value, $valuetype, $arrFilter = array()) {
		
		switch ($valuetype) {
			case ALL : break;
			case INT : if (!self::isINT($value)) 
							unset($value);
						break;
			case STR : if (!self::isSTR($value)) 
							unset($value);
						break;
			case CUSTOM : if (!self::secFilterVal($value, $arrFilter))
							unset($value); 
						break;
			case MD5 : if (!self::isMD5($value))
							unset($value);
						break;	
			default : unset($value);
		}
		
		if (isset($value)) {
		return $value;
		} else {
			return null;
		}
	}
	
	/**
	 * Validering og sikring af SQL værdi.
	 *
	 * @param mixed $value
	 * @param int $valuetype
	 * @return mixed
	 */
	static function secField($value, $valuetype = 0, $postvalue = true) {
		
		$objDB = tuksiDB::getInstance();
		
		$value = self::secVal($value, $valuetype);
		
		if (get_magic_quotes_gpc() && $postvalue)
			$value = stripslashes($value);
		$arrDbh = $objDB->getDBH();
		$value = mysql_real_escape_string($value, $arrDbh['read']);
		
		return $value;
	}

	/**
	 * Valider og sikker mysql værdier fra GET eller POST
	 *
	 * @param mixed $arrValues
	 * @param mixed $arrSecValues
	 * @return array 
	 */
	static function secSqlval($values,$secValues){

		if (is_array($values)) {
			$arrParsed = array();
			if (is_array($secValues)) {
				foreach ($secValues as $arrSet) {
					if (array_key_exists($arrSet[0],$values)) {
						if (isset($arrSet[1])) {
							$type = $arrSet[1];
						}	else {
							$type = ALL;
						}
						$arrParsed[$arrSet[0]] = self::secField($values[$arrSet[0]], $type);
					}
				}
			} elseif(strlen($secValues) > 0) {
				if(isset($values[$secValues])){
					return secField($values[$secValues],ALL);
				}
			}
		}
	}
	
	/**
	 * Validering af værdi, hvor kan tilladte værdier gælder.
	 *
	 * @param mixed $value
	 * @param array $arrFilter
	 * @return bool
	 */
	static function secFilterVal($value, $arrFilter) {
		if (is_array($arrFilter)) {
			if (in_array($value, $arrFilter))
				return true;
			else
				return false;
		} else { 
			return false;
		}
	}
	
	/**
	 * Validering af int, is_int php funktion. [0-9].
	 *
	 * @param int $value
	 * @return bool
	 */
	static function isINT($value) {
		return is_numeric($value);
	}
	
	/**
	 * Validering af string.
	 *
	 * @param string $value
	 * @return bool
	 */
	static function isSTR($value, $lan = 256) {
		if (is_string($value) && strlen($value) < $lan) 
			return true;
		else 
			return false;
	}
	
	/**
	 * Valider at et filenavn er OK.
	 *
	 * @param string $value skal være string og MAX 64 tegn.
	 * @return bool
	 */
	static function isFileName($value) {
		if (self::isSTR($value, 64)) {
			if (strpos($value, "..") === true)
				return false;
			$value_tmp = str_replace("%", '_', rawurlencode($value));
			if ($value != $value_tmp)
				return false;
				
		} else
			return false;
		
		return true;
	}
	
	/**
	 * Validering af Md5 string. [a-zA-Z0-9]+
	 *
	 * @param string $value
	 * @return bool
	 */
	static function isMD5($value) {
		if (!preg_match("/^[a-zA-Z0-9]+$/", $value, $m)) 
			return false;
		else 	
			return true;		
	}
	
	/**
	 * Tjek om et password er godt nok
	 *
	 * @param string $password
	 * @param int $level 0 = low, 1 = medium, 2 = strong.
	 * @return array Array med fejl som tekst. count() == 0 betyder ingen fejl.
	 */
	static function checkPassword($password, $level = 0) {
		
		$arrErrors = array();
		// Skal være mindst 5 tegn
		if (strlen($password) < 5) {		
			$arrErrors[] = "Skal have mindst 5 tegn.";			
		}
		
		// Der skal være store bogstaver
		if (!preg_match("/[a-z]/", $password)) {
			$arrErrors[] = "Skal have mindst et lille bogstav.";
		}
		
		// Der skal være store bogstaver
		//if (!preg_match("/[A-Z]/", $password)) {
		//	$arrErrors[] = "Skal have mindst et stort bogstav.";
		//}
				
		// Der skal være tal
		//if (!preg_match("/[0-9]/", $password)) {
		//	$arrErrors[] = "Skal have mindst et tal.";
		//}
		
		// Level 1+
		if ($level > LOW) {
			// Skal være mindst 8tegn
			if (strlen($password) < 8) {
				$arrErrors[] = "Skal have mindst 8 tegn.";
			}
			// Der skal være tal
			if (!preg_match("/[0-9]/", $password)) {
				$arrErrors[] = "Skal have mindst et tal.";
			}
		}
		
		// Level 2+
		if ($level > MEDIUM) {
			// Skal være mindst et af følgende tegn. [,.-_'/\$#@]
			$chars = ",.-_'/\$#@{[]}+?&!";
			if (!preg_match("/[$chars]", $password)) {
				$arrErrors[] = "Skal have mindst et af disse tegn [$chars].";
			}
		}
		
		if(count($arrErrors) == 0) {
			return true;
		} else {
			return $arrErrors;
		}
	}
	
	 /** 
	 * Funktion til at tjekke om en værdi er tal uden at få warning i strict PHP
	 *
	 * @param array $arr tjek værdi i dette array.
	 * @param string $varname Key skal skal tjekkes i array.
	 * @return bool Returnere om værdi er ok.
	 */
	static function isNum($arr, $varname = "") {
		if (is_array($arr) && $varname) {
	
			if (array_key_exists($varname,$arr) && !empty($arr[$varname]) && is_numeric($arr[$varname]))
				return true;
			else
				return false;
		} elseif ($arr && !$varname) {
			
			if (!empty($arr) && is_numeric($arr))
				return true;
			else
				return false;
		}
	} // End funktion isNum()
	
  /** 
	 * Funktion til at tjekke om en værdi er tekst uden at få warning i strict PHP
	 *
	 * @param array $arr tjek værdi i dette array.
	 * @param string $varname Key skal skal tjekkes i array.
	 * @return bool Returnere om værdi er ok.
	 */
	static function isText($arr, $varname) {
		if (is_array($arr) && $varname) {
	
			if (array_key_exists($varname,$arr) && !empty($arr[$varname]))
				return true;
			else
				return false;
		}
	} // End isText();
	
	/**
	 * isEmail validates emails
	 * 
	 * @param string $email 
	 * @param int $simpel 
	 * @access public
	 * @return boolean
	 */
	static function isEmail($email, $simpel = 0) {
		$bValid = false;
		// Check if email is string and contains @
		if (is_string($email) && strpos($email, '@')) {
			list ($local, $domain) = explode("@", $email);
			
			// Patterns for validation
			$pattern_local = '/^([0-9a-z]*([-|_]?[0-9a-z]+)*)(([-|_]?)\.([-|_]?)[0-9a-z]*([-|_]?[0-9a-z]+)+)*([-|_]?)$/i';
			$pattern_domain = '/^([0-9a-z]+([-]?[0-9a-z]+)*)(([-]?)\.([-]?)[0-9a-z]*([-]?[0-9a-z]+)+)*\.[a-z]{2,4}$/i';
			$match_local = preg_match($pattern_local, $local);
			$match_domain = preg_match($pattern_domain, $domain);
			
			// If email strings validate
			if ($match_local && $match_domain) {
				// If email validation is simple or dns checks
				if ($simpel || checkdnsrr($domain)) {
					$bValid = true;
				} // if
			} // if
		} // if
		return $bValid;
	} // function isEmail

	/**
	 * isPhone validates phone numbers
	 * Valid: +81 82-486 1234
	 * 
	 * @param string $value 
	 * @access public
	 * @return bool
	 */
	static function isPhone($value) {
		return preg_match('/^\+?([0-9]+[ -]?)+[0-9]+$/', $value);
	} // function isPhone
	
	static function validate($arr) {
		$arrErr = array();

		foreach ($arr as $key => &$data) {
			if (!isset($data['type'])) $data['type'] = 'none';
			$bValid = (isset($data['empty']) && $data['empty']) || !empty($data['value']);

			// Only check if value isn't empty or empty values are allowed
			if ($bValid) {
				switch ($data['type']) {
					case 'str':
						$bValid = isset($data['length']) ? self::isSTR($data['value'], $data['length']) : self::isSTR($data['value']);
						break;

					case 'int':
						$bValid = self::isINT($data['value']);
						break;

					case 'filename':
						$bValid = self::isFileName($data['value']);
						break;

					case 'md5':
						$bValid = self::isMD5($data['value']);
						break;

					case 'array':
						$bValid = is_array($data['value']);
						break;

					case 'email':
						$bValid = isset($data['simple']) ? self::isEmail($data['value'], $data['simple']) : self::isEmail($data['value']);
						break;

					case 'phone':
						$bValid = self::isPhone($data['value']);
						break;
				} // switch
			} // if

			if (!$bValid) {
				$arrErr[$key] = true;
			} // if
		} // foreach
		
		return $arrErr;
	} // function validate
}

?>
