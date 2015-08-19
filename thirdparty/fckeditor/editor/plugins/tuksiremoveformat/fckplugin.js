/* 	TuksiRemoveFormat
	Information kommer her!!
*/

var TuksiRemoveFormatCommand = function()
{
}

TuksiRemoveFormatCommand.prototype =
{
	Execute : function()
	{
		function getQueryVariable(variable) {
			var query = window.location.search.substring(1);
			var vars = query.split("&");
			for (var i=0;i<vars.length;i++) {
				var pair = vars[i].split("=");
				if (pair[0] == variable) {
					return pair[1];
				}
			} 
		}

		var InstanceName = getQueryVariable('InstanceName');
		var oFCKeditor = FCKeditorAPI.GetInstance(InstanceName);
		var html = oFCKeditor.GetData();
		
		function get_selection_html(instance_name) {
			var oFCKeditor = FCKeditorAPI.GetInstance(instance_name);
			var selection = (oFCKeditor.EditorWindow.getSelection ? oFCKeditor.EditorWindow.getSelection() : oFCKeditor.EditorDocument.selection);
		   
			if(selection.createRange) {
				var range = selection.createRange();
				var html = range.htmlText;
			} else {
				var range = selection.getRangeAt(selection.rangeCount - 1).cloneRange();
				var clonedSelection = range.cloneContents();
				var div = document.createElement('div');
				div.appendChild(clonedSelection);
				var html = div.innerHTML;
			}
			   
			return html;
		}
		function removeHtml(html) {
			html = html.replace(/<style[^>]*>([\s\S]*?)<\/style>/gi, "");
			html = html.replace(/<(font|span|div|sub|label|i|em|b|strong|u|strike|li|ul|ol)[^>]*>/gi, "");
			html = html.replace(/<\/(font|span|div|sub|label|i|em|b|strong|u|strike|li|ul|ol)>/gi, "");
			html = html.replace(/<b>/gi, "<strong>");
			html = html.replace(/<\/b>/gi, "</strong>");
			html = html.replace(/(<\/?strong>)\1*/gi, "$1");
			html = html.replace(/<\/strong><\/strong>/gi, "</strong>");
			html = html.replace(/<\/strong>(\S)*<strong>/gi, "");
			html = html.replace(/<p[^>]*>([\s\S]*?)<p[^>]*>/gi, "<p>$1");
			html = html.replace(/(<\/?p>)\1*/gi, "$1");
			html = html.replace(/<o:p>\s*<\/o:p>/g, '') ;
			html = html.replace(/<o:p>[\s\S]*?<\/o:p>/g, '&nbsp;') ;
			html = html.replace(/\s*mso-[^:]+:[^;"]+;?/gi, '' ) ;
			html = html.replace(/\s*MARGIN: 0cm 0cm 0pt\s*;/gi, '' ) ;
			html = html.replace(/\s*MARGIN: 0cm 0cm 0pt\s*"/gi, "\"" ) ;
			html = html.replace(/\s*TEXT-INDENT: 0cm\s*;/gi, '' ) ;
			html = html.replace(/\s*TEXT-INDENT: 0cm\s*"/gi, "\"" ) ;
			html = html.replace(/\s*TEXT-ALIGN: [^\s;]+;?"/gi, "\"" ) ;
			html = html.replace(/\s*PAGE-BREAK-BEFORE: [^\s;]+;?"/gi, "\"" ) ;
			html = html.replace(/\s*FONT-VARIANT: [^\s;]+;?"/gi, "\"" ) ;
			html = html.replace(/\s*tab-stops:[^;"]*;?/gi, '' ) ;
			html = html.replace(/\s*tab-stops:[^"]*/gi, '' ) ;
			html = html.replace(/\s*face="[^"]*"/gi, '' ) ;
			html = html.replace(/\s*face=[^ >]*/gi, '' ) ;
			html = html.replace(/\s*FONT-FAMILY:[^;"]*;?/gi, '' ) ;
			html = html.replace(/<(\w[^>]*) class=([^ |>]*)([^>]*)/gi, "<$1$3") ;
			html = html.replace(/<(\w[^>]*) style="([^\"]*)"([^>]*)/gi, "<$1$3" ) ;
			html = html.replace(/<STYLE[^>]*>[\s\S]*?<\/STYLE[^>]*>/gi, '' ) ;
			html = html.replace(/<(?:META|LINK)[^>]*>\s*/gi, '' ) ;
			html = html.replace(/\s*style="\s*"/gi, '' ) ;
			html = html.replace(/<SPAN\s*[^>]*>\s*&nbsp;\s*<\/SPAN>/gi, '&nbsp;' ) ;
			html = html.replace(/<SPAN\s*[^>]*><\/SPAN>/gi, '' ) ;
			html = html.replace(/<div\s*[^>]*>\s*&nbsp;\s*<\/div>/gi, '&nbsp;' ) ;
			html = html.replace(/<div\s*[^>]*><\/div>/gi, '' ) ;
			html = html.replace(/<(\w[^>]*) lang=([^ |>]*)([^>]*)/gi, "<$1$3") ;
			html = html.replace(/<SPAN\s*>([\s\S]*?)<\/SPAN>/gi, '$1' ) ;
			html = html.replace(/<FONT\s*>([\s\S]*?)<\/FONT>/gi, '$1' ) ;
			html = html.replace(/<\\?\?xml[^>]*>/gi, '' ) ;
			html = html.replace(/<w:[^>]*>[\s\S]*?<\/w:[^>]*>/gi, '' ) ;
			html = html.replace(/<\/?\w+:[^>]*>/gi, '' ) ;
			html = html.replace(/<\!--[\s\S]*?-->/g, '' ) ;
			html = html.replace(/<(U|I|STRIKE)>&nbsp;<\/\1>/g, '&nbsp;' ) ;
			html = html.replace(/<H\d>\s*<\/H\d>/gi, '' ) ;
			html = html.replace(/<(\w+)[^>]*\sstyle="[^"]*DISPLAY\s?:\s?none[\s\S]*?<\/\1>/ig, '' ) ;
			html = html.replace(/<(\w[^>]*) language=([^ |>]*)([^>]*)/gi, "<$1$3") ;
			html = html.replace(/<(\w[^>]*) onmouseover="([^\"]*)"([^>]*)/gi, "<$1$3") ;
			html = html.replace(/<(\w[^>]*) onmouseout="([^\"]*)"([^>]*)/gi, "<$1$3") ;
			html = html.replace(/<H(\d)([^>]*)>/gi, '<h$1>' ) ;
			html = html.replace(/<(H\d)><FONT[^>]*>([\s\S]*?)<\/FONT><\/\1>/gi, '<$1>$2<\/$1>' );
			html = html.replace(/<(H\d)><EM>([\s\S]*?)<\/EM><\/\1>/gi, '<$1>$2<\/$1>' );
			html = html.replace(/<H1([^>]*)>/gi, '' ) ;
			html = html.replace(/<H2([^>]*)>/gi, '' ) ;
			html = html.replace(/<H3([^>]*)>/gi, '' ) ;
			html = html.replace(/<H4([^>]*)>/gi, '' ) ;
			html = html.replace(/<H5([^>]*)>/gi, '' ) ;
			html = html.replace(/<H6([^>]*)>/gi, '' ) ;
			html = html.replace(/<([^\s>]+)(\s[^>]*)?>\s*<\/\1>/g, '' ) ;
			html = html.replace(/<([^\s>]+)(\s[^>]*)?>\s*<\/\1>/g, '' ) ;
			html = html.replace(/<([^\s>]+)(\s[^>]*)?>\s*<\/\1>/g, '' ) ;

			return html;
		}
		html = removeHtml(html);
		oFCKeditor.SetHTML(html);
	},

	GetState : function()
	{
		return FCK_TRISTATE_OFF ;
	}

} ;

FCKCommands.RegisterCommand('TuksiRemoveFormat', new TuksiRemoveFormatCommand());
var oPlaceholderItem = new FCKToolbarButton( 'TuksiRemoveFormat', "RemoveFormat", null, FCK_TOOLBARITEM_ONLYICON, true, true, 0 ) ;
oPlaceholderItem.IconPath = FCKPlugins.Items['tuksiremoveformat'].Path + 'remove_format.png' ;
FCKToolbarItems.RegisterItem( 'TuksiRemoveFormat', oPlaceholderItem) ;

