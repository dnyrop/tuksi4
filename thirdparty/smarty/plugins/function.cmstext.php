<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {lang} function plugin
 *
 * Type:     function<br>
 * Name:     lang<br>
 * Purpose:  handle texts 
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_cmstext($params, &$smarty)
{
	// Getting current template resourcename from smarty object
	$tpl = $smarty->template_resource;
	
	// Checking text parameter 
	if (empty($params['value'])) {
		$smarty->trigger_error("text: missing text parameter");
		return;
	} else {
		$objText = tuksiText::getInstance($tpl);
 
		$ignoreEmpty = false;
		if (isset($params['empty'])) {
			$pEmpty = strtolower(trim($params['empty']));
			if ($pEmpty && ($pEmpty == 'true' || $pEmpty == '1')) {
				$ignoreEmpty = true;
			}
		}
  	
		$text = $objText->getText($params['value'], $ignoreEmpty);
		
		if (isset($params['value1'])) {
			$text = str_replace('#VALUE1#', $params['value1'], $text);
		}
		
		if (isset($params['value2'])) {
			$text = str_replace('#VALUE2#', $params['value2'], $text);
		}
 	
		if (!empty($params['replace'])) {
			$arrRepl = explode('|', $params['replace']);
			if (count($arrRepl)) {
				foreach ($arrRepl as &$replace) {
					$pos = strpos($replace, ':');
					if ($pos !== false) {
						$key = substr($replace, 0, $pos);
						$val = substr($replace, $pos + 1);
						$text = str_replace($key, $val, $text);
					}
				}
			}
		}

		if (isset($params['utf8'])) {
			$pUTF8 = strtolower(trim($params['utf8']));
			if ($pUTF8 && ($pUTF8 == 'true' || $pUTF8 == '1')) {
				$text = tuksiTools::encode($text);
			}
		}
		
		return $text;
	}
}

/* vim: set expandtab: */
?>
