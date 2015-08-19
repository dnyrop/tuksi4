<script type="text/javascript">
var nextStep = 'imageeditorpopup.php?itemid={$conf.itemid}&rowid={$conf.rowid}&editorAction=recrop';
{literal}
function uploadCompleted(){
	var returnData = document.rup.getResponse();
	var status = returnData.match('status:([0-9]+)')[1];
	if(status == 1) {
		document.location = nextStep;
	} else {
		//var errormsg = "";
		//errormsg = /##([^#]*)##/gi.exec(returnData);
		//console.log(errormsg);
		$('error_message').innerHTML = returnData.match(/##([^#]*)##/)[1];
		new Effect.Opacity($('error_message'),{duration:0.0,from:1.0,to:0.0});
		$('error_message').style.display = 'block';
		new Effect.Opacity($('error_message'),{duration:0.5,from:0.0,to:1.0});
		new Effect.Opacity($('error_message'),{afterFinish:function(){$('error_message').hide();},duration:1.0,from:1.0,to:0.0,delay:5});
	}
}
function doUpload(){
	document.fileupload.submit();
	$('progress').show();
}
{/literal}
</script>
<div class="imageEditor">
	<div id="error_message" class="imageEditorError" {if !$error}style="display:none;"{/if}>
		{foreach from=$error item=err}
	{cmstext value=$err.error value1=$err.value1 value2=$err.value2}<br />
		{/foreach}
		<br />
		<script type="text/javascript">
			{literal}
			new Effect.Opacity($('error_message'),{
					afterFinish:function(){$('error_message').hide();},
					duration:1.0,
					from:1.0,
					to:0.0,
					delay:5
			});
			{/literal}
		</script>
	</div>
	<form name="fileupload" enctype="multipart/form-data" method="post">
		<input type="hidden" name="isLib" id="isLib" value="{$islib}">
	
		<h5>{cmstext value=uploadinstructions}:</h5>
		<div class="imageCropperInfo">
			<ul>
				<li><label>{cmstext value=min_height}:</label> {if $imgConf.minheight > 0}<b>{$imgConf.minheight}</b> pixels{else}{cmstext value=norules}{/if}</li>
				<li><label>{cmstext value=min_width}:</label> {if $imgConf.minwidth > 0}<b>{$imgConf.minwidth}</b> pixels{else}{cmstext value=norules}{/if}</li>
				<li><label>{cmstext value=allowedfiletypes}:</label> {$imgType}</li>
			</ul>
		</div>
		</td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td><br></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
			</tr>
		</table>
	<!--Du kan vælge imellem følgende uploadmetoder:<br><br>-->
	<!--<b>1. Drag n drop</b>
	<b class="imageEditorBrowseHeading">2. Stifinder</b>-->
		<div class="imageEditorUploader">
			<!--<div class="imageEditorApplet">
				<script>
					setUploader('{$conf.itemid}','{$conf.rowid}','{$conf.sessionid}','{$conf.baseurl}',150,150);
				</script>
			</div>-->
			<div class="imageEditorExplorer">
				<br />
				<p class="imageEditorFindText">
				{cmstext value=btnuploadinstructions}<br><br>
					<input name="userfile" onchange="doUpload();" type="file"><br /><br />
					<span id="progress" style="display:none;">
						<img style="float:left;padding-left:0;" src="{$conf.path.theme}images/graphics/loading.gif">
						<span style="float:left;padding-top:0;padding-left:5px;">{cmstext value=uploadingpicture}...</span>
					</span>
				</p>
			</div>
		</div>
	</form>
</div>	
