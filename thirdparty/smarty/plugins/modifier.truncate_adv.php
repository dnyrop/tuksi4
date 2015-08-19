<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty truncate modifier plugin - Advanced handles html entities
 *
 * Type:     modifier<br>
 * Name:     truncate<br>
 * Purpose:  Truncate a string to a certain length if necessary,
 *           optionally splitting in the middle of a word, and
 *           appending the $etc string or inserting $etc into the middle.
 * @link http://smarty.php.net/manual/en/language.modifier.truncate.php
 *          truncate (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @param integer
 * @param string
 * @param boolean
 * @param boolean
 * @return string
 */
function smarty_modifier_truncate_adv($string, $length = 80, $etc = '...', $break_words = false, $middle = false) {
    if ($length == 0)
        return '';
    
    $arrStr = array();
    $arrChr = str_split($string);
    $tmpStr = '';
    foreach ($arrChr as $chr) {
        if (empty($tmpStr)) {
            if ($chr == '&') {
                $tmpStr = '&';
            } else {
                $arrStr[] = $chr;
            }
        } else if ($chr == ';') {
            $arrStr[] = $tmpStr . ';';
            $tmpStr = '';
        } else {
            if (preg_match('/[#a-z0-9]/i', $chr)) {
                $tmpStr.= $chr;
            } else {
                $arrStr = array_merge($arrStr, str_split($tmpStr));
                $tmpStr = '';
            }
        }
    }
    
    if (count($arrStr) > $length) {
        $length -= min($length, strlen($etc));
        if (!$break_words && !$middle) {
            $arrStr = array_slice($arrStr, 0, $length + 1);
            $spaceKey = 0;
            for ($i = count($arrStr) - 1; $i >= 0; $i--) {
                if ($arrStr[$i] === ' ') {
                    $spaceKey = $i;
                } else if ($spaceKey) {
                    break;
                }
            }
            $arrStr = array_slice($arrStr, 0, $spaceKey);
        }
        if (!$middle) {
            return join('', array_slice($arrStr, 0, $length)) . $etc;
        } else {
            return join('', array_slice($arrStr, 0, $length/2)) . $etc . join('', array_slice($arrStr, -$length/2));
        }
    } else {
        return $string;
    }
}

/* vim: set expandtab: */

?>
