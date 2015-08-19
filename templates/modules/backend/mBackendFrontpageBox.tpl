<div class="mBoxItem">
	<div class="boxItemHeader">
		<h6>{$title}</h6>
	</div>
	<table class="{$module.value1}">
	<tr class="link">
			<th>{cmstext value="date"}</th>
			<th>{cmstext value="name"}</th>
			<th>{cmstext value="user"}</th>
		</tr>
	{foreach from=$pages item=page}
		<tr class="link">
			<td class="date">{$page.orderdate|replace:" ":"&nbsp;"}</td>
			<td>{if $page.backendurl}<a href='{$page.backendurl}'>{/if}{$page.name}{if $page.backendurl}</a>{/if}</td>
			<td>{$page.user}</td>
		</tr>
	{/foreach}
	</table>
	<div class="boxItemFooter"></div>
</div><!--//End mBoxItem-->
