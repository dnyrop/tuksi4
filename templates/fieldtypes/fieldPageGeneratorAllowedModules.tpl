<table>
	<tr>
		<td>Default</td>
		<td>Navn</td>
	</tr>
{foreach from=$modules item=m}
	<tr>
		<td><input onchange="javascript:changed = 1;" class="formcheckbox" type="checkbox" name="{$m.tagname}_{$m.id}" {if $m.checked}CHECKED{/if}></td>
		<td>{$m.name} {if !$m.isactive}(ikke aktiv){/if}</td>
	</tr>
{/foreach}
</table>