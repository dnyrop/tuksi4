<table class="moduleElementRow">
	<tbody>
		<tr>
			<td><label>{cmstext value="toemail"}:</label></td>
			</tr>
			<tr>
			<td colspan="2">
				<input value="{$emailto}" name="sendsinglenewslettertoemail{$id}" id="sendsinglenewslettertoemail{$id}" class="text" type="text" style="width:300px;">
			</td>
		</tr>
	</tbody>
</table>
<table class="moduleElementRow" align="right">
	<tbody>
		<tr>
			<td><a onclick="sendSingleNewsletter();return false;" class="buttonType3 iconPositive" href="#"><span><span>{cmstext value="btnsend"}</span></span></a></td>
			<td><a onclick="tuksi.window.close('#{ldelim}id{rdelim}');return false;" class="buttonType3 iconNegative" href="#"><span><span>{cmstext value="btncancel"}</span></span></a></td>
		</tr>
	</tbody>
</table>
