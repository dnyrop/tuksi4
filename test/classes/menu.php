<?php
include("../../core/classes/tuksi_init.php");

$objMenu = new tuksiMenu(4);

$objMenu->setOpenNodes(array(9791));

$arrMenu = $objMenu->getMenu(113);

$tpl = new tuksiSmarty();

$tpl->assign("nodes",$arrMenu);

$tpl->display("menutest.tpl");

?>