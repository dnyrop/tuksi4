<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<!-- Website produced by DWARF A/S ~ www.dwarf.dk -->
	
<html>
<head>
	<link rel="SHORTCUT ICON" href="/favicon.ico">
	<title>{$page.title}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="keywords" content="{$page.metakeywords}">
	<meta http-equiv="description" content="{$page.metadescription}">
{if isset($page.metatags)}
{foreach from=$page.metatags item=metatag}
	{$metatag}
{/foreach}
{/if}
	{if $loaddebug}
	<link rel="stylesheet" type="text/css" href="/stylesheet/tuksi_debug.css">
        {/if}
	<link rel="stylesheet" type="text/css" href="/stylesheet/style.css">
	<script type="text/javascript" src="/javascript/frontend/sifr.js"></script>

	{foreach from=$page.javascript item=js}
	<script type="text/javascript" src="{$js}"></script>
	{/foreach}
	<script type="text/javascript">
	{if $loaddebug}tuksi_debug.setDebug();{/if}
	{foreach from=$page.onload item=onload}
		tuksi.util.addLoadEvent({$onload});
	{/foreach}
	</script>
</head>
<body bgcolor="#EDEAE1" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" marginwidth="0" marginheight="0">
	
	<div class="wrapper">
		<div class="main">
			<div class="mainHeader">
					<a href="/"><img class="logo" src="/images/Tuksi-logo.png" alt="Tuksi Logo" title=""></a>
					<ul class="mainMenu">
						{foreach $menu as $item}
							{if $item.show_inmenu == 1}
								<li><a href="/{$item.pg_urlpart_full}"{if $item.selected || $item.open_selected} class="active"{/if}>{$item.menuname}</a></li>
							{/if}
						{/foreach}
					</ul>
					<ul class="specialMenu">
						{foreach $specialmenu as $item}
							<li><a href="/{$item.pg_urlpart_full}">{$item.menuname}</a></li>
						{/foreach}
					</ul>
			</div><!--//End mainHeader-->
			{$page.content}
			
			<div class="mainFooter"></div>
		</div><!--//End main-->
	</div><!--//End wrapper-->
	
	<script type="text/javascript" src="/javascript/frontend/sifr_config.js"></script>
	<script src="/__utm.js" type="text/javascript"></script>
	{if $tuksi_debug}
	{$tuksi_debug}
	{/if}
	{$google_analytics}
</body>
</html>
