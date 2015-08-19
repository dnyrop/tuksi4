<table>
	<tr>
		<th class="align_left"><span>{cmstext value=date}</span></th>
		<th class="align_left"><span>{cmstext value=event}</span></th>
		<th class="align_left"><span>{cmstext value=user}</span></th>
	</tr>
	{foreach from=$log name=log item=log}
	<tr class="link {cycle values=",even"}">
		<td>{$log.dateadded}</td>
		<td>{$log.eventname}</td>
		<td>{$log.username}</td>
	</tr>
	{/foreach}
</table>
