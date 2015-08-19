{$content}
<input type="hidden" name="releaseSingleTable" id="releaseSingleTable" value="">
{if $tables}
<div class="mListView">
		<h6>{cmstext value=tablerelease}</h6>
		<table>
		<tr>
			<th class="align_left"><span>{cmstext value=tablename}</span></th>
			<th class="align_left"><span>{cmstext value=fieldstatus}</span></th>
			<th class="align_left"><span>{cmstext value=livestatus}</span></th>
			<th class="align_left"><span>{cmstext value=differentrows}</span></th>
			<th class="align_left"><span>{cmstext value=islive}</span></th>
		</tr>
		{foreach from=$tables name=tables item=table}
			<tr class="link {if $table.error}error{else}{cycle values=",even"}{/if}" >
				<td>{$table.name}</td>
				<td>{$table.diffstatus}</td>
				<td>{$table.livestatus}</td>
				<td>{$table.rows}</td>
				<td><input type="checkbox" name="livetables[]" value="{$table.name}" {if $table.islive}checked{/if}></td>
				<td>{if $table.islive}<a href="#" class="buttonTableRelease" onclick="releaseTable('{$table.name}');return false;" ><span>{cmstext value=releasetable}</span>{/if}</td>
			</tr>
		{/foreach}
		</table>
</div><!--//End mListView-->
{/if}
{literal}
<script>
function releaseTable(name){
	$('releaseSingleTable').value = name;
	doAction('RELEASESINGLE');
}
</script>
{/literal}
