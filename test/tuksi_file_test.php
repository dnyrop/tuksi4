<?
include("include/tuksi_file.inc");

// 


$objFile = new tuksi_file();

$objFile->outfile("uploads/" . $obj->filename, $_GET['filename']);

?>
