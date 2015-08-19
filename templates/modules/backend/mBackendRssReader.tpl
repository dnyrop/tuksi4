<div class="mBoxItem">
	<div class="boxItemHeader">
		<h6>{$title}</h6>
	</div>
	<table>
	<tr class="link">
			<th>Dato</th>
			<th>Title</th>
		</tr>
	{foreach from=$items item=i}
		<tr class="link">
			<td>{$i.pubDate_clean}</td>
			<td>{$i.title}</td>
		</tr>
	{/foreach}
	</table>
	<div class="boxItemFooter"></div>
</div><!--//End mBoxItem-->