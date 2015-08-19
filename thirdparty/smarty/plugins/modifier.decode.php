<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty decode modifier plugin
 *
 * Type:     modifier<br>
 * Name:     decode<br>
 * Purpose:  Decodes html numeric entities
 * @author   FSA
 * @param string
 * @param bool
 * @return string
 */
function smarty_modifier_decode($string, $utf8 = false)
{
	$encoding = 'Windows-1252';
	if ($utf8) {
		$encoding = 'UTF-8';
		$string = tuksiTools::encode($string);
	}
	
	$string = html_entity_decode($string, ENT_QUOTES, $encoding);
	$convmap = array(0x0, 0x10000, 0, 0xfffff);
	return mb_decode_numericentity($string, $convmap, $encoding);
}

/* vim: set expandtab: */

?>
