<table class="moduleElementRow">
	<tbody>
		<tr>
			<td colspan="2"><label>{cmstext value="choose_destination"}:</label></td>
		</tr>
		<tr>
			<td width="70">
				<select name="movePagePlacement{$id}" id="movePagePlacement{$id}" onchange="tuksi.pagegenerator.checkParent('movePagePlacement{$id}','movePageTreeid{$id}','movePageDialogMoveError{$id}','movePageSubmitButton');" style="width:70px;">
					<option value="1" selected>{cmstext value="under"}</option>
					<option value="2">{cmstext value="before"}</option>
					<option value="3">{cmstext value="after"}</option>
				</select>
			</td>
			<td>
				<select name="movePageTreeid{$id}" id="movePageTreeid{$id}" onchange="tuksi.pagegenerator.checkParent('movePagePlacement{$id}','movePageTreeid{$id}','movePageDialogMoveError{$id}','movePageSubmitButton');" style="width:216px;">
				<option value="">{cmstext value=choose_page}</option>
				{foreach from=$pageSelect	item=p name=pageselect}
					<option value="{$p.id}" {if $treeid == $p.id} selected{/if}>{$p.selectname}</option>
				{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2"><div id="movePageDialogMoveError{$id}" style="display:none;">{cmstext value=movepagepermerror}</div></td>
		</tr>
	</tbody>
</table>
<table class="moduleElementRow" align="right">
	<tbody>
		<tr>
			<td><a id="movePageSubmitButton" onclick="tuksi.pagegenerator.movePage();return false;" class="buttonType3 iconPositive" href="#"><span><span>{cmstext value="btnmove"}</span></span></a></td>
			<td><a onclick="tuksi.window.close('#{ldelim}id{rdelim}');return false;" class="buttonType3 iconNegative" href="#"><span><span>{cmstext value="btncancel"}</span></span></a></td>
		</tr>
	</tbody>
</table>