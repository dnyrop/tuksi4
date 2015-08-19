tuksi.util = {
	getWindowDimensions: function(){
		
		var width =	self.innerWidth
						|| document.documentElement.clientWidth
						|| document.body.clientWidth
						|| 0;
		var height =	self.innerHeight
						|| document.documentElement.clientHeight
						|| document.body.clientHeight
						|| 0;
		
		return {width:width,height:height};
	},
	getWindowScroll: function(){
		
		var scroll_x = 	window.pageXOffset
							|| document.documentElement.scrollLeft
							|| document.body.scrollLeft
							|| 0;
		var scroll_y = 	window.pageYOffset
							|| document.documentElement.scrollTop
							|| document.body.scrollTop
							|| 0;
							
		return {scroll_x:scroll_x,scroll_y:scroll_y};
		
	},
	getCursorPosition:function(event){
		 if(event === undefined) {
	    	return {x:0,y:0};
	    }
		 
		 var cursor = {x:0, y:0};
	    if (event.pageX || event.pageY) {
	        cursor.x = event.pageX;
	        cursor.y = evente.pageY;
	    } 
	    else {
	        var de = document.documentElement;
	        var b = document.body;
	        cursor.x = event.clientX + 
	            (de.scrollLeft || b.scrollLeft) - (de.clientLeft || 0);
	        cursor.y = event.clientY + 
	            (de.scrollTop || b.scrollTop) - (de.clientTop || 0);
	    }
	    return cursor;
	},
	setContentSize:function(){
	  var wdim = tuksi.util.getWindowDimensions();
    var scrollPos = Position.cumulativeOffset($('scrollFrame'));
	  var height = wdim.height - scrollPos[1];
	  var width = wdim.width - scrollPos[0];
		var rightTopHeight = $('rightTop').getHeight();
    // if(width < 950) { width = 950; }
		$('scrollFrame').setStyle({height:height+'px',width:width+'px'});
		$('frameContent').setStyle({width:(width-51)+'px'});
		$('leftFrame').setStyle({height:(height+rightTopHeight-37)+'px'});
	},
	setPageHeight:function(){
		var wdim = tuksi.util.getWindowDimensions();
		var scrollPos = Position.cumulativeOffset($('scrollFrame'));
		var newHeigth = wdim.height - scrollPos[1] - 24;
	//	$$('.mListView table td ul.buttons li').invoke('setStyle',{float:'none'});
		$('scrollFrame').setStyle({height:newHeigth+'px'});
		newHeigth = wdim.height - 52;
		//$('leftFrame').setStyle({height:newHeigth+'px'});
	},
	setPopup: function(oThis, sHeight, sWidth,name){
		var winName = "popWin";
		if(typeof(oThis) == 'object') {
			url = oThis.href;
		} else {
			url = oThis;
		}
		if(name)
			winName = name;
		var popupWin = window.open(url, winName, "width=" + sWidth + ", height=" + sHeight + ", status=0, resizable=0");
		return false;
	},
	addLoadEvent: function(func) {
		document.observe('dom:loaded',func);
	}, 
	humanFilesize:function(size) {
                
		var i = 0 ;
		var iec = Array("B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");
		while ((size/1024)>1) {
				size = size/1024;
				i++;
			}
		return (Math.round(size*100)/100) + iec[i];
	},
	openPage:function(url){
		if(!window.open(url)) {
			$('onloadError').innerHTML = '<p>Der ser ud til at previewet blev forhindret. Tjek evt. om der er installeret en popup-blokker. Tryk <a href="'+url+'" target="_blank">her</a> for at se previewet.</p>';
 			$('onloadError').show();
		}
	}
}