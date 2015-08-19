<script>
function doSubmit(modid,val){ldelim}
	document.getElementById('mContact_sec_'+modid).value = val;
	document.getElementById('mContact_form_'+modid).submit();
{rdelim}
</script>
<div>
{if $error_name}
	{$error_name}<br />
{/if}
{if $error_email}
	{$error_email}<br />
{/if}
{if $error_message}
	{$error_message}<br />
{/if}
<form method="POST" id="mContact_form_{$module.id}">
<input name="mContact_sec_{$module.id}" id="mContact_sec_{$module.id}" type="hidden" value="">
<table>
	<tr>
		<td><label for="name">Navn:</label></td>
		<td><input {if $error_name} style="border:1px solid red;" {/if} name="mContact_name_{$module.id}" type="text" value="{$mContact_name}"></td>
	</tr>
	<tr>
		<td><label for="email">E-mail:</label></td>
		<td><input {if $error_email} style="border:1px solid red;" {/if} name="mContact_email_{$module.id}" value="{$mContact_email}" type="text"></td></tr>
	<tr>
		<td><label for="text">Besked:</label></td>
		<td><textarea {if $error_message} style="border:1px solid red;" {/if} name="mContact_message_{$module.id}" rows="6">{$mContact_message}</textarea></td></tr>
	<tr>
		<td></td>
		<td><input type="submit" value="Send formular" onclick="doSubmit('{$module.id}','{$submitNumber}');return false;"></td></tr>
	</table>
</form>
</div>