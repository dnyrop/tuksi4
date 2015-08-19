<div class="mGraphView">
<table>
	{foreach from=$votes item=v}
	<tr class="{cycle values=",even"}">
		<td>{$v.name}:</td>
		<td>{$v.votes} ({$v.percent} %)</td>
		<td class="graphCell"><div class="graphLine" style="width:{$v.width}px;"></td>
	</tr>
	{/foreach}
</table>
</div>
