<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty utf8 convert modifier plugin
 *
 * Type:     modifier<br>
 * Name:     utf8 convert<br>
 * Purpose:  Convert encoding to utf8
 * @author   FSA
 * @param string
 * @return string
 */
function smarty_modifier_utf8_convert($string)
{
	return tuksiTools::encode($string);
}

/* vim: set expandtab: */

?>
