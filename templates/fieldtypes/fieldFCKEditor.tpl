<textarea name="{$htmltagname}">{$value}</textarea>
<script type="text/javascript">
	// Automatically calculates the editor base path based on the _samples directory.
	// This is usefull only for these samples. A real application should use something like this:
	// oFCKeditor.BasePath = '/fckeditor/' ;	// '/fckeditor/' is the default value.
	var sBasePath = "/thirdparty/fckeditor/" ;
	
	var oFCKeditor = new FCKeditor( '{$htmltagname}' ) ;
	oFCKeditor.BasePath	= sBasePath ;
	
	oFCKeditor.Config['AutoDetectLanguage']	= false ;
	oFCKeditor.Config['DefaultLanguage'] = 'en' ;
	oFCKeditor.Config['CustomConfigurationsPath'] = "../fcktuksiconfig.js";
	
	// Bruger ikke i TuksiLink
	oFCKeditor.Config['LinkUploadURL'] = "/deralte.php";
	oFCKeditor.ToolbarSet = 'tuksi_default';
	oFCKeditor.Height	= 300 ;
	oFCKeditor.treeid = {$page.treeid};
	oFCKeditor.ReplaceTextarea() ;
</script>