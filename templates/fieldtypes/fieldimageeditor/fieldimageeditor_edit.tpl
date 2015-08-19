<script type="text/javascript">
	var rotateimages = new Array();
	rotateimages[1] = '/uploads{$rotate.degrees1}';
	rotateimages[2] = '/uploads{$rotate.degrees2}';
	rotateimages[3] = '/uploads{$rotate.degrees3}';
	rotateimages[4] = '/uploads{$rotate.degrees4}';
	
	var cropper = {ldelim}
					{if $ratio}
						ratio: {ldelim} x: {$ratio.x}, y: {$ratio.y} {rdelim}, 
					{/if}
					{if $minwidth}
						minWidth: {$minwidth}, 
					{/if}
					{if $minheight}
						minHeight: {$minheight},
					{/if}
					{if $displayOnInit} 
						displayOnInit: true, 
					{/if}
					onloadCoords: {ldelim} x1: 0, y1: 0, x2: {$initDim.width}, y2: {$initDim.height} {rdelim},
					onEndCrop: onEndCrop 
	{rdelim};
	imageEditor.setup({ldelim}
							doFullRotate:'{$doFullRotate}',
							isSaveable:'{$saveable}',
							notSaveableMessage:'{cmstext value=notsaveablemessage}',
							rotateimages:rotateimages,
							cropperActive:true,
							cropper:cropper
	{rdelim});
</script>


<div id="cropperContainer" ondblclick="imageEditor.doAction('crop'); return false;" style="float: left;">
	<img src="/uploads{$imgInfo.src}" alt="test image" id="testImage" width="{$imgInfo.width}" height="{$imgInfo.height}">
</div>

		<div id="cropperPreviewBtsn" style="display:none;left:{$previewBtnWidth}px;top:37px;position:absolute;width:160px;">
			<ul class="buttons">
				<li class="preview"><span class="left"></span><a href="#" onclick="imageEditor.doAction('crop');" title="Preview"  class="preview">{cmstext value=preview}</a><span class="right"></span></li>
			</ul>
		</div>
		
		<form id="sizeForm" name="sizeForm" action="" method="get">
			<input type="hidden" name="pictureid" value="{$pictureid}">
			<input type="hidden" name="itemid" value="{$itemid}">
			<input type="hidden" name="editorAction" value="{$editorAction}">
			<input type="hidden" name="delta" value="{$delta}">
			<input type="hidden" name="rotatedegrees" id="rotatedegrees" value="{$degrees}">
			<input type="hidden" name="x1" id="x1">
			<input type="hidden" name="y1" id="y1">
			<input type="hidden" name="x2" id="x2">
			<input type="hidden" name="y2" id="y2">
			<input type="hidden" name="width" id="width">
			<input type="hidden" name="height" id="height">
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
		<div id="previewArea"></div>
		
		<div class="imageCropperInfo imageCropperInfoRight">
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



<div style="position: absolute; top: 100px; width: 340px; left: 300px; display: none;" class="mPopupWindow" id="rotateContainer">
	<div id="popupWindowContent_3">
		<div id="popupWindowHeader_3" class="windowHeader">
			<h5><span id="popupWindowTitle_3">{cmstext value=rotatepicture}</span></h5>
			<div class="headerButton"><a onclick="imageEditor.hideRotate(); return false" class="buttonTypeX" href="#"></a></div>
		</div>
		<div class="windowInner">
			<div id="popupWindowContentContainer_3" class="windowInnerPadding">
				<table cellspacing="0" cellpadding="0" style="margin: 0 auto">
					<tr>
						<td height="100" width="30" valign="middle" >
							<a href="#" class="buttonType3 iconRotateCounterClockwise" onclick="imageEditor.rotate('counter');return false;" title="{cmstext value=rotatecounterclockwise}"><span><span></span></span></a>
						</td>
						<td style="vertical-align: middle; text-align: center; width: 120px;">{if $rotate.degrees1}<img id="rotateImage" src="/uploads{$rotate.degrees1}">{/if}</td>
						<td width="30" valign="middle" align="right" style="padding-left: 10px;">
							<a href="#" class="buttonType3 iconRotateClockwise" onclick="imageEditor.rotate('clock');return false;" title="{cmstext value=rotateclockwise}"><span><span></span></span></a>
						</td>
					</tr>
				</table>
				<table align="right" class="moduleElementRow">
					<tbody>
						<tr>
							<td><a href="#" class="buttonType3 iconPositive" onclick="imageEditor.doRotate();"><span><span>{cmstext value=btnok}</span></span></a></td>
							<td><a href="#" class="buttonType3 iconNegative" onclick="imageEditor.hideRotate(); return false;"><span><span>{cmstext value=btcancel}</span></span></a></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<!--<div id="adjustContainer" style="display:none;position:absolute;top:100px;left:400px;border: 1px solid black;">
<table cellspacing="0" cellpadding="0" bgcolor="#E6E7E0" width="200">
		<tr>
			<td colspan="3"><h2>Juster billede</h2></td>
		</tr>
		<tr>
			<td colspan="3">
			<div id="track2" style="height:100px;background-color:#aaa;width:5px;">
 <div id="handle2" style="width:5px;height:8px;background-color:#7F1301;cursor:move;"> </div></div>
<div id="debug2"></div>
			<span id="brightvalue">0</span>
			</td>
		</tr>
</table>			
</div>-->