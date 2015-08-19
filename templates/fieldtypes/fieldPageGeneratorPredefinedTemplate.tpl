<input type="hidden" name="TABLE_{$fieldcolname}_{$fieldid}_newrow" id="TABLE_{$fieldcolname}_{$fieldid}_newrow" value="0">

&nbsp;&nbsp;&nbsp;<a onclick="document.forms[0].TABLE_{$fieldcolname}_{$fieldid}_newrow.value=1;saveData({$fieldid}); return false;" class="mini_button" href="#" title="#"><span class="mini_bn_st"></span><span>{$ny_template}</span><span class="mini_bn_end"></span></a>
<div class="clear"></div>

<br>
<table class="simple_list">
	<tr>
		<th width="24" class="align_left"><span></span></th>
		<th class="align_left"><span>Navn</span></th>
	</tr>	
{foreach from=$pretpl name=tpl item=item}
	<tr class="{cycle values="class="rowAbgcolor,rowBbgcolor"}">
		<td><a href="/cmsscripts/rightframe.php?TREEID={$item.id}"><img src="/modules/tables/images/pil.gif" alt=""></a></td>
		<td>{$item.name}</td>
	</tr>
{/foreach}	
</table>