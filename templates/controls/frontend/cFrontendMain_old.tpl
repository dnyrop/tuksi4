<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>{$page.title}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta HTTP-EQUIV="keywords" CONTENT="{$page.metakeywords}">
	<meta HTTP-EQUIV="description" CONTENT="{$page.metadescription}">
	<link rel="STYLESHEET" type="text/css" href="/stylesheet/style.css">
	{foreach from=$page.javascript item=js}
		<script src="{$js}"></script>
	{/foreach}
	<link media="screen" href="/stylesheet/tuksi.css" type="text/css" rel="stylesheet">
	{if $tuksi_debug}
	<script>
		var objTuksi = new tuksi({ldelim}{if $conf.debug.active}debug:true{else}debug:false{/if}{rdelim});
		{foreach from=$page.onload item=onload}
			tuksi.util.addLoadEvent({$onload});
		{/foreach}
	</script>
	{/if}
</head>
<body bgcolor="#EDEAE1" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" marginwidth="0" marginheight="0">
<table width="691" height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
	<td width="11" class="tileleft" valign="top"><img src="/images/graphics/gx_blank.gif" alt="" width="1" height="1" border="0"></td>
	<td width="669" valign="top" class="tilecontent">
	{lang text="headline"}
	
	<table width="669" border="0" cellspacing="0" cellpadding="0">
		<!-- logo -->
		<tr>
			<td width="229"><a href="/"><img src="/images/graphics/logo.gif" alt="" width="229" height="151" border="0"></a></td>
			<td width="440"><img src="/images/pictures/top.jpg" alt="" width="440" height="151" border="0"></td>
		</tr><!-- logo slut -->
		</table>
		
		<table width="669" height="19" border="0" cellspacing="0" cellpadding="0">
		<tr><!-- content headers -->
			<td width="229" class="tilecontentheader">&nbsp;Menu</td>
			<td width="1"><img src="/images/backgrounds/white.gif" alt="" width="1" height="15" border="0"></td>
			<td width="439" class="tilecontentheader">&nbsp;{$page.headline}</td>
		</tr><!-- content headers slut -->
		</table>
		
		<table width="669" border="0" cellspacing="0" cellpadding="0">		
		<tr>
			<td width="230" valign="top" class="tilecontent4">
			<!-- menu + nyhed -->
				<!--Her starter menuen-->
					<div class="navigation">
						<ul>
						{foreach name=topmenu item=topmenu from=$menu}			
							{if $topmenu.show_inmenu}
								<li {if $topmenu.selected}class="current"{/if}><a {if $topmenu.selected}class="currentA"{elseif $topmenu.nodes && ($topmenu.selected || $topmenu.open_selected)}class="currentB"{/if} href="/{$topmenu.pg_urlpart_full}" title="{$topmenu.menuname}">{$topmenu.menuname}</a></li>
									{if $topmenu.nodes && ($topmenu.selected || $topmenu.open_selected)}
										<ul>
											{foreach name=submenu item=submenu from=$topmenu.nodes}
												{if $submenu.show_inmenu}
													<li {if $submenu.selected}class="current"{/if}><a {if $submenu.selected}class="currentA"{elseif $submenu.nodes && ($submenu.selected || $submenu.open_selected)}class="currentB"{/if} href="/{$submenu.pg_urlpart_full}" title="{$submenu.menuname}">{$submenu.menuname}</a></li>
														{if $submenu.nodes && ($submenu.selected || $submenu.open_selected)}
															<ul>
																{foreach name=subsubmenu item=subsubmenu from=$submenu.nodes}
																	{if $subsubmenu.show_inmenu}
																		<li {if $subsubmenu.selected}class="current"{/if}><a {if $subsubmenu.selected}class="currentA"{/if} href="/{$subsubmenu.pg_urlpart_full}" title="{$subsubmenu.menuname}">{$subsubmenu.menuname}</a></li>
																	{/if}
																{/foreach}
															</ul>
														{/if}
												{/if}
											{/foreach}
										</ul>
									{/if}	
							{/if}
						{/foreach}
						</ul>
					</div>
		<!--	Her slutter menuen-->
			</td>
		<!-- menu + nyhed slut -->
			
		<!-- content -->	
			<td width="439" valign="top" class="tilewhite">
			
				{$page.content}
				
				
			</td>
			<!-- content slut-->
				
		</tr>
		</table>
	
	
		<!-- adresse  + pink streg -->
		<table width="669" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="229" class="tilewhite"><img src="/images/graphics/staf_blue.gif" width="25" height="8" alt="" border="0"></td>
			<td width="1" class="tilewhite"><img src="/images/graphics/gx_blank.gif" alt="" width="1" height="1" border="0"></td>
			<td width="439" class="tilewhite"><img src="/images/graphics/staf_blue.gif" width="25" height="8" alt="" border="0"></td>
		</tr>
		</table>
		<table width="669" height="19" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="229" class="tilemail"></td>
			<td width="1" class="tilewhite"><img src="/images/graphics/gx_blank.gif" alt="" width="1" height="1" border="0"></td>
			<td width="439" align="right" class="tilecontentheader">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="3" class="tilewhite"><img src="/images/graphics/gx_blank.gif" alt="" width="1" height="1" border="0"></td>
		</tr>
		</table>
		<!-- adresse  + pink streg slut -->
		
	</td>
	<td width="11" valign="top" class="tileright"><img src="/images/graphics/gx_blank.gif" alt="" width="1" height="1" border="0"></td>
</tr>
</table>
<script src="/__utm.js" type="text/javascript"></script>
{if $tuksi_debug}
<div>{$tuksi_debug}</div>
{/if}
{$google_analytics}
</body>
</html>
