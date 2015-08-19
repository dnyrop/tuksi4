
<script type="text/javascript">
{literal}
function doPagesize(obj){
	$('changePagesize').value = obj.value;
	doAction('UPDATE');
}
function doSearch(field){
	$('strSearch').value = $F(field); 
	doAction('UPDATE');
}
function editRow(treeid,tabid,moduleid,rowid){
	{/literal}
	window.location = "?treeid="+treeid+"&tabid="+tabid+"&moduleid="+moduleid+"&rowid="+rowid;
	{literal}
}

function changePage(treeid){
	{/literal}
	window.location = "?treeid="+treeid;
	{literal}
}

{/literal}
</script>

<input type="hidden" value="{$strSearch}" id="strSearch" name="strSearch"> 
<input type="hidden" value="" id="changePagesize" name="changePagesize">
<input type="hidden" value="" id="editRow_{$module.id}" name="editRow_{$module.id}">
<input type="hidden" value="" id="deleteRow_{$module.id}" name="deleteRow_{$module.id}">
		
		<div class="mSearchBar">
			<ul>
				{if $showsearch}	
					<li><input type="text" class="text" id="searchField1" name="searchField1" size="30" value="{$strSearch}"></li>
					<li><a href="javascript:doSearch('searchField1');" class="buttonType3 iconSearch"><span><span>Søg</span></span></a></li>
				{/if}	
				{if $nav}
				<li class="positionRight">
					<label>Vis per side:</label>
					<select name="selectPagesize1" onchange="doPagesize(this);">
						{foreach from=$nav.pagesizes item=s}
						<option value='{$s}' {if $s == $nav.pagesize}selected{/if}>{$s}</option>
						{/foreach}	
					</select>
				</li>
				{/if}
			</ul>
		</div><!--//End mSearchBar-->
		
<br>

		<div class="mListView">
		{if $resultId or $resultTree}
			<h6>Side resultater</h6>
			<table>
			<th width="2%" class="align_left">&nbsp;</th><th>&nbsp;</th>
			{foreach from=$resultTree name=data item=data}
			<tr class="link {cycle values=",even"}">
			<td>
			<td onclick="javascript:changePage('{$data.id}');">
			{if $smarty.foreach.data.first}<span>{/if}{$data.name} &nbsp;&nbsp;( {$data.id} ){if $smarty.foreach.data.first}</span>{/if}</td>
			</td>
			</tr> 
			{/foreach}

			{foreach from=$resultId name=data item=data}
			<tr class="link {cycle values=",even"}">
			<td>
			<td onclick="javascript:changePage('{$data.id}');">
			{if $smarty.foreach.data.first}<span>{/if}{$data.name} &nbsp;&nbsp;( {$data.id} ){if $smarty.foreach.data.first}</span>{/if}</td>
			</td>
			</tr> 
			{/foreach}
			</table>
			{/if}
			{if $resultModule}
			<h6>Modul resultater</h6>
			<table>
			<th width="2%" class="align_left">&nbsp;</th><th>&nbsp;</th>
			{foreach from=$resultModule name=data item=data}
			{if $data.name}
			<tr class="link {cycle values=",even"}">
			<td>
			<td onclick="javascript:changePage('{$data.id}');">
			{if $smarty.foreach.data.first}<span>{/if}{$data.name} &nbsp;&nbsp;[ {$data.classname} ]{if $smarty.foreach.data.first}</span>{/if}</td>
			</td>
			</tr> 
			{/if}
			{/foreach}
			</table>
			{/if}
		</div>
		
<!-- Start: lista -->


{*
		<div class="mSearchBar">
			<ul>
				{if $showsearch}	
					<li><input type="text" class="text" id="searchField1" name="searchField1" size="30" value="{$strSearch}"></li>
					<li><a href="javascript:doSearch('searchField1');" class="buttonType3 iconSearch"><span><span>Søg</span></span></a></li>
				{/if}	
				{if $nav}
				<li class="positionRight">
					<label>Vis per side:</label>
					<select name="selectPagesize1" onchange="doPagesize(this);">
						{foreach from=$nav.pagesizes item=s}
						<option value='{$s}' {if $s == $nav.pagesize}selected{/if}>{$s}</option>
						{/foreach}	
					</select>
				</li>
				{/if}
			</ul>
		</div><!--//End mSearchBar-->
*}		
	
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
