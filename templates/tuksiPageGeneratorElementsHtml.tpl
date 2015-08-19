{if !$singelmodule || $editmodule}
<input type="hidden" name="openselected" id="openselected" value="" />
<input type="hidden" name="closeselected" id="closeselected" value="" />
<input type="hidden" name="sortorder" id="sortorder" value="" />
<input type="hidden" name="deletemodule" id="deletemodule" value="" />
<input type="hidden" name="newmodule" id ="newmodule" value="0" />
<input type="hidden" name="newmodule_tmp" id ="newmodule_tmp" value="0" />
<input type="hidden" name="newmoduleplacement" id ="newmoduleplacement" value="0" />
<input type="hidden" name="newmoduleplacement_tmp" id ="newmoduleplacement_tmp" value="0" />
<input type="hidden" name="activatemodule" id ="activatemodule" value="0" />
<input type="hidden" name="deactivatemodule" id ="deactivatemodule" value="0" />
<input type="hidden" name="releasemodule" id ="releasemodule" value="0" />
<input type="hidden" name="opento" id ="opento" value="{$opento}" />
<input type="hidden" name="savemodulearrange" id ="savemodulearrange" value="" />
{/if}
{if !$singelmodule}
<div class="actionButtonsRow">
	<ul>
		{if $modules}
		<li><input type="checkbox" class="checkbox" onclick="tuksi.pagegenerator.toggleSelectedModules(this);" /></li>
		<li><a href="#" onclick="tuksi.pagegenerator.openSelectedModules(); return false;" class="buttonType3 itemOpen"><span><span>{cmstext value="open_selected"}</span></span></a></li>
		<li><a href="#" onclick="tuksi.pagegenerator.closeSelectedModules(); return false;" class="buttonType3 itemClose"><span><span>{cmstext value="close_selected"}</span></span></a></li>
		<li><a href="#" class="buttonType3 itemDelete" onclick="tuksi.pagegenerator.deleteSelectedModulesDialog();  return false;"><span><span>{cmstext value="delete_selected"}</span></span></a></li>
		<li><a href="#" class="buttonType3 itemArrange" onclick="tuksi.pagegenerator.arrangeModulesDialog('{$page.treeid}','{$page.tabid}','{$areaid}','{cmstext value=arrange_modules}');  return false;"><span><span>{cmstext value="arrange"}</span></span></a></li>
		{/if}
		<li class="positionRight"><a href="#" class="buttonType3 itemAdd" onclick="tuksi.pagegenerator.addModuleDialog(); return false;"><span><span>{cmstext value="add_new_element"}</span></span></a></li>
	</ul>
