<table class="moduleElementRow moduleElementRowFullWidth">
<tbody>
{foreach from=$lang item=item}
<tr>
	<td class="column1"><label>{$item.name}</label></td>
	<td width="150" id='textsuggest_{$item.lang}_{$item.id}'>{if $item.value}{$item.value}{else}{cmstext value=empty}{/if}</td>
	<td align="right" style="text-align:right;"><input type="hidden" id='textsuggestvalue_{$item.lang}_{$item.id}' value="{$item.value}"><a href="#" onclick="editTextVal('{$item.id}','{$item.lang}',this);return false;">{cmstext value=edit}</a></td>
</tr>
{/foreach}
</tbody>
</table>
