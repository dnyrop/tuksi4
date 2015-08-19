{$startHtml}

<div id="fieldLink_{$htmltagname}">
	{cmstext value='choose_link_type'}:<br>
	<select name="{$htmltagname}_type" onchange="tuksi.fieldLink.onchange('{$htmltagname}_type')" id="{$htmltagname}_type">
	{if $showlinktype.cmstree}
		<option {if $selected.type == 'cmstree'}selected {/if} value="cmstree">{cmstext value='internal_page'}</option>
	{/if}
	{if $showlinktype.extern}
		<option {if $selected.type == 'extern'}selected {/if} value="extern">{cmstext value='external_page'}</option>
	{/if}
	{if $showlinktype.relative}
		<option {if $selected.type == 'relative'}selected {/if} value="relative">{cmstext value='relative_page'}</option>
	{/if}
	{if $showlinktype.mail}
		<option {if $selected.type == 'mail'}selected {/if} value="mail">{cmstext value='email'}</option>
	{/if}
	{if $showlinktype.file}
		<option {if $selected.type == 'file'}selected {/if} value="file">{cmstext value='file'}</option>
	{/if}
	</select>
	<br /><br />
	{if $showlinktype.cmstree}
	<table id="{$htmltagname}_cmstree"{if $selected.type != 'cmstree'} style="display:none"{/if}>
		<tr>
			<td width="80">{cmstext value='internal_link'}:</td>
			<td>
				<select name="{$htmltagname}_cmstree" id="{$htmltagname}_cmstree_select">
				{foreach from=$value.cmstree.pages item=page name=page}	
					<option value="{$page.value}" {if $page.selected}selected{/if}>{if $page.value eq ''}-- {cmstext value="`$page.name`"} --{else}{$page.name}{/if}</option>
				{/foreach}
				</select>
				<br />
				<select name="{$htmltagname}_cmstree_target" id="{$htmltagname}_cmstree_target" style="margin-bottom: 5px;">
					<option {if $value.cmstree.target == ''}selected{/if} value="">Samme vindue</option>
					<option {if $value.cmstree.target == 'new'}selected{/if} value="_blank">Nyt vindue</option>
				</select>
			</td>
		</tr>
	</table>
	{/if}
	{if $showlinktype.extern}
	<table id="{$htmltagname}_extern"{if $selected.type != 'extern'} style="display:none"{/if}>
		<tr>
			<td>{cmstext value='external_link'}:</td>
			<td>
				<select name="{$htmltagname}_extern_protocol" id="{$htmltagname}_extern_protocol" style="margin-bottom: 5px;">
					<option {if $value.extern.protocol == 'http'}selected{/if} value="http">http://</option>
					<option {if $value.extern.protocol == 'https'}selected{/if} value="https">https://</option>
					<option {if $value.extern.protocol == 'ftp'}selected{/if} value="ftp">ftp://</option>
				</select>
				<br />
				<input class="text" type="text" value="{$value.extern.link}" id="{$htmltagname}_extern_link" name="{$htmltagname}_extern_link" style="margin-bottom: 5px;"><br>
				<select name="{$htmltagname}_extern_target" id="{$htmltagname}_extern_target" style="margin-bottom: 5px;">
					<option {if $value.extern.target == ''}selected{/if} value="">{cmstext value='same_window'}</option>
					<option {if $value.extern.target == 'new'}selected{/if} value="_blank">{cmstext value='new_window'}</option>
				</select>
			</td>
		</tr>
	</table>
	{/if}
	{if $showlinktype.relative}
	<table id="{$htmltagname}_relative"{if $selected.type != 'relative'} style="display:none"{/if}>
		<tr>
			<td>{cmstext value='relative_link'}:</td>
			<td>
				<input class="text" type="text" value="{$value.relative.link}" id="{$htmltagname}_relative_link" name="{$htmltagname}_relative_link" style="margin-bottom: 5px;"><br>
				<select name="{$htmltagname}_relative_target" id="{$htmltagname}_relative_target" style="margin-bottom: 5px;">
					<option {if $value.relative.target == ''}selected{/if} value="">{cmstext value='same_window'}</option>
					<option {if $value.relative.target == 'new'}selected{/if} value="_blank">{cmstext value='new_window'}</option>
				</select>
			</td>
		</tr>
	</table>
	{/if}
	{if $showlinktype.mail}
	<table id="{$htmltagname}_mail"{if $selected.type != 'mail'} style="display:none"{/if}>
		<tr id="mail" tyle="display:{if $selected.type == 'mail'}block{else}none{/if}">
			<td width="80">{cmstext value='email'}:</td>
			<td>
				<input class="text" type="text" value="{$value.mail.email}" name="{$htmltagname}_mail_input" id="{$htmltagname}_mail_input">
			</td>
		</tr>
	</table>
	{/if}
	{if $showlinktype.file}
	<table id="{$htmltagname}_file"{if $selected.type != 'file'} style="display:none"{/if}>
		<tr>
			<td>
				{$value.file.html}
			</td>
		</tr>
	</table>
	{/if}
</div>
