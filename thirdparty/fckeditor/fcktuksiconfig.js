//
// CUSTOM FCKeditor (2.6.3) settings for Tuksi
// Remember when updating editor! 
//  - replace '<br>' with '' in da.js (alerts)
//  - change DlgLnkUpload		: "Filer", in da.js (tab in link)
//  - disable context menu (same files as http://dev.fckeditor.net/ticket/311 but with //)
//
// Last updated 080828
//

FCKConfig.LinkBrowser = false ;
FCKConfig.LinkDlgHideTarget = true ;
FCKConfig.LinkDlgHideAdvanced = true ;
FCKConfig.CleanWordKeepsStructure = true ;
// FCKConfig.SourcePopup = true ;

FCKConfig.BrowserContextMenuOnCtrl = true ;
// FCKConfig.IeSpellDownloadUrl = 'http://files.kch.meer3-udv.dwarf.dk/ieSpellSetup251106.exe' ;
FCKConfig.SpellChecker = 'SpellerPages' ;
FCKConfig.FirefoxSpellChecker = true ;

FCKConfig.SkinPath = FCKConfig.BasePath + 'skins/tuksi4/' ;
FCKConfig.BackgroundBlockerOpacity = 0 ;

FCKConfig.PluginsPath = FCKConfig.BasePath + 'plugins/' ;
FCKConfig.Plugins.Add('tuksilink') ;
FCKConfig.Plugins.Add('tuksiremoveformat') ;

FCKConfig.ToolbarCanCollapse = false;
FCKConfig.FontFormats  = 'p;h1;h2;h3;pre';

FCKConfig.ToolbarSets["tuksi_default"] = [
 ['PasteWord','-','Bold','Italic','Underline','StrikeThrough','Subscript','Superscript','-','OrderedList','UnorderedList','-','Tuksilink','Unlink','-',/*'FontFormat','-','RemoveFormat','-',*/'Undo','Redo','-','TuksiRemoveFormat','-','Source']
];

