<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title>Link</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<meta name="robots" content="noindex, nofollow" />
		<script src="/thirdparty/fckeditor/editor/dialog/common/fck_dialog_common.js" type="text/javascript"></script>
		<script src="tuksilink.js" type="text/javascript"></script>
		<script src="/javascript/backend/fieldtypes/fieldLink.js" type="text/javascript"></script>
{literal}		
		<style type="text/css">
			#popup_cmstree_select {
				width: 300px;
			}
			#popup_file_media {
				width: 400px;
			}
			.popup_file_upload {
				display: none;
			}
		</style>
{/literal}
	</head>
	<body scroll="no" style="OVERFLOW: hidden">
		<input type="hidden" id="cmbTarget" value="" />
		<input type="hidden" id="txtTargetFrame" value="_blank" />
		<input type="hidden" id="txtAttId" value="" />
		<input type="hidden" id="txtAttClasses" value="" />
		<input type="hidden" id="txtAttStyle" value="" />
		<form name="tuksiForm" id="tuksiForm" method="post" enctype="multipart/form-data" action="">
		{$linkinput}
		</form>
		<script type="text/javascript">
		{if $post}
			window.parent.Ok();
		{else}
			document.getElementById('popup_file_browse').onchange = function() {ldelim}
			  changeToSubmit();
			{rdelim}
		{/if}
		</script>
	</body>
</html>
