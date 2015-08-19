<table width="439" border="0" cellspacing="0" cellpadding="0" class="tilewhite">
	<tr>
		<td colspan="3" height="10"></td>
	</tr>
	<tr>
		<td width="16"></td>
		<td colspan="2">
		{if !$showResult}
			<form action="" name="" method="POST">
				<span>Afstemning</span>
				<table>
					<tr>
						<td colspan="2">
						{$pollInfo.question}
						</td>
					</tr>
				{foreach from=$poll name=poll item=q}
					<tr>				
						<td><input type="radio" id="question_{$q.id}" name="question" value="{$q.id}"></td>
						<td><label for="question_{$q.id}">{$q.name}</label></td>				
					</tr>
				{/foreach}
					<tr>
						<td colspan="2"><input type="submit" value="Stem" id="submit" name="pollSubmit_{$pollid}"></td>					
					</tr>			
				</table>		
			</form>
		{else} 
			<form action="" name="" method="">
			<span> Resultat</span>
			<table>
				<tr>
					<td>
					{$pollInfo.name}
					</td>
					<td></td>
				</tr>
			{foreach from=$votes item=v name=votes}
				<tr>				
					<td>{$v.name}</td>
					<td></td>
								
				</tr>
				<tr>				
					<td>
						{if $v.percent > 0}
							<img src="/images/graphics/staf_blue.gif" width="{$v.width}" height="10"}>
						{/if}
						{$v.percent}% ({$v.votes})
						</td>
					<td></td>	
				</tr>
			{/foreach}
			</table>	
			Stemmer i alt: {$pollInfo.votes}	
		</form>
	{/if}
		</td>
	</tr>
	<tr>
		<td colspan="3" height="15"></td>
	</tr>
</table>