<form name="theform"><input type="text" name="testfile">
<script>
{literal}
function testUpload(){
	alert('uploaded');
}
{/literal}
</script>
<table>
<tr>
	<td>{$btnupload}</td>
</tr>
<tr>
	<td>{if $img}<img src="{$img}" alt="" />{/if}</td>
</tr>
<tr>
	<td>{$btndelete}</td>
</tr>
</table>
<input type="submit" value="upload">
</form>