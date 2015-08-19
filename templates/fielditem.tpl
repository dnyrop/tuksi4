<!--start fielditem-->
{if $tpldata.itemtype == 'pg' || $tpldata.itemtype == 'option'}
<table class="tuksiTableStyle">
	<tr>
		<td>{$tpldata.nyt_element}:</td>
	</tr>
	<tr>
		<td>
			<input name="field_add" type="hidden" value="">
			<table class="tuksiTableStyle">

				{foreach item=field name=field from=$tpldata.fields}

				{if $smarty.foreach.field.iteration == 1}
				<tr>
					<td>
						<table class="tuksiTableStyle">
				{/if}
							<tr>
								<td>
									<input {if $field.used}disabled{/if} type="checkbox" class="checkbox" value="{$field.value}"  name="field_add[]" id="chk{$field.value}" ondblclick="this.checked=true; doAction('SAVE'); return false;">
								</td>
								<td>
									<label for="chk{$field.value}" title="{$field.name}" {if NOT $field.used}ondblclick="document.getElementById('chk{$field.value}').checked=true; doAction('SAVE'); return false;"{/if}>
										{if $field.used}<strike>{/if}{$field.name}{if $field.used}</strike>{/if}
									</label>
								</td>
							</tr>

						{if $smarty.foreach.field.iteration % 5 == 0 }
						</table>
					</td>
					<td>
						{if $smarty.foreach.field.iteration != $smarty.foreach.field.total}
						
						<table class="tuksiTableStyle">
						{/if}
						{/if}
						{/foreach}
						</table>
					</td>
					<td style="vertical-align: bottom;"><a href="#" onclick="doAction('SAVE'); return false;" class="buttonType3 itemAdd"><span><span>{$tpldata.btnadd}</span></span></a></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
{/if}
<table width="100%">
	<tr>
		<td align="right"><a class="buttonType3 itemArrange" onclick="showArrangewindow('{cmstext value=arrange_items}','{$setup.relationid}','{$setup.type}','{$setup.tablename}'); return false;" href="#"><span><span>{cmstext value=arrange}</span></span></a></td>
	</tr>
