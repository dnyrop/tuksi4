{if $arrStats}
<div class="mListView">
{foreach from=$arrStats item=stat}
	{if $stat.headline}<h6>{$stat.headline}</h6>{/if}
	{foreach from=$stat.stats item=linkstat}
	{if $linkstat.name}<strong style="padding-left:10px;">{$linkstat.name}</strong>{/if}
	<table>
		<tr>
			<th><span>{cmstext value="date"}</span></th>
			<th><span>{cmstext value="nb_click"}</span></th>
			<th></th>
		</tr>	
		{foreach from=$linkstat.lines item=line}
		<tr class="link {cycle values=",even"}">
			<td width="40">{$line.date}</td>
			<td width="20">{$line.nb}</td>
			<td class="graphCell"><div class="graphLine" style="width:{$line.width}px;"></div></td>
		</tr>
		{/foreach}
	</table>
	{/foreach}
{/foreach}
</div>
{else}
<div class="mListView">
<h6>{cmstext value="newsletter_awaiting"}</h6>
<table>
	<tr>
		<th><span>{cmstext value="dispatchment_name"}</span></th>
		<th><span class="headLine">{cmstext value="newsletter"}</span></th>
		<th><span class="headLine">{cmstext value="maillist"}</span></th>
		<th><span class="headLine">{cmstext value="datetosend"}</span></th>
	</tr>
	{foreach from=$waiting item=mail name=waitingmail}
	<tr class="link {cycle values=",even"}">
		<td>{$mail.name}</td>
		<td>{$mail.newsletter}</td>
		<td>{$mail.emaillist}</td>
		<td>{$mail.dato}</td>
		<TD><ul class="buttons">
					<li>
							<a title="Slet" class="buttonType4 iconDelete" onclick="deleteNewsletterDialog('{$mail.id}','{$mail.name}'); return false;" href="#"><span/></a></TD>
					</li>
				</ul>
		</td>
	</tr>
	{/foreach}
</table>

<h6>{cmstext value="newsletter_sent"}</h6>
<table>
	<tr>
		<th><span>{cmstext value="dispatchment_name"}</span></th>
		<th><span class="headLine">{cmstext value="newsletter"}</span></th>
		<th><span class="headLine">{cmstext value="maillist"}</span></th>
		<th><span>{cmstext value="datesent"}</span></th>
		<th><span>{cmstext value="count_sent"}</span></th>
		<th><span>{cmstext value="count_viewed"}</span></th>
	</tr>
	{foreach from=$sent item=mail name=sentmail}
	<tr class="link {cycle values=",even"}" onclick="document.location='{$mail.url}';">
		<td>{$mail.name}</td>
		<td>{$mail.newsletter}</td>
		<td>{$mail.emaillist}</td>
		<td>{$mail.dato}</td>
		<td>{$mail.sentto}</td>
		<td>{$mail.viewed}</td>
	</tr>
	{/foreach}
</table>
</div>
{/if}
<input type="hidden" name="deleteNewsletterId" id="deleteNewsletterId">
<script type="text/javascript">
{literal}
function deleteNewsletterDialog(id,name) {
	
	$('deleteNewsletterId').value = id;
	
	tuksi.window.confirm('Delete the newsletter '+name+'?',{
		title:'Delete newsletter',
		callback:function(id){deleteNewsletter(id);},
		placement:'center',
			options:{
				width:350,
				id:'deleteNewsletter'
			}
	});
}
function deleteNewsletter(id){
	doAction('DELETE');
}
{/literal}
</script>
