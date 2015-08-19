tuksi.window = {
	alert:function(msg) {
		alert(msg);
	},
	confirm:function(msg) {
		return confirm(msg);
	},
	popup: function(options) {
		
		if(options.isDraggable === false) {
			this.isDraggable = false;
		} else {
			this.isDraggable = true;
		}
		
		var fromajax = options.fromajax || false;
		var placement = options.placement || 'body';
		var content = options.content || null;
		var querystring = options.querystring || null;
		this.noIframe = options.noIframe || false;
		this.callback = options.callback || false;
		
		if(fromajax) {
			
			var url = "/services/ajaxform.php?formelement=" + options.ajaxelement;
			
			if(querystring) {
				for(var i in querystring) {
					if(i != "toJSONString") {
						url+= "&"+i+"="+escape(querystring[i]);
					}
				}
			}
			new Ajax.Request(url,{
				method:'get',
				onSuccess:this.generateFromAjax.bind(this,placement)
			});
		} else {
			this.generate(content,placement);	
		}
	},
	generateFromAjax: function(placement,t){
		var content = t.responseText;
		this.generate(content,placement);
	},
	generate: function(content,placement) {
		
		var iframecontent = '<iframe id="tuksi_popupbox_iframe" src="/cmsscripts/empty.html" scrolling="no" frameborder="0" style="position:absolute;width:{$width};height:{$height};top:0;border:none;z-index:0;"></iframe>';
		
		var popupName = '';
		
		if(placement == 'sessionConfirm') {
			
			popupName = 'tuksi_sessionpopupbox';
			
			var popY = 200;
			var popX = Element.getDimensions(document.body).width / 2 - 210;
			
			
		} else {
			
			popupName = 'tuksi_popupbox';
			
			if(placement == 'body') {
				var popY = 27;
				var popX = Element.getDimensions($('main')).width - 510;
				
			} else if(placement == 'center') {
			
				var popY = 200;
				var winDim = tuksi.util.getWindowDimensions();
				var popX = winDim.width / 2 - 210;
				
			} else if(placement == 'media') {
			
				var popY = 100;
				var popX = Element.getDimensions($('main')).width / 2 - 210;
				
			} else {
				
				var scroll = Position.realOffset($('main'));
				
				var pos = Position.cumulativeOffset($(placement));
				
				var cHeigth = $('main').getStyle('height').replace("px","");
				
				var popY = pos[1] - scroll[1];
				var popX = pos[0];

				var d = pos[1]-scroll[1] - cHeigth;
				
				if(d > -150) {
					popY-= 180;
				}
			}
		}
		
		if(!$(popupName)) {

			var tuksi_popupbox = document.createElement('div');
			tuksi_popupbox.setAttribute('id',popupName);
			
			if(popupName == 'tuksi_sessionpopupbox') {
				document.body.appendChild(tuksi_popupbox);
			} else {
				$('popuplayer').appendChild(tuksi_popupbox);
			}
			
			//$(popupName).setStyle({padding:0,margin:0,zIndex:10000,overflow:'auto'});
			$(popupName).setStyle({padding:0,margin:0,zIndex:10000});
		}
		
		Element.setStyle(popupName, {position:'absolute',left:popX+'px',top:popY+'px'} );
		
		$(popupName).setStyle({display:'block'});	
			
		new Effect.Opacity(popupName,{ duration: 0.0, from: 0.0, to: 1.0 });
		
		//$(popupName).innerHTML = "<div style='z-index:10000;position:relative;overflow:auto;'>"+content;
		$(popupName).innerHTML = "<div style='z-index:10000;position:relative;'>"+content;
		
		var dim = Element.getDimensions(popupName);
		
		if(!this.noIframe) {
			var thisIframe = iframecontent;
			
			thisIframe = thisIframe.replace(/\{\$width\}/,dim.width+'px');
			thisIframe = thisIframe.replace(/\{\$height\}/,dim.height+'px');
		} else {
			var thisIframe = "";
		}	
		
		$(popupName).innerHTML = $(popupName).innerHTML + thisIframe +"</div>";
		if(this.isDraggable) {
			new Draggable(popupName,{handle:'tuksi_divpopupheader'});
		}
		
		if((typeof this.callback) == "function") {
			this.callback();
		}
	},
	
	hide: function(){
		if($('tuksi_popupbox')) {
			$('tuksi_popupbox').innerHTML = '';
			//hack for IE dunno why
			$('tuksi_popupbox').setStyle({visibility:'hidden'});
			$('tuksi_popupbox').setStyle({display:'none'});
			$('tuksi_popupbox').setStyle({visibility:'visible'});
		}
	},
	
	hideSess: function() {
		if($('tuksi_sessionpopupbox')) {
			$('tuksi_sessionpopupbox').innerHTML = '';
			$('tuksi_sessionpopupbox').setStyle({display:'none'});
		}
	},
	
	getYPos: function(){
		return Try.these(
			function (){return document.documentElement.scrollTop;},
			function (){return window.pageYOffset;}
		) || false;
	}
}