<?

include(dirname(__FILE__) . '/../../../../../include/tuksi_init.php');
tuksiIni::setSystemType('backend');

// All users have access
$objDB = tuksiDB::getInstance();


$real_filename = mysql_real_escape_string($_FILES['linkupload']['name']);

$sqlSetPart = "";
print("<html><body><script language=\"javascript\">\n");
print("parent.OnUploadCompleted(0); alert('sdfsdf')");
	
print("</script></body></html>\n");


exit();


if ($real_filename != "") {
	$sqlInsert = "INSERT INTO cmslinkupload (file, cmsuserid) VALUES ('', {$objPage->USER['id']})";
	
	$objDB->query($sqlInsert) or error_log(mysql_error());
	$rowid = mysql_insert_id();
	
	$objField->htmltagname = "linkupload";
	$objField->tablename = "cmslinkupload";
	$objField->colname = "file";
	$objField->id = "1";
	$objField->rowid = $rowid;
	
	$f = new fieldFileupload($objField);

	$sqlInsert = "UPDATE cmslinkupload SET ";
	$sqlSetPart = $f->saveData();
	$sqlWhere = " WHERE id = {$rowid}";
	//error_log($sqlSetPart);
	//print($f->saveData());
}

if ($sqlSetPart != "") {	
	$real_filename = mysql_real_escape_string($_FILES[$objField->htmltagname]['name']);
	$sqlSetPart .= ", filename = '{$real_filename}' ";	
	
	error_log($sqlInsert . $sqlSetPart . $sqlWhere);
	
	$objDB->query($sqlInsert . $sqlSetPart . $sqlWhere) or print mysql_error();
	$filename = $sqlSetPart;
	$filename = substr($filename, strpos($filename, "'")+1);
	$filename = substr($filename, 0, substr($filename, "'")-1);
	
	if ($filename != "") {
		print("<html><body><script language=\"javascript\">\n");
		print("parent.document.getElementById('inpURL').value = '/downloads/{$rowid}/{$real_filename}';parent.applyHyperlink(); parent.close();");
		print("</script></body></html>\n");
	}
} else {
	print("<html><body><script language=\"javascript\">\n");
	print("if (parent.document.getElementById('uploaded_files').selectedIndex < 0) {\n");
	print("		alert('Du skal v&aelig;lge en fil at sende!');\n");
	print("} else {\n");
	print("parent.applyHyperlink(); parent.close();\n");
	print("}\n");
	print("</script></body></html>\n");
}

exit();


?>
