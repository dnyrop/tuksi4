tuksi_debug = {
	setDebug: function(){
		var link = document.createElement('link');
		link.setAttribute('rel','stylesheet');
		link.setAttribute('type','text/css');
		link.setAttribute('href','/stylesheet/tuksi.css');
		document.getElementsByTagName('head')[0].appendChild(link);
	},
	showPanel: function(){
		window.open('/services/popup/debug.php?debugtype=frontend','tuksiDebug',"scrollbars=1,width=600,height=800");
	}
}