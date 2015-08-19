<input name="{$module.htmltagname}_delete" id="{$module.htmltagname}_delete" value="" type="hidden" />
<table class="chooseAreaForModule">
{$slet}
	<tr>
		<td colspan="2"><br /></td>
	</tr>
	<tr>
		<td style="width: 312px;">
			<select name="{$module.htmltagname}_new">
				<option value="">Vælg område</option>
				{foreach name=option item=option from=$area}
				{$option.name}
				{if !$option.contentAreaChoosen}
				<option value="{$option.id}">{$option.name}</option>
				{/if}
				{/foreach}
			</select>
		</td>
		<td>{$tilfoj}</td>
	</tr>
</table>
