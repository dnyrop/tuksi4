<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="da">
<head>
<link rel="SHORTCUT ICON" href="/favicon.ico">
	<title>Tuksi 4.0 - Login</title>
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="robots" content="noindex, nofollow">
	<meta http-equiv="imagetoolbar" content="no">
	<meta http-equiv="imagetoolbar" content="false">
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
	<link type="text/css" rel="stylesheet" href="/themes/default/stylesheet/style.css" />
	<title>{$error.text}tuksi login</title>
	{foreach from=$page.javascript item=js}
		<script type="text/javascript" src="{$js}"></script>
	{/foreach}
</head>
<body class="login">
<div class="mainLogin">
{$page.content}
</div>
</body>
</html>
