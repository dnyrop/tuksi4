<table class="moduleElementRow">
	<tbody>
		<tr>
			<td colspan="2">{cmstext value=releasepopup}</td>
			</tr>
			<tr>
			<tr>
			<td colspan="2"><input type="checkbox" name="releasePageSubpages{$id}" id="releasePageSubpages{$id}" /> {cmstext value=release_subpages}</td>
		</tr>
		</tr>
	</tbody>
</table>
<table class="moduleElementRow" align="right">
	<tbody>
		<tr>
			<td><a onclick="tuksi.pagegenerator.releasePage();return false;" class="buttonType3 iconPositive" href="#"><span><span>{cmstext value=btnreleasepage}</span></span></a></td>
			<td><a onclick="tuksi.window.close('#{ldelim}id{rdelim}');return false;" class="buttonType3 iconNegative" href="#"><span><span>{cmstext value=btncancel}</span></span></a></td>
		</tr>
	</tbody>
</table>