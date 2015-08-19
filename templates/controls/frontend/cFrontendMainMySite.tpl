<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<link rel="icon" href="/favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
	<title>{$page.title}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta HTTP-EQUIV="keywords" CONTENT="{$page.metakeywords}">
	<meta HTTP-EQUIV="description" CONTENT="{$page.metadescription}">
	<link rel="stylesheet" type="text/css" href="/stylesheet/mysite/style.css">

	<script type="text/javascript" src="/javascript/frontend/mysite/functions.js"></script>
	<script type="text/javascript" src="/javascript/frontend/mysite/sifr203.js"></script>
	<script type="text/javascript" src="/javascript/frontend/mysite/site.js"></script>	
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
<body bgcolor="#EDEAE1"{if $page.treeid == 238} class="frontpage"{/if}>

<div class="main">

<div class="mainHeader" {if $page.treeid == 238}style="background-image:url(/images/mysite/pictures/pi_header_1.jpg);"{/if}>
<a href="/"><img src="/images/mysite/mysite-logo.png" alt="RespektIt Logo" title=""></a>

			<img class="png" src="/images/mysite/graphics/gx_transparentBorder.png" alt="">
			<ul>
				<!-- <li class="page0"><a href="/" {if $page.treeid == 238}class="active"{/if}>Forside</a></li> -->
			{foreach name=topmenu item=topmenu from=$menu name=top}			
				{if $topmenu.show_inmenu}
				
				<li class="page{$smarty.foreach.top.iteration}"><a href="/{$topmenu.pg_urlpart_full}" {if $topmenu.open_selected}class="active"{/if}>{$topmenu.menuname}</a></li>

				{/if}
			{/foreach}	
			
			</ul>
</div>

{$page.content}

<div class="mainLeft">

	{foreach name=topmenu key=key item=topmenu from=$menu}			
		
{if $topmenu.show_inmenu}
							<!--	<li {if $topmenu.selected}class="current"{/if}><a {if $topmenu.selected}class="currentA"{elseif $topmenu.nodes && ($topmenu.selected || $topmenu.open_selected)}class="currentB"{/if} href="/{$topmenu.pg_urlpart_full}" title="{$topmenu.menuname}">{$topmenu.menuname}</a></li> -->
{if $topmenu.nodes && ($topmenu.selected || $topmenu.open_selected)}
			<ul>
{foreach name=submenu item=submenu from=$topmenu.nodes}
{if $submenu.show_inmenu}
				<li {if $submenu.selected}class="current"{/if}><a {if $submenu.selected}class="active"{elseif $submenu.nodes && ($submenu.selected || $submenu.open_selected)}class="active"{/if} href="/{$submenu.pg_urlpart_full}" title="{$submenu.menuname}">{$submenu.menuname}</a>
{if $submenu.nodes && ($submenu.selected || $submenu.open_selected)}
			<ul>
{foreach name=subsubmenu item=subsubmenu from=$submenu.nodes}
{if $subsubmenu.show_inmenu}
													<li><a {if $subsubmenu.selected}class="active"{/if} href="/{$subsubmenu.pg_urlpart_full}" title="{$subsubmenu.menuname}">{$subsubmenu.menuname}</a>
{if $subsubmenu.nodes && ($subsubmenu.selected || $subsubmenu.open_selected)}
													<ul>
{foreach name=subsubsubmenu item=subsubsubmenu from=$subsubmenu.nodes}
{if $subsubsubmenu.show_inmenu}
															<li {if $subsubsubmenu.selected}class="current"{/if}><a {if $subsubsubmenu.selected}class="active"{/if} href="/{$subsubsubmenu.pg_urlpart_full}" title="{$subsubsubmenu.menuname}">{$subsubsubmenu.menuname}</a></li>
{/if}
{/foreach}
													</ul>
{/if}
																		<!--{*if $subsubmenu.nodes && ($subsubmenu.selected || $subsubmenu.open_selected)}-->
																		<!--{/if*}-->
													</li>
{/if}
{/foreach}
													</ul>
{/if}
												</li>
{/if}
{/foreach}
											</ul>
{/if}	
{/if}
{/foreach}
</div>
	
	<div class="mainFooter">
		<div>
			<span>MySite</span>
			<span>Bernhard Bangs Allé 25</span>
			<span>2000 Frederiksberg</span>
			<span>E-mail: MySite</span>
			<span>Tlf.: +45 00 00 00 00</span>
		</div>			
	</div>
	
</div>
<div class="credit"><a class="credit" href="/credits.htm" onclick="return setPopup(this, 194, 295)"></a></div>

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write("\<script src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'>\<\/script>" );
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-2922539-1");
pageTracker._setLocalRemoteServerMode();
pageTracker._initData();
pageTracker._trackPageview();
</script>

{$tuksi_debug}
</body>
</html>
