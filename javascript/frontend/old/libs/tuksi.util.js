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
	addLoadEvent: function(func) {
		var oldonload = window.onload;
		if (typeof window.onload != 'function') {
			window.onload = func;
		} else {
			window.onload = function() {
				oldonload();
				func();
			}
		}
	}
}