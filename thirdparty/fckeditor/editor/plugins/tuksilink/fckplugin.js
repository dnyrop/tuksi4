
FCKCommands.RegisterCommand( 'Tuksilink', new FCKDialogCommand( 'Tuksilink', FCKLang.TuksilinkDlgTitle, FCKPlugins.Items['tuksilink'].Path + 'tuksilink.php', 500, 300 ) ) ;

var oPlaceholderItem = new FCKToolbarButton( 'Tuksilink', "Link" ) ;
oPlaceholderItem.IconPath = FCKPlugins.Items['tuksilink'].Path + 'placeholder.gif' ;

FCKToolbarItems.RegisterItem( 'Tuksilink', oPlaceholderItem ) ;


function test(link) {
	alert(link)
}