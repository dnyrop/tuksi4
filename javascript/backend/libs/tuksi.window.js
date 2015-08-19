tuksi.window = {
	arrWindows:Array(),
	arrUniqueWindows:$H(),
	alert:function(msg) {
		var tpl = new Template(this.getAlertTemplate())
		var data = {message:msg,strOk:'Ok',id:'#{id}'};
		this.generate('Alertbox',tpl.evaluate(data),{width:286,overlay:true});
	},
	confirm:function(msg,options) {
		
		if(!options)
			options = {};
			
		if((typeof options.callback) == "function") {
			this.confirmCallback = options.callback;			
		} else {
			this.confirmCallback = '';
		}
		
		if((typeof options.callbackCancel) == "function") {
			this.confirmCallbackCancel = options.callbackCancel;			
		}else {
			this.confirmCallbackCancel = '';
		}
		
		var tpl = new Template(this.getConfirmTemplate())
		var data = {message:msg,strOk:'Ok',strCancel:'Cancel',id:'#{id}'};
		this.generate('Confirmbox',tpl.evaluate(data),{width:286});
		
	},
	setConfirm:function(windowid,status){
		if(status) {
			if((typeof this.confirmCallback) == "function") {
				this.confirmCallback();
			}
		} else {
			if((typeof this.confirmCallbackCancel) == "function") {
				this.confirmCallbackCancel();
			}
		}
		this.close(windowid);
	},
	popup: function(options) {
		//check if popup is unique
		if(options.isDraggable === false) {
			this.isDraggable = false;
		} else {
			this.isDraggable = true;
		}
		
		var content = options.content || null;
		
		var title = options.title || false;
		
		if(options.ajax) {
			new Ajax.Request(options.url,{
				method:'get',
				onSuccess:this.generateFromAjax.bind(this,title,options.options)
			});
		} else {
			this.generate(title,content,options.options);
		}
	},
	generateFromAjax: function(title,options,t){
		var content = t.responseText;
		this.generate(title,content,options);
	},
	generate: function(title,content,options) {
		
		if(!options)
			options = {};

		if(options.overlay) {
			this.showOverlay();
		}	
			
		var intIndex = this.arrWindows.length + 1;
		var popupContainerId = 'popupWindow_' + intIndex;
		
		this.arrWindows[intIndex] = popupContainerId;
			
		if(options.id){
			if(this.arrUniqueWindows.get(options.id)) {
				return false;
			} else {
				this.arrUniqueWindows.set(options.id,intIndex);
			}
		}
		if($('mainRight')) {	
			var rightOffset = Position.cumulativeOffset($('mainRight'));
			var rightDim = $('mainRight').getDimensions();
			
			var topDim = Element.getDimensions($('rightTop'));
			
			var offsetX = rightOffset[0]  + (rightDim.width / 2);
			var offsetY = topDim.height + 95;
		} else {
			var dim = Element.getDimensions(document.body);
			var offsetX = (dim.width / 2); 
			var offsetY = (dim.height / 2) - 50; 
		}

		var elm = new Element('div', { 'class': 'mPopupWindow', 'id': popupContainerId});
		$('popupWindow').insert(elm);
		
		var elm_content_wrapper = new Element('div');
		var elm_content = this.getMainTemplate().interpolate({id: intIndex, title: title});
		
		elm_content_wrapper.update(elm_content);	
		$(popupContainerId).insert(elm_content_wrapper);
		
		$(popupContainerId).setStyle({	
			position:'absolute',
			top:offsetY+'px'
		});
		
		if(options.width) {
				$(popupContainerId).setStyle({
					width:options.width+'px'
				});
		}
	
		var popup_elm_content_wrapper = new Element('div');
		var popup_elm_content = content.interpolate({id: intIndex});
	
		popup_elm_content_wrapper.update(popup_elm_content);

		$('popupWindowContentContainer_'+intIndex).insert(popup_elm_content_wrapper);
		
		var dim = $(popupContainerId).getDimensions();
		
		offsetX = offsetX - (dim.width/2);
		
		$(popupContainerId).setStyle({
			left:offsetX+'px'
		});
		
		/*var thisIframe = iframecontent;
			thisIframe = thisIframe.replace(/\{\$width\}/,dim.width+'px');
			thisIframe = thisIframe.replace(/\{\$height\}/,dim.height+'px');
		*/
		
		new Draggable($(popupContainerId),{handle:'popupWindowHeader_'+intIndex});
		
		$(popupContainerId).appear({duration:0.3});
		
		if((typeof this.callback) == "function") {
			this.callback();
		}
	},
	close: function(id){
		
		id = parseInt(id);
		
		var popupContainerId = 'popupWindow_' + id;
		//cleanup unique
		var uIndex = this.arrUniqueWindows.index(id);
		
		if(typeof(uIndex) != 'undefined') {
			this.arrUniqueWindows.unset(uIndex);
		}
		if($(popupContainerId)) {
			$(popupContainerId).fade({
				duration:0.3,
				afterFinish:function(){
					$(popupContainerId).remove();
				}
			});
			this.hideOverlay();
		}
	},
	showOverlay:function(){
		if(!$('tuksi_overlay')) {
			this.createOverlay();
		}
		if(!$('tuksi_overlay').visible()) {
			$('tuksi_overlay').appear({
				duration: 0.2, 
				from: 0.0, 
				to: 0.4 
			});
		}
	},
	hideOverlay:function(){
		if($('tuksi_overlay')) {
			$('tuksi_overlay').fade({
				duration: 0.2
		});
		}
	},
	createOverlay:function(){
		
		var objOverlay = new Element('div',{
				id:'tuksi_overlay'
		});
		objOverlay.setStyle({
			background:'#000',
			opacity:0,
			display:'none'
		});
		objOverlay.observe('click',
			function(){return false;
		});
		
		$$('body')[0].insert(objOverlay);
		
		Element.setStyle('tuksi_overlay',{
			height: '100%',
			width:'100%',
			bottom:'0px', 
			right:'0px',
			margin:0,
			padding:0
		});
		
		if (typeof document.body.style.maxHeight === "undefined") {//if IE 6
			$('tuksi_overlay').setStyle({
				position:'absolute'
			});
			$$('body')[0].setStyle({
				overflow:'hidden'
			});
		} else {
			$('tuksi_overlay').setStyle({
				position:'fixed'
			});
		}
	},
	getAlertTemplate: function(){
		var strTpl = '<table class="moduleElementRow" width="100%">';
		strTpl+= '<tbody>';
		strTpl+= '<tr>';
		strTpl+= '<td align="center">#{message}</td>';
		strTpl+= '</tr>';
		strTpl+= '</tbody>';
		strTpl+= '</table>';
		strTpl+= '<table align="right" class="moduleElementRow">';
		strTpl+= '<tbody>';
		strTpl+= '<tr>';
		strTpl+= '<td>';
		strTpl+= '<a onclick="tuksi.window.close(\'#{id}\');return false;" class="buttonType3 iconPositive" href="#"><span><span>#{strOk}</span></span></a>';
		strTpl+= '</td>';
		strTpl+= '</tr>';
		strTpl+= '</tbody>';
		strTpl+= '</table>';
		return strTpl;
	},
	getConfirmTemplate: function(){
		var strTpl = '<table class="moduleElementRow" width="100%">';
		strTpl+= '<tbody>';
		strTpl+= '<tr>';
		strTpl+= '<td align="center">#{message}</td>';
		strTpl+= '</tr>';
		strTpl+= '</tbody>';
		strTpl+= '</table>';
		strTpl+= '<table align="right" class="moduleElementRow">';
		strTpl+= '<tbody>';
		strTpl+= '<tr>';
		strTpl+= '<td>';
		strTpl+= '<a onclick="tuksi.window.setConfirm(\'#{id}\',true);return false;" class="buttonType3 iconPositive" href="#"><span><span>#{strOk}</span></span></a>';
		strTpl+= '</td>';
		strTpl+= '<td>';
		strTpl+= '<a onclick="tuksi.window.setConfirm(\'#{id}\',false);return false;" class="buttonType3 iconNegative" href="#"><span><span>#{strCancel}</span></span></a>';
		strTpl+= '</td>';
		strTpl+= '</tr>';
		strTpl+= '</tbody>';
		strTpl+= '</table>';
		return strTpl;
	},
	getMainTemplate: function(){
		var strTpl = '<div id="popupWindowContent_#{id}">';
		strTpl+= '<div class="windowHeader" id="popupWindowHeader_#{id}">';
		strTpl+= '<h5><span id="popupWindowTitle_#{id}">#{title}</span></h5>';
		strTpl+= '<div class="headerButton"><a href="#" class="buttonTypeX" onclick="tuksi.window.close(\'#{id}\');return false;"></a></div>';
		strTpl+= '</div>';
		strTpl+= '<div class="windowInner">';
		strTpl+= '<div class="windowInnerPadding" id="popupWindowContentContainer_#{id}"></div>';
		strTpl+= '</div>';
		strTpl+= '</div>';
	return strTpl;
	}
}
