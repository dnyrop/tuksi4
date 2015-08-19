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
<input type="hidden" value="{$query}" id="query" name="query">
<input type="hidden" value="" id="changePagesize" name="changePagesize">
<input type="hidden" value="" id="editRow_{$module.id}" name="editRow_{$module.id}">
<input type="hidden" value="" id="deleteRow_{$module.id}" name="deleteRow_{$module.id}">
		{if $showsearch || $nav}
		<div class="mSearchBar">
			<ul>
				{if $showsearch}	
				<li><input type="text" class="text" id="searchField1" name="searchField1" size="30" value="{$query}"></li>
				<li><a href="javascript:doSearch('searchField1');" class="buttonType3 iconSearch"><span><span>{cmstext value='search'}</span></span></a></li>
				{/if}	
				{if $nav}
				<li class="positionRight">
					<label>{cmstext value='show_per_page'}:</label>
					<select name="selectPagesize1" onchange="doPagesize(this);">
						{foreach from=$nav.pagesizes item=s}
						<option value='{$s}' {if $s == $nav.pagesize}selected{/if}>{$s}</option>
						{/foreach}	
					</select>
				</li>
				{/if}
			</ul>
		</div><!--//End mSearchBar-->
		{/if}
			{if $nav}
			<div class="mListNavigation">
			<ul>
			{if $nav.previous}
				<li>&lt; <a href="{$nav.previous.url}">{cmstext value='previous'}</a></li>
			{else}
				<li>&lt; {cmstext value='previous'}</li>
			{/if}
			{foreach from=$nav.pages item=p}
			{if $p.isactive}
				<li><strong>{$p.name}</strong></li>
			{else}
				<li><a href="{$p.url}">{$p.name}</a></li>
			{/if}
			{/foreach}
				{if $nav.next}
				<li><a href="{$nav.next.url}">{cmstext value='next'}</a> &gt;</li>
					{else}
				<li>{cmstext value='next'} &gt;</li>
				{/if}
			</ul>
		</div><!--//End mListNavigation-->
		{/if}
<!-- Start: lista -->
		<div class="actionButtonsRow">
			<ul>
			{if $perm.ADD}<li class="positionRight"><a href="#" class="buttonType3 itemAdd" onclick="doAction('ADD'); return false;"><span><span>{cmstext value=add}</span></span></a></li>{/if}
			</ul>
		</div><!--//End actionButtonsRow (TOP)-->
		<div class="mListView">
			{if $module.name}
			<h6>{$module.name}</h6>
			{/if}
			<table>
			<tr>
				{foreach from=$headers item=header name=headers}
				<th {if $header.width}width="{$header.width}"{/if} class="align_left"><span>{$header.name}</span></th>
				{/foreach}
				<th><!--emty--></th>
			</tr>
			{foreach from=$data name=data item=item}
			<tr class="link {cycle values=",even"}">
				{foreach from=$item.col name=col item=col}
				<td{if $smarty.foreach.col.first} class="arrow"{/if} {$col.align} {if !$module.value11}onclick="{$item.link} return false;"{/if}>{if $smarty.foreach.col.first}<span>{/if}{$col.value}{if $smarty.foreach.col.first}</span>{/if}</td>
				{/foreach}
				<td>
					<ul class="buttons" {if $useseq}style="width:120px;"{/if}>
						<li><a href="#" onclick="{$item.link} return false;" class="buttonType4 iconEdit" title="{cmstext value=edit}"><span></span></a></li>
						{if $perm.DELETE == 1}<li><a href="#" onclick="deleteRowDialog('{$module.id}','{$item.rowid}'); return false;" class="buttonType4 iconDelete" title="{cmstext value=delete}"><span></span></a></li>{/if}
						{if $useseq}
							{if $item.moveuplink}	
							<li><a href="#" onclick="{$item.moveuplink} return false;" class="buttonType4 iconUp" title="{cmstext value=move}"><span></span></a></li>
							{/if}
							{if $item.movedownlink}	
							<li><a href="#" onclick="{$item.movedownlink} return false;" class="buttonType4 iconDown" title="{cmstext value=move}"><span></span></a></li>
							{/if}
						{/if}
					</ul>
				</td>
			</tr>
			{/foreach}
			</table>
			{if $nav}
			<div class="mListNavigation">
			<ul>
			{if $nav.previous}
				<li>&lt; <a href="{$nav.previous.url}">{cmstext value='previous'}</a></li>
			{else}
				<li>&lt; {cmstext value='previous'}</li>
			{/if}
			{foreach from=$nav.pages item=p}
			{if $p.isactive}
				<li><strong>{$p.name}</strong></li>
			{else}
				<li><a href="{$p.url}">{$p.name}</a></li>
			{/if}
			{/foreach}
				{if $nav.next}
				<li><a href="{$nav.next.url}">{cmstext value='next'}</a> &gt;</li>
					{else}
				<li>{cmstext value='next'} &gt;</li>
				{/if}
			</ul>
		</div><!--//End mListNavigation-->
		{/if}
		{if $showsearch || $nav}
		<div class="mSearchBar">
			<ul>
				{if $showsearch}	
				<li><input type="text" class="text" id="searchField2" name="searchField2" size="30" value="{$query}"></li>
				<li><a href="javascript:doSearch('searchField2');" class="buttonType3 iconSearch"><span><span>{cmstext value='search'}</span></span></a></li>
				{/if}
				{if $nav}
				<li class="positionRight">
					<label>{cmstext value='show_per_page'}:</label>
					<select name="selectPagesize2" onchange="doPagesize(this);">
					{foreach from=$nav.pagesizes item=s}
					<option value='{$s}' {if $s == $nav.pagesize}selected{/if}>{$s}</option>
					{/foreach}	
					</select>
					{/if}	
				</li>
			</ul>
		</div><!--//End mSearchBar-->
		{/if}
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
