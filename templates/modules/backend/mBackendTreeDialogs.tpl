<div id="addTabDialog" style="display:none;">
<table class="moduleElementRow">
	<tbody>
	<tr>
			<td><label>Name:</label></td>
			</tr>
			<tr>
			<td colspan="2">
				<input name="addTabName#{ldelim}formid{rdelim}" id="addTabName#{ldelim}formid{rdelim}" class="text" type="text" style="width:300px;">
			</td>
		</tr>
		<tr>
			<td colspan="2"><label>Template:</label></td>
		</tr>
		<tr>
			<td width="70">
				<select name="addTabTemplate#{ldelim}formid{rdelim}" id="addTabTemplate#{ldelim}formid{rdelim}" style="width:300px;">
				{foreach from=$templates name=tpl item=tpl}	
					<option value="{$tpl.value}">{$tpl.name}</option>
				{/foreach}
				</select>
			</td>
		</tr>
	</tbody>
</table>
<table class="moduleElementRow" align="right">
	<tbody>
		<tr>
			<td><a onclick="tuksi.pagegenerator.addTab();return false;" class="buttonType3 iconPositive" href="#"><span><span>{cmstext value=btninsert}</span></span></a></td>
			<td><a onclick="tuksi.window.close('#{ldelim}id{rdelim}');return false;" class="buttonType3 iconNegative" href="#"><span><span>{cmstext value=btncancel}</span></span></a></td>
		</tr>
	</tbody>
</table>
</div>
<div id="deleteTabDialog" style="display:none;">
<table class="moduleElementRow">
	<tbody>
		<tr>
			<td>{cmstext value=deletetabdialog}</td>
		</tr>
	</tbody>
</table>
<table class="moduleElementRow" align="right">
	<tbody>
		<tr>
			<td><a onclick="tuksi.pagegenerator.deleteTab();return false;" class="buttonType3 iconPositive" href="#"><span><span>{cmstext value=btndelete}</span></span></a></td>
			<td><a onclick="tuksi.window.close('#{ldelim}id{rdelim}');return false;" class="buttonType3 iconNegative" href="#"><span><span>{cmstext value=btncancel}</span></span></a></td>
		</tr>
	</tbody>
</table>
</div>
<div id="deleteNodeDialog" style="display:none;">
<table class="moduleElementRow">
	<tbody>
		<tr>
			<td>{cmstext value=deletenodedialog}</td>
		</tr>
	</tbody>
</table>
<table class="moduleElementRow" align="right">
	<tbody>
		<tr>
			<td><a onclick="tuksi.pagegenerator.deleteNode();return false;" class="buttonType3 iconPositive" href="#"><span><span>{cmstext value=btndelete}</span></span></a></td>
			<td><a onclick="tuksi.window.close('#{ldelim}id{rdelim}');return false;" class="buttonType3 iconNegative" href="#"><span><span>{cmstext value=btncancel}</span></span></a></td>
		</tr>
	</tbody>
</table>
</div>
<div id="addNodeDialog" style="display:none;">
<table class="moduleElementRow">
	<tbody>
	<tr>
			<td><label>Name:</label></td>
			</tr>
			<tr>
			<td colspan="2">
				<input name="addNodeName#{ldelim}formid{rdelim}" id="addNodeName#{ldelim}formid{rdelim}" class="text" type="text" style="width:300px;">
			</td>
		</tr>
		<tr>
			<td colspan="2"><label>Parent:</label></td>
		</tr>
		<tr>
			<td width="70">
				<select name="addNodeParent#{ldelim}formid{rdelim}" id="addNodeParent#{ldelim}formid{rdelim}" style="width:300px;">
				{foreach from=$nodes name=nodes item=node}	
					<option value="{$node.value}" {if $currentnode == $node.value}selected{/if}>{$node.name}</option>
				{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2"><label>Add default Tab</label> <input type="checkbox" name="addNodeTab#{ldelim}formid{rdelim}" id="addNodeTab#{ldelim}formid{rdelim}" checked> </td>
		</tr>
	</tbody>
</table>
<table class="moduleElementRow" align="right">
	<tbody>
		<tr>
			<td><a onclick="tuksi.pagegenerator.addNode();return false;" class="buttonType3 iconPositive" href="#"><span><span>{cmstext value=btnadd}</span></span></a></td>
			<td><a onclick="tuksi.window.close('#{ldelim}id{rdelim}');return false;" class="buttonType3 iconNegative" href="#"><span><span>{cmstext value=btncancel}</span></span></a></td>
		</tr>
	</tbody>
</table>
</div>