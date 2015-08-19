<input type="hidden" id="{$htmtagname}_row_count" name="{$htmtagname}_row_count" value="{$ROWCOUNT}">
<input type="hidden" id="{$htmtagname}_col_count" name="{$htmtagname}_col_count" value="{$COLCOUNT}">
<input type="hidden" id="{$htmtagname}_del_col" name="{$htmtagname}_del_col" value="">
<input type="hidden" id="{$htmtagname}_del_row" name="{$htmtagname}_del_row" value="">
<input type="hidden" id="{$htmtagname}_add_row" name="{$htmtagname}_add_row" value="">
<input type="hidden" id="{$htmtagname}_add_col" name="{$htmtagname}_add_col" value="">

<a name="{$htmtagname}"></a>
<table>
<tr>
{foreach name=top key=colnb item=columns from=$arrColumns}
	<td >
	{if $smarty.foreach.top.last}
		{if $btn.show_add_col}
		<a class="buttonType3 itemAdd" onclick="getElementById('{$htmtagname}_add_col').value=1; doAction('SAVE'); return false;" href="#"><span><span>{$cmstext.add_col}</span></span></a>
		{/if}
	{elseif $smarty.foreach.top.first}
	{elseif $btn.show_del_col}
		<a class="buttonType3 itemDelete" onclick="getElementById('{$htmtagname}_del_col').value={$colnb-1}; doAction('SAVE'); return false;" href="#"><span><span>{$cmstext.delete_col}</span></span></a>
	{/if}
	</td>
{/foreach}
</tr>

{foreach key=rownb name=rowloop item=row from=$arrData}
<tr>
<td>
{if $btn.show_del_row}
	<a class="buttonType3 itemDelete" onclick="getElementById('{$htmtagname}_del_row').value={$rownb}; doAction('SAVE'); return false;" href="#"><span><span>{$cmstext.delete_row}</span></span></a>
{/if}
</td>

	{foreach key=colnb name=colloop item=col from=$row}
		<td>
		<input type="text" style="width: 120px;" class="text" name="{$htmtagname}_{$rownb}_{$colnb}" id="{$htmtagname}_{$rownb}_{$colnb}" value="{$arrData[$rownb][$colnb]}" size="20" {if $readonly}disabled{/if} />
		</td>
	{/foreach}
	<td>
<input type="hidden" id="{$htmtagname}_{$rownb}_op" name="{$htmtagname}_{$rownb}_op" value="">
<input type="hidden" id="{$htmtagname}_{$rownb}_ned" name="{$htmtagname}_{$rownb}_ned"  value="">
<span class="elm_buttons">

	{if $smarty.foreach.rowloop.iteration>1}
		<a onclick="getElementById('{$htmtagname}_{$rownb}_op').value=1; saveData('{$htmtagname}'); return false" class="mini_button" href="#" title="#"><span class="mini_bn_st"><img src="/themes/default/images/icons/ic_arrowUp.png"/></span><span class="mini_a_up"></span><span class="mini_bn_end"></span></a>
	{else}
		<span class="mini_up_down_spacer"></span>
	{/if}
	
	{if  !$smarty.foreach.rowloop.last}
		<a onclick="getElementById('{$htmtagname}_{$rownb}_ned').value=1; saveData('{$htmtagname}'); return false" class="mini_button" href="#" title="#"><span class="mini_bn_st"></span><span class="mini_a_down"><img src="/themes/default/images/icons/ic_arrowDown.png"/></span><span class="mini_bn_end"></span></a>
	{else}
		<span class="mini_up_down_spacer"></span>
	{/if}
</span>
</td>
</tr>
{/foreach}
<tr>
{foreach name=bottom item=colomns from=$arrColumns}
<td>{if $smarty.foreach.bottom.first && $btn.show_add_row}
<a class="buttonType3 itemAdd" onclick="getElementById('{$htmtagname}_add_row').value=1; doAction('SAVE'); return false;" href="#"><span><span>{$cmstext.add_row}</span></span></a>
{/if}
{/foreach}
</tr>
</table>