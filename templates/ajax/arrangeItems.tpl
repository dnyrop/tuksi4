<ul id='itemarrange'>
{foreach from=$items item=item}
<li id='item_{$item.id}'>{$item.name} ({$item.colname})</li>
{/foreach}
</ul>
<table class="moduleElementRow" align="right">
	<tbody>
		<tr>
			<td><a id="arrangeItemsSubmitButton" onclick="saveArrangeItems();return false;" class="buttonType3 iconPositive" href="#"><span><span>{cmstext value=btnsave}</span></span></a></td>
			<td><a onclick="tuksi.window.close('#{ldelim}id{rdelim}');return false;" class="buttonType3 iconNegative" href="#"><span><span>{cmstext value=btncancel}</span></span></a></td>
		</tr>
	</tbody>
</table>
<script>
Sortable.create('itemarrange');
</script>