</table>
<table class="moduleElementRow moduleElementRowFullWidth">
{foreach item=item name=item from=$fields}
<tr>
	<td colspan="2">
		<h2>
			<table class="tuksiTableStyle" border=0 width="100%">
				<tr>
					<td >{cmstext value=fieldname}= {$item.colname} ({$item.id})<a name="cmsfieldtypeid_{$item.colname}"></a></td>
					{if $item.itemtype != 'table' && !$item.isglobal}
					<td width="130">
						<input name="fielditemid_delete_{$item.id}" type="hidden" value="0">
						<a href="#" onclick="document.tuksiForm.fielditemid_delete_{$item.id}.value = 1; doAction('SAVE'); return false;" class="buttonType3 itemDelete"><span><span>Slet element</span></span></a>
					</td>
					{/if}
					<td width="56">
						
						{if $smarty.foreach.item.first}
						<span class="mini_up_down_spacer"></span>
						{else}
						<a onclick="document.forms[0].fieldid_{$item.id}_up.value=1; saveData({$fieldid}); return false" class="mini_button" href="#{$item.id}" title="#"><img src="{$conf.path.theme}/images/icons/ic_arrowUp.png"></a>
						{/if}
						{if $smarty.foreach.item.last}
						<span class="mini_up_down_spacer"></span>
						{else}
						<a onclick="document.forms[0].fieldid_{$item.id}_down.value=1; saveData({$fieldid}); return false" class="mini_button" href="#{$row.id}" title="#"><img src="{$conf.path.theme}/images/icons/ic_arrowDown.png"></a>
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
				<td width="100"><label class="leftLabel">{cmstext value=element_name}:</label> </td>
				<td width="400">
					{$item.name_html}<br>
					<em>{cmstext value=helptext_element_name}</em>
				</td>
			</tr>
			{if $item.itemtype == 'option'}
			<tr>
				<td><label class="leftLabel">{cmstext value=showinedit}:</label></td>
				<td><input type="checkbox" class="checkbox" name="showin_edit_{$item.colname}" {if $item.showin_edit}checked{/if}></td>
			</tr>
			<tr>
				<td><label class="leftLabel">{cmstext value=showinsettings}:</label></td>
				<td><input type="checkbox" class="checkbox" name="showin_settings_{$item.colname}" {if $item.showin_settings}checked{/if}></td>
			</tr>
			{/if}
			{if 1==2}	
			<tr>
				<td><label class="leftLabel">{cmstext value=showatcreate}:</label> </td>
				<td><input type="checkbox" class="checkbox" name="showonnew_{$item.colname}"  {$item.onnew}></td>
			</tr>
			{/if}
			<tr>
				<td><label class="leftLabel">{cmstext value=help}:</label></td>
				<td><input class="text" name="helptext_{$item.colname}"  value="{$item.helptext}"><br><i>{cmstext value=showabovehtml}.</i></td>
			</tr>
			<tr>
				<td><label class="leftLabel">{cmstext value=group}:</label></td>
				<td>
					<select name="cmsfieldgroupid_{$item.colname}" >
					{html_options options=$item.fieldgroups_options selected=$item.fieldgroups_selected}							
					</select>
				</td>
			</tr>
			<tr>
				<td><label class="leftLabel">{cmstext value=fieldtype}:</label></td>
				<td>
					<select onchange="saveData('cmsfieldtypeid_{$item.colname}');" name="cmsfieldtypeid_{$item.colname}" >
					{html_options options=$item.fieldtypes_options selected=$item.fieldtypes_selected}
					</select>
					{if $item.fieldtypedescription}
					<br>
					{$item.fieldtypedescription}
					{/if}
				</td>
			</tr>			
			{if $item.fieldtypes_selected}
			{if $item.fieldvalue1desc || $item.fieldvalue1}
			<tr>
				<td><label class="leftLabel">{cmstext value=fieldvalue} 1:</label></td>
				<td width="400"><input class="text" name="fieldvalue1_{$item.colname}"  value="{$item.fieldvalue1}">
					<i>{$item.fieldvalue1desc}</i>
				</td>
			</tr>
			{/if}
			{if $item.fieldvalue2desc || $item.fieldvalue2}
			<tr>
				<td><label class="leftLabel">{cmstext value=fieldvalue} 2:</label></td>
				<td width="400"> <input class="text" name="fieldvalue2_{$item.colname}"  value="{$item.fieldvalue2}">
					<i>{$item.fieldvalue2desc}</i>
				</td>
			</tr>
			{/if}
			{if $item.fieldvalue3desc || $item.fieldvalue3}
			<tr>
				<td><label class="leftLabel">{cmstext value=fieldvalue} 3:</label></td>
				<td width="400" ><input class="text" name="fieldvalue3_{$item.colname}"  value="{$item.fieldvalue3}">
					<i>{$item.fieldvalue3desc}</i>
				</td>
			</tr>
			{/if}
			{if $item.fieldvalue4desc || $item.fieldvalue4}
			<tr>
				<td><label class="leftLabel">{cmstext value=fieldvalue} 4:</label></td>
				<td width="400" ><input class="text" name="fieldvalue4_{$item.colname}"  value="{$item.fieldvalue4}">
					<i>{$item.fieldvalue4desc}</i>
				</td>
			</tr>
			{/if}
			{if $item.fieldvalue5desc || $item.fieldvalue5}
			<tr>
				<td><label class="leftLabel">{cmstext value=fieldvalue} 5:</label></td>
				<td width="400" ><input class="text" name="fieldvalue5_{$item.colname}"  value="{$item.fieldvalue5}">
					<i>{$item.fieldvalue5desc}</i>
				</td>
			</tr>
			{/if}
			{/if}
			{if $item.customsetup}
			<tr>
				<td>{$item.customsetup.name}</td>
				<td width="400" >{$item.customsetup.html}</td>
			</tr>
			{/if}
			{if $item.extrafieldvalues}
			{foreach item=fieldvalue name=fieldvalue from=$item.extrafieldvalues}
			<tr>
				<td><label class="leftLabel">{$fieldvalue.name|replace:'_':' '|regex_replace:"/(\\d+)/":" \$1"|lower|capitalize:true}:</label></td>
				<td width="400">
					<input class="text" name="{$fieldvalue.type}extrafieldvalues_{$item.id}_{$fieldvalue.id}_value"  value="{$fieldvalue.value}">
					<i>{$fieldvalue.description}</i>
				</td>
			</tr>
			{/foreach}
			{/if}
		</table>
	</td>
	<td valign="top" align="right">
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
						<th class="labelLightTop">{cmstext value=rights}:</th>
						<th>{cmstext value=right_see}</th>	
						<th>{cmstext value=right_list}</th>	
						<th>{cmstext value=right_save}</th>	
						<th>{cmstext value=right_admin}</th>	
						<th>{cmstext value=right_delete}</th>	
						<th>{cmstext value=right_add}</th>	
					</tr>
					{foreach item=item_right name=item_right from=$item.rights_options}
					<tr>
						<td>{$item_right.name}</td>
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
			</tr>
		</table>
		{if $item.itemtype == 'option'}
		<strong style="margin-top:4px;">{cmstext value=aplliestotemplate}:</strong> <br />
		<select multiple name="option_template_{$item.colname}[]" style="width:270px;margin-top:4px;" size="6">
		{foreach from=$item.option_template item=used}
			<option value="{$used.id}"  {if $used.checked}selected="selected"{/if}>{$used.name}</option>
		{/foreach}		
		</select>
		{/if}
	</td>	
</tr>	
{/foreach}
</table>
<!--end fielditem-->
