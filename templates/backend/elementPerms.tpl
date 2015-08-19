<script type="text/javascript">
function copyPermsDialog(id) {ldelim}
	var strMessage = "{cmstext value='copypermdialog'}";
	tuksi.window.confirm(strMessage,{ldelim}
		callback:function(){ldelim}copyPerms(id);{rdelim}
	{rdelim});
{rdelim}
function copyPerms(id){ldelim}
	$('copyperm').value = id;
	doAction('SAVE'); return false;
{rdelim}
</script>
<input type="hidden" id="copyperm" name="copyperm" value="">
{foreach from=$group item=g}
<table class="moduleElementRow">
	<tr>
		<td class="column1"><label><strong>{$g.name}</strong></label></td>
	</tr>
</table>	
<table class="moduleElementRow">
	<tr>
		<td class="column1" style="width:130px;"><label>{cmstext value='standardperm'}:</label></td>
		<td><input type="checkbox" name="read_{$g.id}" id="read_{$g.id}" {if $g.read}checked{/if}> {cmstext value='read'}</td>
		<td><input type="checkbox" name="add_{$g.id}" id="add_{$g.id}" {if $g.add}checked{/if}> {cmstext value='add'}</td>
		<td><input type="checkbox" name="save_{$g.id}" id="save_{$g.id}" {if $g.save}checked{/if}> {cmstext value='save'}</td>
		<td><input type="checkbox" name="delete_{$g.id}" id="delete_{$g.id}" {if $g.delete}checked{/if}> {cmstext value='delete'}</td>
		<td><input type="checkbox" name="admin_{$g.id}" id="admin_{$g.id}" {if $g.admin}checked{/if}> {cmstext value='admin'}</td>
	</tr>
</table>
<table class="moduleElementRow">
	<tr>
		<td class="column1" style="width:130px;"><label>{cmstext value='extraperm'}: <br>(click to delete)</label></td>
		{foreach from=$g.extraperm item=ep}
		<td valign="top"><input type="checkbox" name="extraperm_{$g.id}[]" value="{$ep.extrapermid}" id="extraperm_{$g.id}_{$ep.id}"> {$ep.name}</td>
		{/foreach}
	</tr>
</table>
<table class="moduleElementRow">
	<tr>
		<td class="column1" style="width:130px;">{cmstext value='addperm'}</td>
		<td>
			<select class="forminput400" name="newpermid_{$g.id}">
				<option value="">Choose permission</option>
				{foreach from=$extraperm item=ep}
				<option value="{$ep.id}">{$ep.name}</option>
				{/foreach}
			</select>
		</td>
	</tr>
</table>		
<table class="moduleElementRow">
	<tr>
		<td align="right"><a class="buttonType1" href="#" onclick="copyPermsDialog({$g.id});return false;"><span><span>{cmstext value="copyperms"}</span></span></a></td>
	</tr>
</table>
{/foreach}	