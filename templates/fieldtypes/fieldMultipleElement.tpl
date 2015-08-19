<!-- Start: multiple_element -->
{$html_start}
<input name="TABLE_{$fieldcolname}_{$fieldid}_newrow" value="0" type="hidden">
<a name="{$fieldid}"></a>
<table>
<tr>
<td>
{if $addperm}
	<a class="buttonType3" onclick="document.forms[0].TABLE_{$fieldcolname}_{$fieldid}_newrow.value=1;saveData({$fieldid}); return false;" href="#"><span><span>{$new_element}</span></span></a>
{/if}
</td>
{if $tableadminurl}
<td><a class="buttonType1 iconAdd" href="{$tableadminurl}"><span><span>{cmstext value=setup}</span></span></a></td>
{/if}
</tr>
</table>

{foreach name=row item=item from=$row}
<input type="hidden" name="TABLE_{$fieldid}_{$item.id}_moveup">
<input type="hidden" name="TABLE_{$fieldid}_{$item.id}_movedown">
<input type="hidden" name="{$item.fieldname_isopen}" value="{$item.isopen}">
<input type="hidden" name="{$item.fieldname_isopen}_old" value="{$item.isopen}">
<input type="hidden" name="TABLE_{$fieldid}_{$item.id}_deleterow">
<div class="contentItemRow" id="module_{$module.id}">
	<div class="itemHeader" style="width:100%;">
		<ul>
			<li><h6 onclick="document.tuksiForm.{$htmltagname}_isopen_{$item.id}.value = {$item.isopen_invert};saveData({$this->objField->id});" {if $module.isopen}class="active"{/if}>{$item.row_elem_title}</h6></li>
			<li class="positionRight">
			<ul class="buttons">
				<li>{if $smarty.foreach.row.first}
					<img src="/themes/default/images/icons/ic_arrowUp_deactivated.gif">
				{else}
									<a onclick="document.tuksiForm.TABLE_{$fieldid}_{$item.id}_moveup.value = 1; saveData({$fieldid}); return false" class="mini_button" href="#{$item.id}" title="#"><img src="/themes/default/images/icons/ic_arrowUp.png"></a>
				{/if}
				{if $smarty.foreach.row.last}
									<img src="/themes/default/images/icons/ic_arrowDown_deactivated.gif">
				{else}
									<a onclick="document.tuksiForm.TABLE_{$fieldid}_{$item.id}_movedown.value = 1; saveData({$fieldid}); return false" class="mini_button" href="#{$item.id}" title="#"><img src="/themes/default/images/icons/ic_arrowDown.png"></a>
				{/if}
				</li>
				<li><a href="#" onclick="document.tuksiForm.{$htmltagname}_isopen_{$item.id}.value = {$item.isopen_invert};saveData({$fieldid});" class="buttonType4 iconEdit" title="{$edit_element}"><span></span></a></li>
				{if $deleteperm}<li><a href="#" onclick="document.tuksiForm.TABLE_{$fieldid}_{$item.id}_deleterow.value = 1;saveData({$fieldid});" class="buttonType4 iconDelete" title="{$delete_element}"><span></span></a></li>{/if}
				</ul>	
			</li>
		</ul>
</div><!--//End itemHeader-->
<table>
{foreach key=fieldtypeid item=fieldtype from=$item.fieldtypes}
<tr class="multi_elm_edit">
	<td></td>
	<td>{$fieldtype.name}</td>
	<td>{$fieldtype.html}</td>
</tr>
{/foreach}
</table>
</div>
{/foreach}
<!-- End: multiple_element -->
