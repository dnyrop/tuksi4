{$filename}
<input type="hidden" id="fileUploaded{$id}" name="fileUploaded{$id}" value="">
<table>
	<tbody>
		<tr>
			<td><input disabled="disabled" type="text" class="text" name="uploadFilename{$id}" id="uploadFilename{$id}" value="" size="40"></td>
			<td><a class="buttonType2" onclick="uploader{$id}.selectFiles(); return false;" href="#" /><span><span>Browse</span></span></a></td>
			</tr>
	</tbody>
</table>
<div style="margin-top:5px;">
	<span id="uploadProgressText{$id}"></span>
</div>
<script>
	var uploader{$id} = new tuksiSwfupload('{$id}','{$uploadpath}');
</script>