</div><!--//End actionButtonsRow (TOP)-->
<div id='moduleContainer'>
{/if}
{foreach from=$modules item=module name=modules}
{if !$singelmodule || $editmodule}
	<input type="hidden" name="TABLE_{$module.id}_op" />
	<input type="hidden" name="TABLE_{$module.id}_ned" />
	<input type="hidden" id="module_isopen_{$module.id}" name="module_isopen[]" value="{if $module.isopen}{$module.id}{/if}" />
	<input type="hidden" id="module_isopen_{$module.id}_old" name="module_isopen_{$module.id}_old" value="{$module.isopen}" />
	<input type="hidden" name="TABLE_{$module.id}_deleterow" />
	<input type="hidden" name="TABLE_{$module.id}_moverow" />
{/if}	
{if !$singelmodule}
	<div class="contentItemRow">
	{*<div class="contentItemRow{if !$module.placement} moveable{/if}" id="module_{$module.id}">*}
		<div class="itemHeader">
		{*<div class="itemHeader{if !$module.placement} moveable{/if}" {if !$module.placement}style="cursor:move;"{/if} >*}
			<ul>
				<li>
					<a name="gotomodule_{$module.id}"></a>
					<input type="checkbox" id="module_selected[]" name="module_selected[]" value="{$module.id}" class="checkbox" />
				</li>
				<li><h6 onclick="document.getElementById('module_isopen_{$module.id}').value = '{if !$module.isopen}{$module.id}{/if}';{if !$module.isopen}$('opento').value = '{$module.id}';{/if}return openModule({$page.treeid}, {$page.tabid}, {$areaid}, {$module.id});" {if $module.isopen}class="active"{/if}>{$module.modname}</h6></li>
				<li class="positionRight">
					<input type="hidden" name="setupLink_{$module.id}" id="setupLink_{$module.id}" value="{$module.setuplink}">
					<select onchange="tuksi.pagegenerator.doActionOnModule('{$module.id}',this,'{$module.modname}');" id="moduleActionSelect_{$module.id}">
						<option selected>{cmstext value="choose_action"}</option>
						{if !$module.not_delete}
						<option value="delete">{cmstext value="delete"}</option>
						{/if}
						{if $module.isactive}
						<option value="hide">{cmstext value="hide"}</option>
						{else}
						<option value="show">{cmstext value="show"}</option>
						{/if}
						{if $module.setuplink}
						<option value="setup">{cmstext value="setup"}</option>
						{/if}
						{if $module.release}
						<option value="release">{cmstext value="release"}</option>
						{/if}
					</select>
				</li>
				<li class="positionRight"><label>{cmstext value="status"}: <span class="{if $module.isactive}colorPositive{else}colorNegative{/if}">{if $module.isactive}{cmstext value=visible}{else}{cmstext value=hidden}{/if}</span></label></li>
			</ul>
		</div><!--//End itemHeader-->
		{/if}
		{if $module.isopen}
		<div class="itemContent" id="module_content_{$module.id}">
		{foreach from=$module.fields item=field name=fields}
		{if $field.options.fullwidth}
			{if $field.name}	
				<table class="moduleElementRow">
					<tr>
						<td class="column1"><label>{$field.name}:</label></td>
					</tr>
				</table>
			{/if}
			<table class="moduleElementRow" width="90%">
				<tr>
					{foreach from=$field.html item=h}
						<td>{$h}</td>
					{/foreach}
				</tr>
			</table>
		{elseif $field.name}
		<table class="moduleElementRow"{if $editmodule && $field.colname == "isactive"}{* KCH *} id="module_isactive_input"{/if}>
			<tr>
				<td class="column1"><label>{$field.name}:</label></td>
				{foreach from=$field.html item=h}
					<td>{$h}</td>
				{/foreach}
			</tr>
		</table>
		{elseif $field.wide}
		<table class="moduleElementRow">
			<tr>
				<td class="column1"><label>{$field.name}:</label></td>
			</tr>
		</table>
			{foreach from=$field.html item=h}
					{$h}
				{/foreach}
		{else}
			{foreach from=$field.html item=h}
					<table class="moduleElementRow">
					<tr>
					<td class="column1">{$h}</td>
					</tr>
					</table>
				{/foreach}
		{/if}
	{/foreach}
	</div>
		{/if}
	</div><!--//End contentItemRow-->
{/foreach}
{if !$singelmodule}
</div>
{if $moveable}
<script type="text/javascript">
{literal}
Position.includeScrollOffsets = true;
Sortable.create('moduleContainer',{
	tag:'div',
	onUpdate:tuksi.pagegenerator.updateSortorder,
	only:'moveable',
	'scroll':'scrollFrame'
});
{/literal}
</script>
{/if}
<div id='newModuleContent' style="display:none">
{if $allowedmodules}
<table class="moduleElementRow">
	<tbody>
		<tr>
			<td><label>{cmstext value="module_type"}:</label></td>
			</tr>
			<tr>
			<td>
				<select name="newmoduleSelecter" onchange="tuksi.pagegenerator.updateNewModule(this);">
					<option value="">{cmstext value="choose_module"}</option>
					{foreach from=$allowedmodules item=m}
					<option value="{$m.id}">{$m.name}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td class="column1"><label>{cmstext value="placement"}:</label></td>
		</tr>
		<tr>
			<td>
				<select name="newmodulePlacement" onchange="tuksi.pagegenerator.updateNewModulePlacement(this);">
					<option value="first">{cmstext value="first"}</option>
					<option value="last" selected>{cmstext value="last"}</option>
				</select>
			</td>
		</tr>
	</tbody>
</table>
<table class="moduleElementRow" align="right">
	<tbody>
		<tr>
			<td><a onclick="tuksi.pagegenerator.addModule();return false;" class="buttonType3 iconPositive" href="#"><span><span>{cmstext value="insert"}</span></span></a></td>
			<td><a onclick="tuksi.window.close('#{ldelim}id{rdelim}');return false;" class="buttonType3 iconNegative" href="#"><span><span>{cmstext value="cancel"}</span></span></a></td>
		</tr>
	</tbody>
</table>
{else}
{/if}
</div>
<script>
var gotohref = "{$opento}";
{literal}
if(gotohref) {
	document.location.hash = 'gotomodule_' + gotohref;
}
{/literal}
</script>

<!--	
{if $setup}
		<a href="#" onclick="document.forms[0].TABLE_module_setup.value='{$module.id}';saveData(); return false;" class="mini_button"><span class="mini_bn_st"></span><span>Setup</span><span class="mini_bn_end"></span></a>
	{/if}
	{if $module.move}
	<a href="#" onclick="document.tuksiForm.module_isopen_{$module.id}.value =1; document.tuksiForm.TABLE_{$module.id}_moverow.value=1;saveData(); return false;" class="mini_button"><span class="mini_bn_st"></span><span>{cmstext value=flyt_modul}</span><span class="mini_bn_end"></span></a>
	{/if}
	{if $module.delete}
		<a href="#" onclick="if (confirm('{cmstext value=slet_modul}?')) {ldelim} document.forms[0].TABLE_{$module.id}_deleterow.value=1;saveData(); return false;{rdelim}" class="mini_button"><span class="mini_bn_st"></span><span>{cmstext value=slet_modul}</span><span class="mini_bn_end"></span></a>
	{/if}
	{if $module.arrowUp}
		<a href="#" onclick="document.forms[0].TABLE_{$module.id}_op.value=1;saveData(); return false;" class="mini_button"><span class="mini_bn_st"></span><span class="mini_a_up"></span><span class="mini_bn_end"></span></a>
	{else}
		<span class="mini_up_down_spacer"></span>
	{/if}
	{if $module.arrowDown}
		<a href="#" onclick="document.forms[0].TABLE_{$module.id}_ned.value=1;saveData(); return false;" class="mini_button"><span class="mini_bn_st"></span><span class="mini_a_down"></span><span class="mini_bn_end"></span></a>
	{else}
		<span class="mini_up_down_spacer"></span>
	{/if}-->
{/if}
