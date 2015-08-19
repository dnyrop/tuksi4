<table class="moduleElementRow">
	<tbody>
		<tr>
			<td><label>{cmstext value="name"}:</label></td>
			</tr>
			<tr>
			<td colspan="2">
				<input name="copyPageName{$id}" id="copyPageName{$id}" class="text" type="text" style="width:300px;">
			</td>
		</tr>
		<tr>
			<td colspan="2"><label>{cmstext value="placering"}:</label></td>
		</tr>
		<tr>
			<td width="70">
				<select name="copyPagePlacement{$id}" id="copyPagePlacement{$id}" style="width:70px;">
					<option value="1">{cmstext value="under"}</option>
					<option value="2" selected>{cmstext value="before"}</option>
					<option value="3" selected>{cmstext value="after"}</option>
				</select>
			</td>
			<td>
				<select name="copyPageTreeid{$id}" id="copyPageTreeid{$id}" style="width:216px;">
				{foreach from=$pageSelect	item=p name=pageselect}
					<option value="{$p.id}" {if $treeid == $p.id} selected{/if}>{$p.selectname}</option>
				{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2"><input type="checkbox" name="copyPageSubpages{$id}" id="copyPageSubpages{$id}"> {cmstext value="copy_subpages"}</td>
		</tr>
	</tbody>
</table>
<table class="moduleElementRow" align="right">
	<tbody>
		<tr>
			<td><a onclick="tuksi.pagegenerator.copyPage();return false;" class="buttonType3 iconPositive" href="#"><span><span>{cmstext value="btncopy"}</span></span></a></td>
			<td><a onclick="tuksi.window.close('#{ldelim}id{rdelim}');return false;" class="buttonType3 iconNegative" href="#"><span><span>{cmstext value="btncancel"}</span></span></a></td>
		</tr>
	</tbody>
</table>