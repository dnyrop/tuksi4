<?php

/**
 * Enter description here...
 *
 * @package tuksiBase
 */
class tuksiTools {

	static function strip_ext($name) {
		$ext = strrchr($name, '.');
		if($ext !== false) {
			$name = substr($name, 0, -strlen($ext));
		}
		return $name;
	}

	/**
	 * Encodes a Windows-1252 string to a chosen encoding (default 'UTF-8')
	 * 
	 * @param string $str 
	 * @param string $encoding 
	 * @access public
	 * @return string
	 */
	static function encode($str, $encoding = 'UTF-8') {
		return mb_convert_encoding($str, $encoding, 'Windows-1252');
	} // function encode
	
	/**
	 * Decodes a string with a chosen encoding (default 'UTF-8') to Windows-1252
	 * 
	 * @param string $str 
	 * @param string $encoding 
	 * @access public
	 * @return string
	 */
	static function decode($str, $encoding = 'UTF-8') {
		return mb_convert_encoding($str, 'Windows-1252', $encoding);
	} // function encode

	/** 
	 * Funktion til at klargøre streng til url.
	 *
	 * @param string $name Streng skal skal konverteres.
	 * @return string url klar streng.
	 */	
	static function fixname($name) {
		// Decode all entities
		$name = html_entity_decode($name);
		
		// Map extended latin before converting to entities
		// Map macron
		$name = str_replace("&#256;", "a", $name);
		$name = str_replace("&#257;", "a", $name);
		$name = str_replace("&#274;", "e", $name);
		$name = str_replace("&#275;", "e", $name);
		$name = str_replace("&#298;", "i", $name);
		$name = str_replace("&#299;", "i", $name);
		$name = str_replace("&#332;", "o", $name);
		$name = str_replace("&#333;", "o", $name);
		$name = str_replace("&#362;", "u", $name);
		$name = str_replace("&#363;", "u", $name);
		
		// Map breve
		$name = str_replace("&#258;", "a", $name);
		$name = str_replace("&#259;", "a", $name);
		$name = str_replace("&#276;", "e", $name);
		$name = str_replace("&#277;", "e", $name);
		$name = str_replace("&#286;", "g", $name);
		$name = str_replace("&#287;", "g", $name);
		$name = str_replace("&#300;", "i", $name);
		$name = str_replace("&#301;", "i", $name);
		$name = str_replace("&#334;", "o", $name);
		$name = str_replace("&#335;", "o", $name);
		$name = str_replace("&#364;", "u", $name);
		$name = str_replace("&#365;", "u", $name);
		
		// Map ogonek
		$name = str_replace("&#260;", "a", $name);
		$name = str_replace("&#261;", "a", $name);
		$name = str_replace("&#280;", "e", $name);
		$name = str_replace("&#281;", "e", $name);
		$name = str_replace("&#302;", "i", $name);
		$name = str_replace("&#303;", "i", $name);
		$name = str_replace("&#370;", "u", $name);
		$name = str_replace("&#371;", "u", $name);
		
		// Map acute
		$name = str_replace("&#262;", "c", $name);
		$name = str_replace("&#263;", "c", $name);
		$name = str_replace("&#313;", "l", $name);
		$name = str_replace("&#314;", "l", $name);
		$name = str_replace("&#323;", "n", $name);
		$name = str_replace("&#324;", "n", $name);
		$name = str_replace("&#340;", "r", $name);
		$name = str_replace("&#341;", "r", $name);
		$name = str_replace("&#346;", "s", $name);
		$name = str_replace("&#347;", "s", $name);
		$name = str_replace("&#377;", "z", $name);
		$name = str_replace("&#378;", "z", $name);
		
		// Map circumflex
		$name = str_replace("&#264;", "c", $name);
		$name = str_replace("&#265;", "c", $name);
		$name = str_replace("&#284;", "g", $name);
		$name = str_replace("&#285;", "g", $name);
		$name = str_replace("&#292;", "h", $name);
		$name = str_replace("&#293;", "h", $name);
		$name = str_replace("&#308;", "j", $name);
		$name = str_replace("&#309;", "j", $name);
		$name = str_replace("&#348;", "s", $name);
		$name = str_replace("&#349;", "s", $name);
		$name = str_replace("&#372;", "w", $name);
		$name = str_replace("&#373;", "w", $name);
		$name = str_replace("&#374;", "y", $name);
		$name = str_replace("&#375;", "y", $name);
		
		// Map dot above
		$name = str_replace("&#266;", "c", $name);
		$name = str_replace("&#267;", "c", $name);
		$name = str_replace("&#278;", "e", $name);
		$name = str_replace("&#279;", "e", $name);
		$name = str_replace("&#288;", "g", $name);
		$name = str_replace("&#289;", "g", $name);
		$name = str_replace("&#304;", "i", $name);
		$name = str_replace("&#379;", "z", $name);
		$name = str_replace("&#380;", "z", $name);
		
		// Map caron
		$name = str_replace("&#268;", "c", $name);
		$name = str_replace("&#269;", "c", $name);
		$name = str_replace("&#270;", "d", $name);
		$name = str_replace("&#271;", "d", $name);
		$name = str_replace("&#282;", "e", $name);
		$name = str_replace("&#283;", "e", $name);
		$name = str_replace("&#317;", "l", $name);
		$name = str_replace("&#318;", "l", $name);
		$name = str_replace("&#327;", "n", $name);
		$name = str_replace("&#328;", "n", $name);
		$name = str_replace("&#344;", "r", $name);
		$name = str_replace("&#345;", "r", $name);
		$name = str_replace("&#352;", "s", $name);
		$name = str_replace("&#353;", "s", $name);
		$name = str_replace("&#356;", "t", $name);
		$name = str_replace("&#357;", "t", $name);
		$name = str_replace("&#381;", "z", $name);
		$name = str_replace("&#382;", "z", $name);
		
		// Map stroke
		$name = str_replace("&#272;", "d", $name);
		$name = str_replace("&#273;", "d", $name);
		$name = str_replace("&#294;", "h", $name);
		$name = str_replace("&#295;", "h", $name);
		$name = str_replace("&#321;", "l", $name);
		$name = str_replace("&#322;", "l", $name);
		$name = str_replace("&#358;", "t", $name);
		$name = str_replace("&#359;", "t", $name);
		
		// Map cedilla
		$name = str_replace("&#290;", "g", $name);
		$name = str_replace("&#291;", "g", $name);
		$name = str_replace("&#310;", "k", $name);
		$name = str_replace("&#311;", "k", $name);
		$name = str_replace("&#315;", "l", $name);
		$name = str_replace("&#316;", "l", $name);
		$name = str_replace("&#325;", "n", $name);
		$name = str_replace("&#326;", "n", $name);
		$name = str_replace("&#342;", "r", $name);
		$name = str_replace("&#343;", "r", $name);
		$name = str_replace("&#350;", "s", $name);
		$name = str_replace("&#351;", "s", $name);
		$name = str_replace("&#354;", "t", $name);
		$name = str_replace("&#355;", "t", $name);
		
		// Map tilde
		$name = str_replace("&#296;", "i", $name);
		$name = str_replace("&#297;", "i", $name);
		$name = str_replace("&#360;", "u", $name);
		$name = str_replace("&#361;", "u", $name);
		
		// Map middle dot
		$name = str_replace("&#319;", "l", $name);
		$name = str_replace("&#320;", "l", $name);
		
		// Map double acute
		$name = str_replace("&#336;", "o", $name);
		$name = str_replace("&#337;", "o", $name);
		$name = str_replace("&#368;", "u", $name);
		$name = str_replace("&#369;", "u", $name);
		
		// Map ring above
		$name = str_replace("&#366;", "u", $name);
		$name = str_replace("&#367;", "u", $name);
		
		// Map other
		$name = str_replace("&#305;", "i", $name);	// dotless
		$name = str_replace("&#306;", "ij", $name);	// ligature ij
		$name = str_replace("&#307;", "ij", $name);	// ligature ij
		$name = str_replace("&#312;", "k", $name);	// kra
		$name = str_replace("&#329;", "n", $name);
		$name = str_replace("&#330;", "n", $name);	// eng
		$name = str_replace("&#331;", "n", $name);	// eng
		$name = str_replace("&#338;", "oe", $name);	// ligature oe
		$name = str_replace("&#339;", "oe", $name);	// ligature oe
		$name = str_replace("&#376;", "y", $name);	// diaeresis
		$name = str_replace("&#383;", "s", $name);	// long s

		// Map Greek - Capital
		$name = str_replace("&#913;", "a", $name);
		$name = str_replace("&#914;", "b", $name);
		$name = str_replace("&#915;", "g", $name);
		$name = str_replace("&#916;", "d", $name);
		$name = str_replace("&#917;", "e", $name);
		$name = str_replace("&#918;", "z", $name);
		$name = str_replace("&#919;", "h", $name);
		$name = str_replace("&#920;", "th", $name);
		$name = str_replace("&#921;", "i", $name);
		$name = str_replace("&#922;", "k", $name);
		$name = str_replace("&#923;", "l", $name);
		$name = str_replace("&#924;", "m", $name);
		$name = str_replace("&#925;", "n", $name);
		$name = str_replace("&#926;", "x", $name);
		$name = str_replace("&#927;", "o", $name);
		$name = str_replace("&#928;", "p", $name);
		$name = str_replace("&#929;", "r", $name);
		$name = str_replace("&#931;", "s", $name);
		$name = str_replace("&#932;", "t", $name);
		$name = str_replace("&#933;", "y", $name);
		$name = str_replace("&#934;", "f", $name);
		$name = str_replace("&#935;", "ch", $name);
		$name = str_replace("&#936;", "ps", $name);
		$name = str_replace("&#937;", "w", $name);
		// Map Greek - Small
		$name = str_replace("&#945;", "a", $name);
		$name = str_replace("&#946;", "b", $name);
		$name = str_replace("&#947;", "g", $name);
		$name = str_replace("&#948;", "d", $name);
		$name = str_replace("&#949;", "e", $name);
		$name = str_replace("&#950;", "z", $name);
		$name = str_replace("&#951;", "h", $name);
		$name = str_replace("&#952;", "th", $name);
		$name = str_replace("&#953;", "i", $name);
		$name = str_replace("&#954;", "k", $name);
		$name = str_replace("&#955;", "l", $name);
		$name = str_replace("&#956;", "m", $name);
		$name = str_replace("&#957;", "n", $name);
		$name = str_replace("&#958;", "x", $name);
		$name = str_replace("&#959;", "o", $name);
		$name = str_replace("&#960;", "p", $name);
		$name = str_replace("&#961;", "r", $name);
		$name = str_replace("&#962;", "s", $name);
		$name = str_replace("&#963;", "s", $name);
		$name = str_replace("&#964;", "t", $name);
		$name = str_replace("&#965;", "y", $name);
		$name = str_replace("&#966;", "f", $name);
		$name = str_replace("&#967;", "ch", $name);
		$name = str_replace("&#968;", "ps", $name);
		$name = str_replace("&#969;", "w", $name);
		// Map Greek - Special
		$name = str_replace("&#902;", "a", $name);
		$name = str_replace("&#904;", "e", $name);
		$name = str_replace("&#905;", "h", $name);
		$name = str_replace("&#906;", "i", $name);
		$name = str_replace("&#908;", "o", $name);
		$name = str_replace("&#910;", "y", $name);
		$name = str_replace("&#911;", "w", $name);
		$name = str_replace("&#912;", "i", $name);
		$name = str_replace("&#938;", "i", $name);
		$name = str_replace("&#939;", "y", $name);
		$name = str_replace("&#940;", "a", $name);
		$name = str_replace("&#941;", "e", $name);
		$name = str_replace("&#942;", "h", $name);
		$name = str_replace("&#943;", "i", $name);
		$name = str_replace("&#944;", "y", $name);
		$name = str_replace("&#970;", "i", $name);
		$name = str_replace("&#971;", "y", $name);
		$name = str_replace("&#972;", "o", $name);
		$name = str_replace("&#973;", "y", $name);
		$name = str_replace("&#974;", "w", $name);
		
		// Remove unwanted entities
		$name = preg_replace('/&#[0-9]+;/', '', $name);
		
		// Convert all special chars to entities
		// so they're easier to handle
		$name = htmlentities($name, ENT_COMPAT, 'Windows-1252');
		
		// Make everything lowercase and trim
		$name = trim(strtolower($name));
		
		// Map non-alpha chars
		$name = str_replace("&amp;", "_", $name);
		$name = str_replace("/", "-", $name);
		
		// Map grave `
		$name = str_replace("&agrave;", "a", $name);
		$name = str_replace("&egrave;", "e", $name);
		$name = str_replace("&igrave;", "i", $name);
		$name = str_replace("&ograve;", "o", $name);
		$name = str_replace("&ugrave;", "u", $name);
		
		// Map acute ´
		$name = str_replace("&aacute;", "a", $name);
		$name = str_replace("&eacute;", "e", $name);
		$name = str_replace("&iacute;", "i", $name);
		$name = str_replace("&oacute;", "o", $name);
		$name = str_replace("&uacute;", "u", $name);
		$name = str_replace("&yacute;", "y", $name);
		
		// Map circumflex ^
		$name = str_replace("&acirc;", "a", $name);
		$name = str_replace("&ecirc;", "e", $name);
		$name = str_replace("&icirc;", "i", $name);
		$name = str_replace("&ocirc;", "o", $name);
		$name = str_replace("&ucirc;", "u", $name);
		
		// Map tilde ~
		$name = str_replace("&atilde;", "a", $name);
		$name = str_replace("&ntilde;", "n", $name);
		$name = str_replace("&otilde;", "o", $name);
		
		// Map diaeresis ¨
		$name = str_replace("&auml;", "a", $name);
		$name = str_replace("&iuml;", "i", $name);
		$name = str_replace("&euml;", "e", $name);
		$name = str_replace("&ouml;", "o", $name);
		$name = str_replace("&uuml;", "u", $name);
		$name = str_replace("&yuml;", "y", $name);
		
		// Map special chars
		$name = str_replace("&aring;", "aa", $name);
		$name = str_replace("&aelig;", "ae", $name);
		$name = str_replace("&ccedil;", "c", $name);
		$name = str_replace("&eth;", "dh", $name);
		$name = str_replace("&oslash;", "oe", $name);
		$name = str_replace("&thorn;", "th", $name);
		$name = str_replace("&szlig;", "ss", $name);
		$name = str_replace("&scaron;" , "s", $name);
		$name = str_replace(chr(138), "s", $name);
		$name = str_replace(chr(154), "s", $name);
		$name = str_replace(chr(142), "z", $name);
		$name = str_replace(chr(158), "z", $name);
		$name = str_replace(chr(159), "y", $name);
		
		// Map fractions and other
		$name = str_replace("&frac14;", "1-4", $name);
		$name = str_replace("&frac12;", "1-2", $name);
		$name = str_replace("&frac34;", "3-4", $name);
		$name = str_replace("&sup1;", "1", $name);
		$name = str_replace("&sup2;", "2", $name);
		$name = str_replace("&sup3;", "3", $name);
		$name = str_replace("&deg;", "deg", $name);
		$name = str_replace("&reg;", "reg", $name);
		$name = str_replace("&copy;", "copy", $name);
		$name = str_replace("&yen;", "yen", $name);
		$name = str_replace("&pound;", "pound", $name);
		$name = str_replace("&cent;", "cent", $name);
		
		// Remove unwanted entities and clean underscore and white-space
		$name = preg_replace('/&[a-z0-9]+;/', '', $name);
		$name = preg_replace('/(\s|_)+/', '_', $name);

		$arrSafeChars = array("-", "_");
		
		for ($i = ord("a"); $i<= ord("z"); $i++) $arrSafeChars[] = chr($i);
		for ($i = ord("0"); $i<= ord("9"); $i++) $arrSafeChars[] = chr($i);
		
		
		$safeName = "";
		for ($i = 0; $i < strlen($name); $i++) {
			$char = substr($name, $i, 1);
			if (in_array($char, $arrSafeChars)) $safeName .= $char;
		}
		
		$name = urlencode($safeName);

		return $name;
	} // End fixname();
	
