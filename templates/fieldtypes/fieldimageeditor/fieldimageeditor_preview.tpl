<script type="text/javascript">
imageEditor.setup({ldelim}
						isSaveable:'{$saveable}',
						cropperActive:false
{rdelim});
</script>
{if $previewImg}
<img src="/uploads/{$previewImg.src}" alt="test image" id="testImage" width="{$previewImg.width}" height="{$previewImg.height}" style="float: left;"  />
{else}
<img src="/uploads/{$imgInfo.src}" alt="test image" id="testImage" width="{$imgInfo.width}" height="{$imgInfo.height}" style="float: left;"  />
{/if}
<form id="sizeForm" name="sizeForm" action="" method="GET">
	<input type="hidden" name="pictureid" value="{$pictureid}">
	<input type="hidden" name="itemid" value="{$itemid}">
	<input type="hidden" name="delta" value="{$delta}">
	<input type="hidden" name="editorAction" value="recrop">
	<input type="hidden" name="isLib" id="isLib" value="{$islib}">
	<input type="hidden" name="fieldvalue1" id="fieldvalue1" value="{$itemInfo.fieldvalue1}">
	<input type="hidden" name="fieldvalue2" id="fieldvalue2" value="{$itemInfo.fieldvalue2}">
	<input type="hidden" name="fieldvalue3" id="fieldvalue3" value="{$itemInfo.fieldvalue3}">
	<input type="hidden" name="fieldvalue4" id="fieldvalue4" value="{$itemInfo.fieldvalue4}">
	<input type="hidden" name="fieldvalue5" id="fieldvalue5" value="{$itemInfo.fieldvalue5}">
	<input type="hidden" name="hiddenfieldname" id="hiddenfieldname" value="{$itemInfo.hiddenfieldname}">
	<input type="hidden" name="formname" id="formname" value="{$itemInfo.formname}">
	<input type="hidden" name="callback" id="callback" value="{$itemInfo.callback}">
	<input type="hidden" name="rowid" id="rowid" value="{$rowid}">
</form>

		<div class="imageCropperInfo imageCropperInfoRight">
			
		{if $previewImg}
			<strong style="color:red;">{cmstext value="previewimageresizedheadline"}</strong>
			{cmstext value="previewimageresized"}<br /><br />
		{/if}
		<strong>{cmstext value=originalimage}:</strong>
			
			<ul>
				<li class="odd"><label>{cmstext value=name}:</label> {$orig.name|truncate:20}</li>
				<li><label>{cmstext value=height}:</label> {$orig.height}px</li>
				<li class="odd"><label>{cmstext value=width}:</label> {$orig.width}px</li>
				<li><label>{cmstext value=type}:</label> {$orig.mime}</li>
				<li class="odd"><label>{cmstext value=size}:</label> {$orig.size}Kb</li>
			</ul>
			
			<strong>{cmstext value=template}</strong>
			
			<ul>
				<li class="odd"><label>{cmstext value=height}:</label> min. {$skabelon.minheight}px</li>
				<li><label>{cmstext value=width}:</label> min. {$skabelon.minwidth}px</li>
				<li class="odd"><label>{cmstext value=ratio}:</label> {$skabelon.ratio}</li>
			</ul>
			<br>
			<a href="#" class="buttonType3 iconPositive" onclick="imageEditor.doAction('save'); return false;"><span><span>{cmstext value=savepicture}</span></span></a>
		</div>