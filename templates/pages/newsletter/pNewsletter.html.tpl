<html>
<head>
<title>{$page.title}</title>
<meta http-equiv="imagetoolbar" content="no">
<meta http-equiv="imagetoolbar" content="false">
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
{literal}
<style type="text/css">
<!--
td a{
color:#2f556a;
}
-->
</style>
{/literal}
</head>
{*
Uploads: {$conf.newsletter.path.url_uploads}<br>
website: {$conf.newsletter.path.url_site}<br>
View newsletter online: {$conf.newsletter.path.url_site}/{$conf.setup.newsletter}/?treeid={$page.id}<br>
Off: http://{$conf.newsletter.path.url_site}/ afmeld.html?email=[EMAIL]
*}
Kan du ikke læse nyhedsbrev så se det online <a href="{$conf.newsletter.path.url_site}/{$conf.setup.newsletter}/?treeid={$page.id}&t=[TRACKINGID]">her</a>
<body bgcolor="#a2adb4" link="#2f556a">
	<table width="100%" bgcolor="#a2adb4" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<center>
					<table width="588" cellspacing="0" cellpadding="0">
							<tr>
								<td width="88" align="left" style="text-align: left;"><a href="{$newsletter_link.frontpage}"><img border="0" src="{$conf.newsletter.path.url_site}/images/graphics/logo.gif"></a></td>
							</tr>
							<tr>
								<td width="500" height="71" bgcolor="#ffffff" align="left" style="font-family: arial; font-size: 18px; font-weight: bold; color: rgb(34, 50, 65);"><img width="10" height="1" src="{$conf.newsletter.path.url_site}/t.gif">{$page.title}</td>
							</tr>
					</table>
					<table width="588" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff">
						<tr>
							<td  width="542" align="left" style="text-align: left;">
								{$CONTENT}
							</td>
						</tr>
					</table>
				</center>	
			</td>
		</tr>
	</table>
	<a href="{$newsletter_link.frameld}">Frameld dig nyhedsbrevet her</a>

<img SRC="{$conf.newsletter.path.url_site}/email.[TRACKINGID]/email.gif" ALT="" WIDTH="11" HEIGHT="11" BORDER="0">
</body>
</html>
