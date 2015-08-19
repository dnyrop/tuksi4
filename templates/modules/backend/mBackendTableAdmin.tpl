<input type="hidden" value="{$currentDB}" name="currentDB" id="currentDB">
<table class="tuksiTableStyle">
	<tr>
		<td>
			<select name="db" id="db" onchange="doAction('UPDATE');">
				{foreach from=$databases item=d}
				<option value="{$d}" {if $d == $currentDB}selected="selected"{/if}>{$d}</option>
				{/foreach}
			</select>
		</td>
		{if $tables}
		<td>
			<select name="table" id="table" onchange="doAction('UPDATE');">
				{foreach from=$tables item=t}
				<option value="{$t}" {if $t == $currentTable}selected="selected"{/if}>{$t}</option>
				{/foreach}
			</select>
		</td>
		{/if}
		{if $layouts}
		<td>
			<select name="layout" id="layout" onchange="doAction('UPDATE');">
				{foreach from=$layouts item=l}
				<option value="{$l.id}" {if $l.selected}selected="selected"{/if}>{$l.name}</option>
				{/foreach}
			</select>
		</td>
		{/if}
		<td>
			<label for="name">Navn:</label>
			<input type="text" class="text mediumInput" name="name" id="name" value="{$layoutname}" />
		</td>
	</tr>
</table>
{$fields}

{*<!-- -->
<!--<table class="tuksiTableStyle">
	<tr>
		<td>Name</td>
		<td>System name</td>
		<td>Type</td>
	</tr>
	{foreach from=$elements item=e}
	<tr>
		<td>{$e.name}</td>
		<td>{$e.lol}</td>
		<td>{$e.type}</td>
	</tr>
	{/foreach}
</table>-->*}