<?php
include_once(dirname(__FILE__) . "/../include/tuksi_init.php");

tuksiIni::setSystemType('backend');

$id = $_POST->getStr('id');

$uploader = new tuksiUpload($id);
//$uploader->setAllowedTypes(array('jpg','jpeg','gif'));
//$uploader->setUploadPath($arrFielditem['tablename']);
if(!$uploader->upload()) {
	$error = $uploader->getError();
	echo $error;
	error_log($error);
} else {
	$info = $uploader->getUploadInfo();
	echo $info;
}
?>