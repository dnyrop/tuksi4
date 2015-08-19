<div class="mGraphView">
<table>
	{foreach from=$list item=l}
	<tr class="{cycle values=",even"}"  {if $l.link}onclick="document.location='{$l.url}';"{/if}>
		<td width="70" >{$l.name} ({$l.count}):</td>
		<td width="20" align="right">{$l.count}</td>
		<td class="graphCell"><div class="graphLine" style="width:{$l.width}px;"></td>
	</tr>
	{/foreach}
</table>
</div>
