<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html lang="da">
<head>
	<title></title>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="Cache-Control" content="no-cache">
	<meta http-equiv="Expires" content="0">
	<meta http-equiv="Pragma" content="no-cache"> 
	<meta name="robots" content="noindex,nofollow">
	<link href="/themes/default/stylesheet/style.css" type="text/css" rel="stylesheet">
	<link href="/themes/default/stylesheet/cropper.css" type="text/css" rel="stylesheet">
	<script type="text/javascript" src="/javascript/backend/libs/base.js"></script>
	<script type="text/javascript" src="/javascript/backend/libs/prototype.js"></script>
	<script type="text/javascript" src="/javascript/backend/libs/scriptaculous/scriptaculous.js"></script>
	<script type="text/javascript" src="/javascript/backend/tuksi.js"></script>
	<script type="text/javascript" src="/javascript/backend/libs/tuksi.util.js"></script>
	<script type="text/javascript" src="/javascript/backend/libs/tuksi.window.js"></script>
	<script type="text/javascript" src="/javascript/backend/libs/tuksi.pagegenerator.js"></script>
<!--	<script type="text/javascript" src="/javascript/backend/libs/tuksi.swfupload.js"></script>-->
	<script type="text/javascript" src="/javascript/backend/fieldtypes/tuksi_cropper.js"></script>
	<script type="text/javascript" src="/javascript/backend/fieldtypes/fieldImageEditor.js"></script>

	{literal}
	<style type="text/css">
		label { 
			clear: left;
			margin-left: 50px;
			float: left;
			width: 5em;
		}
		
		html, body { 
			margin: 0;
		}
	</style>
	{/literal}
</head>
<body id="frame_right" style="height:440px; text-align: left;">
<div class="mPopupWindow" style="position:relative;top:0;left:0;width:100%;">
	<div class="windowHeader" style="text-align:left;">
		<h5>ImageEditor</h5>
		<div class="headerButton">
			<a onclick="window.close();" class="buttonTypeX" href="#"/></a>
		</div>
	</div>
	<div class="windowInner">
		<div class="windowInnerPadding" style="height:440px;">
			<div class="imageCropperHead">
				<div class="topActionButtons">
					<ul class="ul">
						{if $btn.adjust}<!--<li class="adjust"><span class="left"></span><a href="#" onclick="doAction('showadjust');" title="Rotate"  class="preview">Juster</a><span class="right"></span></li>-->{/if}
						{if $btn.new}<li class="li"><a class="buttonType1" onclick="imageEditor.doAction('new'); return false;" title="{cmstext value=newimage}" href="#"><span><span>{cmstext value=newimage}</span></span></a></li>{/if}
						{if $btn.rotate}<li class="li"><a class="buttonType1" onclick="imageEditor.doAction('showrotate'); return false;" title="{cmstext value=rotate}" href="#"><span><span>{cmstext value=rotate}</span></span></a></li>{/if}
						{if $btn.crop}<li class="li"><a class="buttonType1" onclick="imageEditor.doAction('showcropper'); return false;" title="{cmstext value=crop}" href="#"><span><span>{cmstext value=crop}</span></span></a></li>{/if}
						{if $btn.recrop}<li class="li"><a class="buttonType1" onclick="imageEditor.doAction('recrop'); return false;" title="{cmstext value=back}" href="#"><span><span>{cmstext value=back}</span></span></a></li>{/if}
						<li class="li" id="cropperPreviewBtn" style="display:none;"><a class="buttonType1" onclick="imageEditor.doAction('crop'); return false;" title="{cmstext value=preview}" href="#"><span><span>{cmstext value=preview}</span></span></a></li>
					</ul>
				</div>
			</div>
			{$content}
		</div>
	</div>	
</div>
<div id="popupWindow"></div>
</body>
</html>
