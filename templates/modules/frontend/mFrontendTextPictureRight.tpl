<table width="439" border="0" cellspacing="0" cellpadding="0">
	{if $module.headline}	
	<tr>
		<td width="439" class="tilecontentheader">&nbsp;{$module.headline}</td>
	</tr>
	{/if}
	<tr>
		<td width="439" valign="top">
		<table width="439" border="0" cellspacing="14" cellpadding="0">
		<tr>
			<td valign="top">{$module.content}</td>
			<td valign="top">	{if $module.link}<a {if $module.value1}target="{$module.value1}"{/if} href="{$module.link}">{/if}<img src="{$module.image}" width="125" height="115" alt="{$module.headline}">{if $module.link}</a>{/if}</td>
		</tr>
		</table></td>
	</tr>
</table>