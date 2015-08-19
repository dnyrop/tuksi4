<script type="text/javascript">
{literal}
function doPagesize(obj){
	$('changePagesize').value = obj.value;
	doAction('UPDATE');
}
function doSearch(field){
	$('query').value = $F(field); 
	doAction('UPDATE');
}
function editRow(treeid,tabid,moduleid,rowid){
	{/literal}
	window.location = "?treeid="+treeid+"&tabid="+tabid+"&moduleid="+moduleid+"&rowid="+rowid;
	{literal}
}
{/literal}
</script>
<input type="hidden" value="" id="editRow_{$module.id}" name="editRow_{$module.id}">
<input type="hidden" value="" id="deleteRow_{$module.id}" name="deleteRow_{$module.id}">
<!-- Start: lista -->
		<div class="actionButtonsRow">
			<ul>
			{if $perm.ADD}<li class="positionRight"><a href="#" class="buttonType3 itemAdd" onclick="doAction('ADD'); return false;"><span><span>{cmstext value=add}</span></span></a></li>{/if}
			</ul>
		</div><!--//End actionButtonsRow (TOP)-->
		<div class="mListView">
			<h6>{$title}</h6>
			<table>
			<tr>
				{foreach from=$headers item=header name=headers}
				<th {if $header.width}width="{$header.width}"{/if} class="align_left"><span>{$header.name}</span></th>
				{/foreach}
			</tr>
			{foreach from=$data name=data item=item}
			<tr class="link {cycle values=",even"}{if $item.rowid == $showrowid} linkOpen{/if}">
				{foreach from=$item.col name=col item=col}
				<td class="arrow" {$col.align} onclick="editRowB('{$page.treeid}','{$page.tabid}','{$module.id}','{$item.rowid}'); return false;"><span>{$col.value}</span></td>
				{/foreach}
			</tr>
			{if $item.rowid == $showrowid}
			<tr>
				<td colspan="{$nbcols}">
				<div class="mListView">
				<table>
					<tr>
					{foreach from=$subHeaders item=subHeaderItem name=subHeaders}
						<th {if $subHeaderItem.width}width="{$subHeaderItem.width}"{/if} class="align_left"><span>{$subHeaderItem.name}</span></th>
					{/foreach}
					<th><!--empty--></th>
					</tr>
					{foreach from=$subData name=subData item=subDataItem}
					<tr class="link {cycle values=",even"}">
						{foreach from=$subDataItem.col name=col item=subCol}
						<td class="arrow" {$subCol.align} onclick="editRow('{$page.treeid}','{$page.tabid}','{$module.id}','{$subDataItem.rowid}'); return false;"><span>{$subCol.value}</span></td>
						{/foreach}
						<td>
							<ul class="buttons">
								<li><a href="#" onclick="editRow('{$page.treeid}','{$page.tabid}','{$module.id}','{$subDataItem.rowid}'); return false;" class="buttonType4 iconEdit" title="{cmstext value=edit}"><span></span></a></li>
								{if $perm.DELETE == 1}<li><a href="#" onclick="deleteRowDialog('{$module.id}','{$subDataItem.rowid}'); return false;" class="buttonType4 iconDelete" title="{cmstext value=delete}"><span></span></a></li>{/if}
							</ul>
						</td>
					</tr>
				{/foreach}
				</table>
				</div>
				</td>	
			</tr>
			{/if}
			{/foreach}
			</table>
	</div><!--//End mListView-->
		<div class="actionButtonsRow">
			<ul>
			{if $perm.ADD}<li class="positionRight"><a href="#" class="buttonType3 itemAdd" onclick="doAction('ADD'); return false;"><span><span>{cmstext value=add}</span></span></a></li>{/if}
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
<!-- End: lista -->	
<script type="text/javascript">
{if $showsearch}	
{literal}
$('searchField1').observe('keypress',function(e){
	var code;
	if (!e) var e = window.event;
	if (e.keyCode) code = e.keyCode;
	else if (e.which) code = e.which;
	if(code == 13) {
		doSearch('searchField1');
	}
});
$('searchField2').observe('keypress',function(e){
	var code;
	if (!e) var e = window.event;
	if (e.keyCode) code = e.keyCode;
	else if (e.which) code = e.which;
	if(code == 13) {
		doSearch('searchField2');
	}
});
{/literal}
{/if}
</script>
