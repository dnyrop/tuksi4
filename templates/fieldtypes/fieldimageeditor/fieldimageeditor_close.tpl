<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="da-dk" />
	<title></title>
	<script>
	{if $itemInfo.hiddenfieldname}
		opener.document.{$itemInfo.formname}.{$itemInfo.hiddenfieldname}.value = "{$filename}";
		{if $itemInfo.callback}
			opener.{$itemInfo.callback}();
		{/if}
		window.close();
	{else }
		opener.doAction('SAVE');
		window.close();
	{/if}	
	</script>
</head>
<body>
</body>
</html>