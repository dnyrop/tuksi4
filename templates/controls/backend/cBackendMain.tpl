<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>{$page.title}</title>
	<link rel="SHORTCUT ICON" href="/favicon.ico">
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta name="keywords" content="{$page.metakeywords}" />
	<meta name="description" content="{$page.metadescription}" />
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
	<meta name="robots" content="noindex, nofollow">
	<link type="text/css" rel="stylesheet" href="/themes/default/stylesheet/style.css" />
	<!--[if lt IE 7]>
		<link rel="stylesheet" media="screen" type="text/css" href="/themes/default/stylesheet/unsupportedbrowsers.css?0" />
 	<![endif]-->
	{foreach from=$page.javascript item=js}
	<script type="text/javascript" src="{$js}"></script>
	{/foreach}
	
	<script type="text/javascript">
		var objTuksi = new tuksi({ldelim}{if $loaddebug}debug:true{else}debug:false{/if}{rdelim});
	</script>
</head>
<body>
<div id="height">
	<div class="main" id="main">
		<div class="mainHeader">
			<a href="?logo" class="TuksiLogo"><img src="{$path.theme}images/TuksiLogo.png" alt="Tuksi"></a>
			<div class="headerInfo">
				<span>{cmstext value=loggedinas}: <a href="/{$conf.setup.admin}/?treeid={$conf.link.useraccount_treeid}">{$user.name}</a> </span><a href="/{$conf.setup.admin}/?treeid={$conf.link.logout_treeid}"><img src="{$path.theme}images/icons/ic_headerInfo.png" alt="{cmstext value=alt_logout}" title="{cmstext value=alt_logout}" border="0"></a>
				{if $showcontrolpanel}<a class="cButton" href="/{$conf.setup.admin}/?treeid={$conf.link.controlpanel_treeid}"  {if $page.parents[1].id == 2}class="active"{/if}>{cmstext value=controlpanel}</a>{/if}
			</div>
		</div>
		<div class="mainInner">
			<div class="mainLeft">
				<div class="leftFrame" id="leftFrame">
					<div class="leftMenu" id="tree">
						{treestructure nodes=$nodes treeid=$page.treeid}	
					</div><!--//End leftMenu-->
				</div><!--//End leftFrame-->
			</div><!--//End mainLeft-->
			<div class="mainRight" id="mainRight">
				<div class="rightTop" id="rightTop">
					<div class="virtTopTabsMenu"><!--Virtual placeholder--> &nbsp;</div>
					<div class="virtTopActionButtons"><!--Virtual placeholder--> &nbsp;</div>
					<div style="padding:10px 10px 10px 0pt;"></div>
					<div class="breadcrumbs" style="display:none;">
					<strong>{cmstext value="youarehere"}:</strong>	
					{foreach from=$breadcrumb item=b name=breadcrumb}
						{if $treeid != $b.id}
								<a href="/?treeid={$b.id}">{$b.name}</a><span>&gt;</span>
						{else}
							{$b.name}
						{/if}
						{/foreach}
					</div>
				</div><!--//End rightTop-->
				<div class="theFrame">
					<div class="innerFrame">
						<div id="scrollFrame" class="scrollFrame" style="height:500px;">
							<div id="frameContent" class="frameContent">
								<div class="statusMessage error" id="onloadError" style="display:none;">
								</div>
							{if $statusMessage}
								{foreach from=$statusMessage item=msg}
								<div class="statusMessage {$msg.type}" id="statusmessage_{$msg.nb}">
									<p>{$msg.message}</p>
								</div>
								{if $msg.type == 'correct'}
								<script>
									function loadHideMessage{$msg.nb}(){ldelim}
									 	$('statusmessage_{$msg.nb}').fade({ldelim}delay:5,duration:0.5,to:0{rdelim});
									{rdelim}
									tuksi.util.addLoadEvent(loadHideMessage{$msg.nb});
								</script>
								{/if}
								{/foreach}
							{/if}
							<form {if $onsubmit}onsubmit="{$onsubmit}"{/if} name="tuksiForm" id="tuksiForm" method="post" enctype="multipart/form-data" action="">
								<input type="hidden" name="json" id ="json" value="" />
								<input type="hidden" id="userAction" name="userAction" value="" />
							{$page.content}
							</form>
							{if $tuksi_debug}
							<div>{$tuksi_debug}</div>
							{/if}
								<div class="clr"></div>
								{if $showstatus}
								<div class="virtFrameFooter"><!--Virtual placeholder for mFrameFooter --> &nbsp;</div>
								<div class="mFrameFooter">
									<h6>Sidestatus</h6>
									<table class="footerInfo">
										<tr>
											<td><strong>Publiseringsstatus:</strong></td>
											<td>{$pagestatus.status} (<a href="#">http://www.tuksi.com</a>)</td>
										</tr>
										<tr>
											<td><strong>Synlighed:</strong></td>
											<td>{$pagestatus.status}</td>
										</tr>
										<tr>
											<td><strong>Sidst ændret:</strong></td>
											<td>{$pagestatus.lastmodified} af {$pagestatus.modifiedby}</td>
										</tr>
										<tr>
											<td><strong>Permissions</strong></td>
											<td>{$pagestatus.perms}</td>
										</tr>
									</table>
								</div><!--//End mFrameFooter-->
								<div class="clr"></div>
								{/if}
							</div><!--//End frameContent-->
							<div class="clr"></div>
						</div><!--//End scrollFrame-->
					</div><!--//End innerFrame-->
				</div><!--//End theFrame-->
				<!-- MainTopelementer Start -->
				<div class="topActionButtons">
					<ul class="ul">
						{foreach from=$buttons item=button}
							<li class="li"><a href="#" onclick="{$button.onclick}" class="buttonType1"><span><span>{$button.text}</span></span></a></li>
						{/foreach}
						{if $actionbuttons|@count}
						<li class="li actionMenuPosition">
							<ul class="actionMenu">
								<li><a href="#"><span>{cmstext value=chooseaction}</span></a>
									<ul>
										{foreach from=$actionbuttons item=button}
										<li><a href="{$button.href}" onclick="{$button.onclick}"><span class="icon{$button.icontype}">{$button.text}</span></a></li>
										{/foreach}
									</ul>
								</li>
							</ul>
						</li>
						{/if}
						{if $preview}<li class="li previewPosition"><a href="#" onclick="doAction('PREVIEW');" class="buttonType2"><span><span>Preview</span></span></a></li>{/if}
					</ul>
				</div>
				<div class="topTabsMenu">
					<ul>
						<li class="dropDownItem" {if !$history}style="display:none;"{/if}>
							<a href="#" title="Vis historie">History</a>
							<br class="clr">
							<ul>
							{foreach from=$history item=item name=history}
							<li><a href="{$item.url}">{$smarty.foreach.history.iteration}. {$item.title}</a></li>
							{/foreach}
							</ul>
						</li>
						{foreach from=$tabs name=tabs item=tab}
							<li {if $tab.isactive}class="active"{/if}><a href="{$tab.url}">{$tab.name}</a></li>
						{/foreach}
					</ul>
				</div>
				<!-- //End MainTopelementer -->
			</div><!--//End mainRight-->
			<br class="clr">
		</div><!--//End mainInner-->
		<div class="headerTabs">
			<ul>
			{foreach from=$topmenu item=node}
					<li><a href="{$node.url}" {if $node.selected}class="active"{/if}>{$node.name}</a>
					{*<!--<div class="headerTabDropdown">
						<ul>
							<li><a href="#"><span>Afstemning</span></a></li>
							<li><a href="#"><span>Forhandlerliste</span></a></li>
							<li><a href="#"><span>Brugere</span></a></li>
							<li><a href="#"><span>Forside</span></a></li>
						</ul>
					</div>-->*}
				</li>
				{/foreach}
			</ul>
		</div>
	</div><!--//End main-->
</div>
<br class="clr">	
<div id="saving_progress" style="position:absolute;top:5px;right:5px;background:#fff;border:1px solid #6F6F6F;padding:7px;z-index:100000;display:none;">
<table>
	<tr>
		<td valign="middle"><strong>Loading</strong></td>
		<td width="10"></td>
		<td valign="middle"><img src="{$conf.path.theme}images/graphics/loader.gif" alt=""></td>
	</tr>
</table>
</div>
<div id="popupWindow">
	<!--<iframe id="tuksi_popupbox_iframe" src="/core/services/empty.htm" scrolling="no" frameborder="0" style="position:absolute;width:{$width};height:{$height};top:0;border:none;z-index:0;"></iframe>-->
</div><!--//End mPopupWindow-->

{if $page.onload}
<script type="text/javascript">
{foreach from=$page.onload item=onload}
       {$onload};
{/foreach}
</script>
{/if}
</body>
</html>
