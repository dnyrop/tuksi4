<?php
include("../../core/classes/tuksi_init.php");

$objPaging = new tuksiPaging(105,15,2);

$arr = $objPaging->getPages(3);

$arr= $objPaging->getNavigation();

$arr = $objPaging->getRecords();
for($i = $arr['start'];$i < $arr['stop'];$i++) {
	print $i;
}

?>