FCK.CustomCleanWord = function(oNode, bIgnoreFont, bRemoveStyles) {
	var html = oNode.innerHTML ;
	var bIgnoreFont = true;
	var bRemoveStyles = true;
	
  // Fjerner <font> og <span> tags
  html = html.replace(/<(font|span)[^>]*>/gi, "");
  html = html.replace(/<\/(font|span)>/gi, "");

  // Remove styles
  html = html.replace(/<style[^>]*>([\s\S]*?)<\/style>/gi, "");

  // b -> strong
  html = html.replace(/<b>/gi, "<strong>");
  html = html.replace(/<\/b>/gi, "</strong>");
  html = html.replace(/(<\/?strong>)\1*/gi, "$1");
  html = html.replace(/<\/strong><\/strong>/gi, "</strong>");
  html = html.replace(/<\/strong>(\S)*<strong>/gi, "");

  // fjerner dobbelt <p>
  html = html.replace(/<p[^>]*>([\s\S]*?)<p[^>]*>/gi, "<p>$1");
  html = html.replace(/(<\/?p>)\1*/gi, "$1");
	
	//
	// RESTEN FRA STANDARD FUNKTIONEN
	//

	html = html.replace(/<o:p>\s*<\/o:p>/g, '') ;
	html = html.replace(/<o:p>[\s\S]*?<\/o:p>/g, '&nbsp;') ;

	// Remove mso-xxx styles.
	html = html.replace( /\s*mso-[^:]+:[^;"]+;?/gi, '' ) ;

	// Remove margin styles.
	html = html.replace( /\s*MARGIN: 0cm 0cm 0pt\s*;/gi, '' ) ;
	html = html.replace( /\s*MARGIN: 0cm 0cm 0pt\s*"/gi, "\"" ) ;

	html = html.replace( /\s*TEXT-INDENT: 0cm\s*;/gi, '' ) ;
	html = html.replace( /\s*TEXT-INDENT: 0cm\s*"/gi, "\"" ) ;

	html = html.replace( /\s*TEXT-ALIGN: [^\s;]+;?"/gi, "\"" ) ;

	html = html.replace( /\s*PAGE-BREAK-BEFORE: [^\s;]+;?"/gi, "\"" ) ;

	html = html.replace( /\s*FONT-VARIANT: [^\s;]+;?"/gi, "\"" ) ;

	html = html.replace( /\s*tab-stops:[^;"]*;?/gi, '' ) ;
	html = html.replace( /\s*tab-stops:[^"]*/gi, '' ) ;

	// Remove FONT face attributes.
	if ( bIgnoreFont )
	{
		html = html.replace( /\s*face="[^"]*"/gi, '' ) ;
		html = html.replace( /\s*face=[^ >]*/gi, '' ) ;

		html = html.replace( /\s*FONT-FAMILY:[^;"]*;?/gi, '' ) ;
	}

	// Remove Class attributes
	html = html.replace(/<(\w[^>]*) class=([^ |>]*)([^>]*)/gi, "<$1$3") ;

	// Remove styles.
	if ( bRemoveStyles )
		html = html.replace( /<(\w[^>]*) style="([^\"]*)"([^>]*)/gi, "<$1$3" ) ;

	// Remove style, meta and link tags
	html = html.replace( /<STYLE[^>]*>[\s\S]*?<\/STYLE[^>]*>/gi, '' ) ;
	html = html.replace( /<(?:META|LINK)[^>]*>\s*/gi, '' ) ;

	// Remove empty styles.
	html =  html.replace( /\s*style="\s*"/gi, '' ) ;

	html = html.replace( /<SPAN\s*[^>]*>\s*&nbsp;\s*<\/SPAN>/gi, '&nbsp;' ) ;

	html = html.replace( /<SPAN\s*[^>]*><\/SPAN>/gi, '' ) ;

	// Remove Lang attributes
	html = html.replace(/<(\w[^>]*) lang=([^ |>]*)([^>]*)/gi, "<$1$3") ;

	html = html.replace( /<SPAN\s*>([\s\S]*?)<\/SPAN>/gi, '$1' ) ;

	html = html.replace( /<FONT\s*>([\s\S]*?)<\/FONT>/gi, '$1' ) ;

	// Remove XML elements and declarations
	html = html.replace(/<\\?\?xml[^>]*>/gi, '' ) ;

	// Remove w: tags with contents.
	html = html.replace( /<w:[^>]*>[\s\S]*?<\/w:[^>]*>/gi, '' ) ;

	// Remove Tags with XML namespace declarations: <o:p><\/o:p>
	html = html.replace(/<\/?\w+:[^>]*>/gi, '' ) ;

	// Remove comments [SF BUG-1481861].
	html = html.replace(/<\!--[\s\S]*?-->/g, '' ) ;

	html = html.replace( /<(U|I|STRIKE)>&nbsp;<\/\1>/g, '&nbsp;' ) ;

	html = html.replace( /<H\d>\s*<\/H\d>/gi, '' ) ;

	// Remove "display:none" tags.
	html = html.replace( /<(\w+)[^>]*\sstyle="[^"]*DISPLAY\s?:\s?none[\s\S]*?<\/\1>/ig, '' ) ;

	// Remove language tags
	html = html.replace( /<(\w[^>]*) language=([^ |>]*)([^>]*)/gi, "<$1$3") ;

	// Remove onmouseover and onmouseout events (from MS Word comments effect)
	html = html.replace( /<(\w[^>]*) onmouseover="([^\"]*)"([^>]*)/gi, "<$1$3") ;
	html = html.replace( /<(\w[^>]*) onmouseout="([^\"]*)"([^>]*)/gi, "<$1$3") ;

	if ( FCKConfig.CleanWordKeepsStructure )
	{
		// The original <Hn> tag send from Word is something like this: <Hn style="margin-top:0px;margin-bottom:0px">
		html = html.replace( /<H(\d)([^>]*)>/gi, '<h$1>' ) ;

		// Word likes to insert extra <font> tags, when using MSIE. (Wierd).
		html = html.replace( /<(H\d)><FONT[^>]*>([\s\S]*?)<\/FONT><\/\1>/gi, '<$1>$2<\/$1>' );
		html = html.replace( /<(H\d)><EM>([\s\S]*?)<\/EM><\/\1>/gi, '<$1>$2<\/$1>' );
	}
	else
	{
		html = html.replace( /<H1([^>]*)>/gi, '<div$1><b><font size="6">' ) ;
		html = html.replace( /<H2([^>]*)>/gi, '<div$1><b><font size="5">' ) ;
		html = html.replace( /<H3([^>]*)>/gi, '<div$1><b><font size="4">' ) ;
		html = html.replace( /<H4([^>]*)>/gi, '<div$1><b><font size="3">' ) ;
		html = html.replace( /<H5([^>]*)>/gi, '<div$1><b><font size="2">' ) ;
		html = html.replace( /<H6([^>]*)>/gi, '<div$1><b><font size="1">' ) ;

		html = html.replace( /<\/H\d>/gi, '<\/font><\/b><\/div>' ) ;

		// Transform <P> to <DIV>
		var re = new RegExp( '(<P)([^>]*>[\\s\\S]*?)(<\/P>)', 'gi' ) ;	// Different because of a IE 5.0 error
		html = html.replace( re, '<div$2<\/div>' ) ;

		// Remove empty tags (three times, just to be sure).
		// This also removes any empty anchor
		html = html.replace( /<([^\s>]+)(\s[^>]*)?>\s*<\/\1>/g, '' ) ;
		html = html.replace( /<([^\s>]+)(\s[^>]*)?>\s*<\/\1>/g, '' ) ;
		html = html.replace( /<([^\s>]+)(\s[^>]*)?>\s*<\/\1>/g, '' ) ;
	}

	return html ;
}