	/** 
	 * Function that checks if a class is already loaded, if not attempts to load it
	 *
	 * @param string $file path to class file
	 * @param string $classname classname
	 * @return string Error message
	 */
  	
	static function loadClass($file, $classname) { 
		
		if(empty($classname))
			return "No classname provided";
		
		if(empty($file))
			return "No filename provided";
			
		if (class_exists($classname)) 
			return "";

		// Loading class library file (if NOT loaded before)
		if (file_exists($file))
			include_once($file);
		else
			return "Classfile doesnt exists ($file)";

		if (class_exists ($classname)) 
			return "";
		else 
			return "Couldnt find {$classname} in file {$file}<br>";

	} // End loadClass();
	
	
	static function arrayToObject($array) {
		if(is_array($array)) {
			$tmp = new stdClass; // start off a new (empty) object
			foreach ($array as $key => $value) {
				if (is_array($value)) { // if its multi-dimentional, keep going :)
					$tmp->$key = self::arrayToObject($value);
				} else {
					if (!is_numeric($key)) { // can't do it with numbers :(
						$tmp->$key = $value;
					}
				}
			}
			return $tmp; // return the object!
		} else {
			return false;
		}
	}
	
	// * ------------------------------------------------------------------------- *
	//  Builder for querys - for remembering $_REQUEST - variables 
	//  USAGE: supply an array with variables to overrule default values eg. $arrVariables = array("TREEID" => 12)
	// * ------------------------------------------------------------------------- *
	static function queryBuilder($arrVariables = array()) {
		// building query
		$arrTmpQuery = array();
		
		foreach($arrVariables AS $key => $val) {
			$arrTmpQuery[] = $key . "=" . $val;
		}
		
		return "?" . implode("&",$arrTmpQuery);
	}
	
