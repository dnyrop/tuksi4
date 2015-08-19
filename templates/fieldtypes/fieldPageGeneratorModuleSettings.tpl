<table>
	<tr>
		<td>Default</td>
		<td>Ikke Flyttes</td>
		<td>Ikke slettes</td>
		<td>Sekvens</td>
		<td>Navn</td>
	</tr>
{foreach from=$modules item=m}
	<tr>
		<td><input onchange="javascript:changed = 1;" class="formcheckbox" type="checkbox" name="{$m.tagname}_{$m.id}" {if $m.checked}CHECKED{/if}></td>
		<td><input onchange="javascript:changed = 1;" class="formcheckbox" type="checkbox" name="{$m.tagname}_{$m.id}_doplace" {if $m.placement}CHECKED{/if}></td>
		<td><input onchange="javascript:changed = 1;" class="formcheckbox" type="checkbox" name="{$m.tagname}_{$m.id}_dodel" {if $m.not_delete}CHECKED{/if}></td>
		<td><input class="forminputnumber4" name="{$m.tagname}_seq" value="{$m.seq}" type="text"></td>
		<td>{$m.name} {if !$m.isactive}(ikke aktiv){/if}</td>
	</tr>
{/foreach}
</table>