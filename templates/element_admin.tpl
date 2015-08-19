<input type="hidden" name="element_setup" value="1">
<!-- Start: element_admin -->
<h6>Tilføj nyt element</h6>

<table>
{if $error_add}

<tr>
	<td width="100"></td>
	<td colspan="2"><span class="red">{$error_add}</span></td>
</tr>

{/if}
<tr>
	<td width="100">Variable navn<br>
	<i>Bruges i frontend</i>:</td>
	<td><input name="field_add_name" type="text" class="tableform200"></td>
	<td>
	<a class="buttonType3 itemAdd" onclick=" doAction('SAVE'); return false;" href="#"><span><span>{cmstext value=add}</span></span></a>
	</td>
</tr>
</table>
<h6>Tilføj elementer fra anden node</h6>
<table>
<tr>
	<td colspan="2"></td>
</tr>
<tr>
	<td width="100" valign="top">Kopier fra</td>
	<td >
	<select name="ID" class="forminput600" onchange='document.location = "/modules/tables/elements_admin.php{$querystring}&COPYTREEID=" + document.tablesform.ID.options[document.tablesform.ID.selectedIndex].value;'>
	<option selected value=0>Ingen node valgt</option>
	{foreach item=options name=options from=$node_options}
		<option {$options.selected} value={$options.value}>{$options.name}</option>
		{/foreach}
	</select>
	</td>
</tr>
{if $elements_options}
<tr>
	<td class="formtext" width="100" valign="top">Elementer fundet</td>
	<td class="formtext" colspan="6" valign=\"top\"><!--<select class="forminput600" name="COPYELEMENT[]" MULTIPLE style="HEIGHT:97px;">{foreach item=options name=options from=$elements_options}
		<option {$options.selected} value={$options.value}>{$options.name}</option>
		{/foreach}
		
		</select>-->
	{foreach item=options name=options from=$elements_options}
		<input class="formcheckbox" name="COPYELEMENT[]" value="{$options.value}" type="checkbox">{$options.name}<br>
		{/foreach}
		<br>
		<a href="#" onclick=" pressButton('SAVE'); return false;" class="mini_button"><span class="mini_bn_st"></span><span>Tilføj</span><span class="mini_bn_end"></span></a>
		</td>
</tr>
{/if}
<tr>
	<td colspan="6">&nbsp;</td>
</tr>
</table>
<h6>Elementer</h6>
<table border="0" cellspacing="0" cellpadding="" width="100%">
{$fields}
</table>
<!-- End: element_admin -->