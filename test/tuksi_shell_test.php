#!/pack/php-5.0.5.old/bin/php
<?
/**
 * @package tuksiTest
 */

include(dirname(__FILE__) . '/../include/tuksi_init.php');


// no debug. Writes to DB and sendes mail
$objShell = new tuksiShell(1, 0);

$objShell->log('Getting texts', 1);

$objShell->log('Getting texts, error', 2);

$objShell->end();

?>