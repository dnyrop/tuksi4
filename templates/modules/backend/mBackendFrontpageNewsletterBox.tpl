<div class="mBoxItem">
	<div class="boxItemHeader">
		<h6>{$title}</h6>
	</div>
	<table>
	<tr class="link">
			<th>{cmstext value=date}</th>
			<th>{cmstext value=name}</th>
			<th>{cmstext value=recipients}</th>
		</tr>
	{foreach from=$newsletters item=page}
		<tr class="link">
			<td>{$page.dato}</td>
			<td>{$page.name}</td>
			<td>{$page.sentto}</td>
		</tr>
	{/foreach}
	</table>
	<div class="boxItemFooter"></div>
</div><!--//End mBoxItem-->