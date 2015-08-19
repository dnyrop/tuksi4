<div>{$starthtml}</div>
{$hiddenvalue}
<table>
<tr>
	<td>{if $img}{$altinput}{/if}</td>
</tr>
<tr>
	<td>{$btnupload}</td>
</tr>
{if $img}
{foreach from=$img item=i}
<tr>
	<td><img src="{$i.src}" alt="" /></td>
</tr>
{/foreach}
{/if}
<tr>
	<td>{$btndelete}</td>
</tr>
</table>
