<table>
	<tr>
		<td>
{foreach from=$arrInputs item=box name=inputs}
{if $smarty.foreach.inputs.index % 4 == 0 && !$smarty.foreach.inputs.first}
		</td>
	</tr>
	<tr>
		<td>
{/if}
<span style="margin-right: 10px; width: 150px; float: left;">{$box.input}&nbsp;&nbsp;<label for="{$box.boxid}">{$box.name}</label></span>
{/foreach}
		</td>
	</tr>
</table>
