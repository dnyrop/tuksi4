	<!-- start: headline -->
<tr>
	<td width="535">
		<table border="0" cellpadding="0" cellspacing="0" width="535">
		<tr>
		</tr>
		<tr>
			<td><h1>{$module.headline}</h1></td>
		</tr>
		<tr>
		</tr>
		</table>
	</td>
</tr>
<!-- end: headline -->
<!-- start: textonly  -->
<tr>
	<td width="535">
		<p>{$module.content}
		{if $link}
		<br><br>
			<a href="{$trackingurl}">Læs mere</a>
		{/if}
		</p>
	</td>
</tr>
<tr><td height="15"></td></tr>
<!-- end: textonly  -->