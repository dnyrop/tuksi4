<table width="100%" cellpadding="2" cellspacing="0">
{foreach from=$elementlist item=element name=list}
	<tr>
		<td>{$element.name}</td>
		{if $show_checkbox}
		<td><input type="checkbox" value="1" name="{$htmltagname}_{$element.id}[]" {if $element.isactive == 1}onclick="addRelation('{$element.id}', {$fieldid}, '{$htmltagname}', '0');" checked="checked"{else}onclick="addRelation('{$element.id}', {$fieldid}, '{$htmltagname}', '1');"{/if}></td>
		{/if}
		<td>{if !$smarty.foreach.list.first}<a href="#" class="buttonType4 iconUp" onclick="moveRelation('{$element.id}', {$fieldid}, '{$htmltagname}', 'up');"><span><span></span></span></a>{/if}</td>
		<td>{if !$smarty.foreach.list.last}<a href="#" class="buttonType4 iconDown" onclick="moveRelation('{$element.id}', {$fieldid}, '{$htmltagname}', 'down');"><span><span></span></span></a>{/if}</td>	
		<td><a href="#" class="buttonType3 itemDelete" onclick="removeRelation('{$element.id}', {$fieldid}, '{$htmltagname}');"><span><span>{cmstext value="remove"}</span></span></a></td>
	</tr>
{/foreach}
</table>
