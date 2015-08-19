{foreach item=item name=item from=$fields}
<tr>
	<td colspan="2">
		<h2 >
			<table class="tuksiTableStyle" border=0 width="100%">
			<tr>
				<td >Feltnavn = {$item.colname} ({$item.id})<a name="cmsfieldtypeid_{$item.colname}"></a></td>
				<td width="56">
{if $smarty.foreach.item.first}
					<span class="mini_up_down_spacer"></span>
{else}
					<a onclick="document.forms[0].fieldid_{$item.id}_up.value=1; saveData('cmsfieldtypeid_{$item.colname}'); return false" class="mini_button" href="#{$item.id}" title="#"><img src="/themes/default/images/icons/ic_arrowUp.png"></a>
{/if}
{if $smarty.foreach.item.last}
					<span class="mini_up_down_spacer"></span>
{else}
					<a onclick="document.forms[0].fieldid_{$item.id}_down.value=1; saveData({$fieldid}); return false" class="mini_button" href="#{$row.id}" title="#"><img src="/themes/default/images/icons/ic_arrowDown.png"></a>
{/if}
			
					<input name="fieldid_{$item.id}_up" type="hidden">
					<input name="fieldid_{$item.id}_down" type="hidden">
				</td>

			</tr>
			</table>
		</h2>
	</td>
</tr>
 {if $item.itemtype != 'table'}
<tr>
	<td colspan="2">
	</td>
</tr>
{/if}

<tr>
<td valign="top" width="500">
	<table class="tuksiTableStyle">
	<tr>
		<td width="100" valign="top"><label class="leftLabel">Element navn:</label> </td>
		<td width="400" valign="top" colspan="3">
			<input class="text" name="name_{$item.colname}"  value="{$item.name}" id="name_{$item.colname}">
			<div id="tokens_{$item.id}" class="autocomplete"></div>
			<script>
			document.observe('dom:loaded' , function() {ldelim}
				new Ajax.Autocompleter('name_{$item.colname}' , 'tokens_{$item.id}' , '/services/ajax/cmstext.php',{ldelim}paramName:'token',select: 'testtest'{rdelim} );
			 {rdelim});
			</script>
		<br>
		</td>
	</tr>
{if 1==2}	
	<tr>
		<td  valign="top"><label class="leftLabel">Vis ved oprettelse:</label> </td>
		<td valign="top" colspan="3"><input type="checkbox" class="checkbox" name="showonnew_{$item.colname}"  {$item.onnew}></td>
	</tr>
	{/if}
	<tr>
		<td valign="top" ><label class="leftLabel">Felttype:</label></td>
		<td valign="top" colspan="3">
			<select onchange="saveData('cmsfieldtypeid_{$item.colname}');" name="cmsfieldtypeid_{$item.colname}" >
				{html_options options=$item.fieldtypes_options selected=$item.fieldtypes_selected}
			</select>
		{if $item.fieldtypedescription}
		<br>
		{$item.fieldtypedescription}
		{/if}</td>
	</tr>
	<tr>
		<td valign="top" ><label class="leftLabel">Vis i rediger:</label></td>
		<td valign="top" colspan="3"><input type="checkbox" class="checkbox" name="showin_edit_{$item.colname}" {if $item.showin_edit}checked{/if}></td>
	</tr>
	<tr>
		<td valign="top" ><label class="leftLabel">Vis i indstillinger:</label></td>
		<td valign="top" colspan="3"><input type="checkbox" class="checkbox" name="showin_settings_{$item.colname}" {if $item.showin_settings}checked{/if}></td>
	</tr>
	{if $item.fieldtypes_selected}
	{if $item.fieldvalue1desc || $item.fieldvalue1}
	<tr>
		<td valign="top"><label class="leftLabel">Fieldvalue 1:</label></td>
		<td valign="top" width="400" colspan="3"><input class="text" name="fieldvalue1_{$item.colname}"  value="{$item.fieldvalue1}">
		<i>{$item.fieldvalue1desc}</i></td>
	</tr>
	{/if}
	{if $item.fieldvalue2desc || $item.fieldvalue2}
	<tr>
		<td valign="top"><label class="leftLabel">Fieldvalue 2:</label></td>
		<td width="400"> <input class="text" name="fieldvalue2_{$item.colname}"  value="{$item.fieldvalue2}">
		<i>{$item.fieldvalue2desc}</i></td>
	</tr>
	{/if}
	{if $item.fieldvalue3desc || $item.fieldvalue3}
	<tr>
		<td valign="top"><label class="leftLabel">Fieldvalue 3:</label></td>
		<td width="400" ><input class="text" name="fieldvalue3_{$item.colname}"  value="{$item.fieldvalue3}">
		<i>{$item.fieldvalue3desc}</i></td>
	</tr>
	{/if}
	{if $item.fieldvalue4desc || $item.fieldvalue4}
	<tr>
		<td valign="top"><label class="leftLabel">Fieldvalue 4:</label></td>
		<td width="400" ><input class="text" name="fieldvalue4_{$item.colname}"  value="{$item.fieldvalue4}">
		<i>{$item.fieldvalue4desc}</i></td>
	</tr>
	{/if}
	{if $item.fieldvalue5desc || $item.fieldvalue5}
	<tr>
		<td valign="top"><label class="leftLabel">Fieldvalue 5:</label></td>
		<td width="400" ><input class="text" name="fieldvalue5_{$item.colname}"  value="{$item.fieldvalue5}">
		<i>{$item.fieldvalue5desc}</i></td>
	</tr>

	{/if}
	{/if}
	{if $item.customsetup}
	<tr>
		<td valign="top">{$item.customsetup.name}</td>
		<td width="400" >{$item.customsetup.html}</td>
	</tr>
	{/if}
	{if $item.extrafieldvalues}
		
		{foreach item=fieldvalue name=fieldvalue from=$item.extrafieldvalues}
		<tr>
		<td valign="top"><label class="leftLabel">{$fieldvalue.name}</label></td>
		<td>
		<input name="{$fieldvalue.type}extrafieldvalues_{$item.id}_{$fieldvalue.id}_varid" type="hidden" value="{$fieldvalue.id}">
		<input class="text" name="{$fieldvalue.type}extrafieldvalues_{$item.id}_{$fieldvalue.id}_value"  value="{$fieldvalue.value}"><br>
		{$fieldvalue.description}</td>
		</tr>
		{/foreach}
		{/if}
	
	</table>
