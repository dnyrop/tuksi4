<table class="moduleElementRow">
	<tbody>
		<tr>
			<td colspan="2"><label>{cmstext value="choose_destination"}:</label></td>
		</tr>
		<tr>
			<td width="70">
			<input type="hidden" name="moveTabId{$id}" id="moveTabId{$id}" value="{$tabid}">
				<select name="moveTabPlacement{$id}" id="moveTabPlacement{$id}" style="width:70px;">
					<option value="1">{cmstext value="first"}</option>
					<option value="2" selected>{cmstext value="last"}</option>
				</select>
			</td>
			<td>
				<select name="moveTabTreeid{$id}" id="moveTabTreeid{$id}" style="width:216px;">
				{foreach from=$pageSelect	item=p name=pageselect}
					<option value="{$p.id}" {if $treeid == $p.id} selected{/if}>{$p.selectname}</option>
				{/foreach}
				</select>
			</td>
		</tr>
	</tbody>
</table>
<table class="moduleElementRow" align="right">
	<tbody>
		<tr>
			<td><a onclick="tuksi.pagegenerator.moveTab();return false;" class="buttonType3 iconPositive" href="#"><span><span>{cmstext value="btnmove"}</span></span></a></td>
			<td><a onclick="tuksi.window.close('#{ldelim}id{rdelim}');return false;" class="buttonType3 iconNegative" href="#"><span><span>{cmstext value="btncancel"}</span></span></a></td>
		</tr>
	</tbody>
</table>