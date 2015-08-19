<textarea id="{$htmltagname}" name="{$htmltagname}" rows="4" cols="30">{$value}</textarea>

<script>
	var o{$htmltagname} = new InnovaEditor("o{$htmltagname}");
	
	//o{$htmltagname}.langDir = 'danish';
	
	/***************************************************
		SETTING EDITOR DIMENSION (WIDTH x HEIGHT)
	***************************************************/
	
	 o{$htmltagname}.width=580;//You can also use %, for example:  {$htmltagname} .width="100%"
	 o{$htmltagname}.height=350;


	/***************************************************
		SHOWING DISABLED BUTTONS
	***************************************************/

	//o{$htmltagname}.btnPrint=true;
	//o{$htmltagname}.btnPasteText=true;
	//o{$htmltagname}.btnFlash=true;
	//o{$htmltagname}.btnMedia=true;
	//o{$htmltagname}.btnLTR=true;
	//o{$htmltagname}.btnRTL=true;
	//o{$htmltagname}.btnSpellCheck=true;
	//o{$htmltagname}.btnStrikethrough=true;
	//o{$htmltagname}.btnSuperscript=true;
	//o{$htmltagname}.btnSubscript=true;
	//o{$htmltagname}.btnClearAll=true;
	//o{$htmltagname}.btnSave=true;
	//o{$htmltagname}.btnStyles=true; //Show "Styles/Style Selection" button

	/***************************************************
		APPLYING STYLESHEET 
		(Using external css file)
	***************************************************/
	
	 o{$htmltagname}.css	=	"{$base}/style/tuksi.css"; //Specify external css file here

	/***************************************************
		APPLYING STYLESHEET 
		(Using predefined style rules)
	***************************************************/
	/*
	 {$htmltagname}.arrStyle = [["BODY",false,"","font-family:Verdana,Arial,Helvetica;font-size:x-small;"],
				[".ScreenText",true,"Screen Text","font-family:Tahoma;"],
				[".ImportantWords",true,"Important Words","font-weight:bold;"],
				[".Highlight",true,"Highlight","font-family:Arial;color:red;"]];
	
	If you'd like to set the default writing to "Right to Left", you can use:
	
	 {$htmltagname}.arrStyle = [["BODY",false,"","direction:rtl;unicode-bidi:bidi-override;"]];
	*/


		
	/***************************************************
		ADDING YOUR CUSTOM LINK LOOKUP
	***************************************************/
	
	o{$htmltagname}.cmdInternalLink = "modelessDialogShow('{$base}/tuksi_popups/hyperlink.php?treeid={$page.treeid}',365,170)"; //Command to open your custom link lookup page.


	/***************************************************
		SETTING EDITING MODE
		
		Possible values: 
			- "HTMLBody" (default) 
			- "XHTMLBody" 
			- "HTML" 
			- "XHTML"
	***************************************************/
	
	 o{$htmltagname}.mode		="XHTMLBody";
	 o{$htmltagname}.useDIV	=true;
	 o{$htmltagname}.useBR		=false;
	 o{$htmltagname}.useTagSelector	=false;
	 
	//o{$htmltagname}.features=["Cut","Copy","Paste","PasteWord","PasteText","|","Undo","Redo","|","ForeColor","BackColor","|","Hyperlink","XHTMLSource","BRK","Numbering","Bullets","|","Indent","Outdent","|","Table","Guidelines","Absolute","|","Characters","Line","RemoveFormat","ClearAll","BRK","TextFormatting","BoxFormatting","ParagraphFormatting","CssText","Styles","|","Paragraph","FontName","FontSize","|","Bold","Italic","Underline","Strikethrough","|","JustifyLeft","JustifyCenter","JustifyRight","JustifyFull"];
	o{$htmltagname}.features=[
	"Cut",
	"Copy",
	"Paste",
	"PasteWord",
	"|",
	"Undo",
	"Redo",
	"|",
	"Bold",
	"Italic",
	"Underline",
	"Strikethrough",
	"Superscript",
	"Subscript",
	"InternalLink",
	"Numbering",
	"Bullets",
//	"Table",
	"Line",
//	"|",
//	"Styles",
//	"|",
//	"Characters",
	"|",
	"RemoveFormat",
	"ClearAll",
	"|",
	"XHTMLSource",	
//		"FullScreen",
//		"Preview",
//		"Print",
//		"Search",
		"Spell",
//		"ForeColor",
//		"BackColor",
//		"|",
//		"Bookmark",
//		"BRK",
//		"|",
//		"Indent",
//		"Outdent",
//		"LTR",
//		"RTL",
//		"|",
//		"Image",
//		"Flash",
//		"Media",
//		"Guidelines",
//		"Absolute",
//		"Form",
//		"BRK",
//		"StyleAndFormatting",
//		"TextFormatting",
//		"ListFormatting",
//		"BoxFormatting",
//		"ParagraphFormatting",
//		"CssText",
//		"|",
//		"Paragraph",
//		"FontName",
//		"FontSize",
//		"|",		
//		"|",
//		"JustifyLeft",
//		"JustifyCenter",
//		"JustifyRight",
//		"JustifyFull"
	];
	o{$htmltagname}.REPLACE("{$htmltagname}");
	</script>