</td>
<td valign="top">
	<table class="tuksiTableStyle" valign="left" border=0>
	{if $item.itemtype == 'table'}
	<tr>		
		<td valign="top" width="70"><label class="leftLabel">{cmstext value="width_in_list"}</label></td>
		<td valign="top" align="left"><input class="text smallInput"  name="listcolwidth_{$item.colname}" value="{$item.listcolwidth}"></td>
	</tr>
	<tr>
		<td valign="top"><label class="leftLabel">Align i listen</label></td>
		<td valign="top"><select class="smallSelect" name="listcolalign_{$item.colname}">
		<option value=""></option> 
		<option value="left" {if $item.listcolalign == "left"}selected="selected"{/if}>Left</option> 
		<option value="center" {if $item.listcolalign == "center"}selected="selected"{/if}>Center</option> 
		<option value="right" {if $item.listcolalign == "right"}selected="selected"{/if}>Right</option> 
		</select></td>
	</tr>
	{/if}
	<tr>
		<td valign="top" colspan="2" width="20">
		<table class="tuksiTableStyle" border=0>
		<tr>
			<td><label class="leftLabel">Rettigheder:</label></td>
			<td><label class="labelLightTop">Se</label></td>	
			<td><label class="labelLightTop">Liste</label></td>	
			<td><label class="labelLightTop">Gem</label></td>	
			<td><label class="labelLightTop">Admin</label></td>	
			<td><label class="labelLightTop">Slet</label></td>	
			<td><label class="labelLightTop">Tilføj</label></td>	
		</tr>
		{foreach item=item_right name=item_right from=$item.rights_options}
		<tr>
			<td><label class="labelLightLeft">{$item_right.name}</label></td>
			<td><input name="right_read_{$item_right.id}"  {if $item_right.pread} checked="checked"{/if} type="checkbox" class="checkbox"></td>
			<td><input name="right_list_{$item_right.id}"  {if $item_right.plist} checked="checked"{/if} type="checkbox" class="checkbox"></td>
			<td><input name="right_save_{$item_right.id}"  {if $item_right.psave} checked="checked"{/if} type="checkbox" class="checkbox"></td>
			<td><input name="right_admin_{$item_right.id}"  {if $item_right.padmin} checked="checked"{/if} type="checkbox" class="checkbox"></td>
			<td><input name="right_delete_{$item_right.id}"  {if $item_right.pdelete} checked="checked"{/if} type="checkbox" class="checkbox"></td>
			<td><input name="right_add_{$item_right.id}"  {if $item_right.padd} checked="checked"{/if} type="checkbox" class="checkbox"></td>
		</tr>
		{/foreach}
		</table>
		</td>
		{*<!--<td valign="top">Rækkefølge:</td>
		<td valign="top"><input  name="seq_{$item.colname}" value="{$item.seq}"></td>-->*}
	</tr>
		
</table>
{/foreach}


