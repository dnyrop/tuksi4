<!-- start: image/text -->
<tr>
	<td width="535">
		<table border="0" cellpadding="0" cellspacing="0" width="535">
		<tr>
			<td valign="top">
				<table border="0" cellpadding="0" cellspacing="0" width="248">
				<tr>
				</tr>
				<tr>
					<td><img src="{$conf.newsletter.path.url_uploads}/{$module.image}" height="182" width="242" alt=""></td>
				</tr>
				<tr>
				</tr>
				</table>
			</td>
			<td width="34"></td>
			<td valign="top">
				<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<!-- start: text/image headline -->
						<table border="0" cellpadding="0" cellspacing="0">
						<tr>
						</tr>
						<tr>
							<td><h1>{$module.headline}</h1></td>
						</tr>
						<tr>
						</tr>
						</table>
						<!-- end: text/image headline -->
					</td>
				</tr>
				<tr>
					<td><p>{$module.content}	{if $module.link}
		<br><br>
			<a href="{$trackingurl}">Læs mere</a>
		{/if}</p></td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr><td height="15"></td></tr>
<!-- end: image/text -->
