<table class="moduleElementRow">
	<tbody>
		<tr>
			<td><label>{cmstext value="name"}:</label></td>
			</tr>
			<tr>
			<td colspan="2">
				<input name="addPageName{$id}" id="addPageName{$id}" class="text" type="text" style="width:300px;">
			</td>
		</tr>
		<tr>
			<td colspan="2"><label>{cmstext value="placement"}:</label></td>
		</tr>
		<tr>
			<td width="70">
				<select name="addPagePlacement{$id}" id="addPagePlacement{$id}" onchange="tuksi.pagegenerator.checkParent('addPagePlacement{$id}','addPageTreeid{$id}','addPageDialogMoveError{$id}','addPageSubmitButton');" style="width:70px;">
					<option value="1" selected="selected">{cmstext value="under"}</option>
					<option value="2">{cmstext value="before"}</option>
					<option value="3">{cmstext value="after"}</option>
				</select>
			</td>
			<td>
				<select name="addPageTreeid{$id}" id="addPageTreeid{$id}" onchange="tuksi.pagegenerator.checkParent('addPagePlacement{$id}','addPageTreeid{$id}','addPageDialogMoveError{$id}','addPageSubmitButton');" style="width:216px;">
				<option value="">{cmstext value=choose_page}</option>
				{foreach from=$pageSelect	item=p name=pageselect}
					<option value="{$p.id}" {if $treeid == $p.id} selected{/if}>{$p.selectname}</option>
				{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2"><div id="addPageDialogMoveError{$id}" style="display:none;">{cmstext value=addpagepermerror}</div></td>
		</tr>
	</tbody>
</table>
<table class="moduleElementRow" align="right">
	<tbody>
		<tr>
			<td><a id="addPageSubmitButton" onclick="tuksi.pagegenerator.addPage();return false;" class="buttonType3 iconPositive {if !$checkparent}disabledButton{/if}" href="#"><span><span>{cmstext value=add}</span></span></a></td>
			<td><a onclick="tuksi.window.close('#{ldelim}id{rdelim}');return false;" class="buttonType3 iconNegative" href="#"><span><span>Cancel</span></span></a></td>
		</tr>
	</tbody>
</table>
{if $checkparent}
<script>
tuksi.pagegenerator.setParentStatus(true);
</script>
{/if}