	function jarray($str){
	  $arr = explode('&',$str);
	  for($i = 0 ; $i < count($arr);$i++){
			preg_match("/\=(.*)/",$arr[$i],$m);
			$arr[$i] = $m[1];
	  }
	  return $arr;
	}
	
	 function humanFilesize($size) {
                
		$i=0;
		$iec = array("B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");
		while (($size/1024)>1) {
			$size=$size/1024;
			$i++;
		}
		return round($size,1).$iec[$i];
	}
	
	function generatePassword($length = 8) {

		$password = "";

		$possible = "0123456789bcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNPQRSTUVWXZY";

		$i = 0;

		while ($i < $length) {

			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);

			if (!strstr($password, $char)) {
				$password .= $char;
				$i++;
			}

		}

		return $password;
	}
	
	static function getBackendUrl($treeid = '',$tabid = 0){
		
		$url = "/" . tuksiIni::$arrIni['setup']['admin'] . "/?treeid=" . $treeid;
		if($tabid > 0) {
			$url.= "&tabid=".$tabid;
		}
		return $url;
	}
	
	static function makeXMLFromArray($arrData){
		$xml = "";
		if(is_array($arrData) && count($arrData) > 0) {
			foreach ($arrData as $key => $item){
				$xml.= "<" . $key . ">";
				if(is_array($item)) {
					$xml.= self::makeXMLFromArray($item);;
				} else {
					$xml.= utf8_encode($item);
				}
				$xml.= "</" . $key . ">";
			}
		}
		return $xml;
	}
}
?>
