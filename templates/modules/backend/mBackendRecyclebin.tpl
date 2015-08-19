<script>
__moveFromThrashDialogText = "{cmstext value=movefromrecycle}";
__deleteFromThrashDialogText = "{cmstext value=deletefromrecycle}";
</script>
<input type="hidden" name="moveFromThrash" id="moveFromThrash" value="" >
<input type="hidden" name="deleteFromThrash" id="deleteFromThrash" value="" >
<div class="mListView">
	<table>
	<tr>
		<th width="" class="align_left"><span>{cmstext value=name}</span></th>
		<th width="" class="align_left"><span>{cmstext value=parent}</span></th>
		<th><!--emty--></th>
	</tr>
	{foreach from=$nodes name=node item=node}
		<tr class="link {cycle values=",even"}">
			<td {if $node.nodes} class="arrow"{/if}><span>{$node.name}</span></td>
			<td ><span>{$node.parent_exsist}</span></td>
				<td>
			<ul class="buttons" {if $useseq}style="width:120px;"{/if}>
				<li><a href="#" onclick="deleteFromThrashDialog('{$node.id}'); return false;" class="buttonType4 iconDelete" title="{cmstext value=delete}"><span></span></a></li>
				{if $node.parent_exsist}<li><a href="#" onclick="moveFromThrashDialog('{$node.id}'); return false;" class="buttonType4 iconRecycle" title="{cmstext value=removefromrecycle}"><span></span></a></li>{/if}
			</ul>
		</td>
		</tr>	
		{/foreach}
	</table>
</div><!--//End mListView-->
<div class="actionButtonsRow">
	<ul>
	{if $perm.ADD}<li class="positionRight"><a href="#" class="buttonType3 itemDelete" onclick="doAction('ADD'); return false;"><span><span>{cmstext value=add}</span></span></a></li>{/if}
	</ul>
</div><!--//End actionButtonsRow (Bottom)-->
<div id='deleteRowDialog' style="display:none;">
<table class="moduleElementRow">
	<tbody>
		<tr>
			<td>{cmstext value=deleterowdialog}</td>
		</tr>
	</tbody>
</table>
<table class="moduleElementRow" align="right">
	<tbody>
		<tr>
			<td><a onclick="deleteRow();return false;" class="buttonType3 iconPositive" href="#"><span><span>{cmstext value=btndelete}</span></span></a></td>
			<td><a onclick="tuksi.window.close('#{ldelim}id{rdelim}');return false;" class="buttonType3 iconNegative" href="#"><span><span>{cmstext value=btncancel}</span></span></a></td>
		</tr>
	</tbody>
</table>
</div>
