rightmenu = Class.create();

rightmenu.prototype = { 
  
	initialize: function(options) {
	
		this.options = options || {};
		this.arrElements = options.elements;
		this.area = $('area');
		this.menu =  $('rightmenu'); 
		this.unitmenu = $('unitmenu');
		this.setupmenu = $('setupmenu');
		this.movingElement = Array();
		this.activeDrag = Array();
		this.activeElement = null;
		this.setupActive = false;
		this.currentX = null;
		this.currentY = null;
		this.resizerId = null;
		this.timer = null;
		this.resizerActive = false;
		this.moverActive = false;
		this.delta = 33;
		this.infoShowing = null;
		this.rightmenuActive = false;
		this.editMode = false;
		this.menuitems = ['menuitem_moveresize','menuitem_move','menuitem_savemoveresize','menuitem_savemove','menuitem_reset','menuitem_resetresize','menuitem_delete','menuitem_setup'];
		this.menuActive = this.options.menuActive;
		this.mouseOnMenu = false;
		if(this.menuActive) {
			this.initMenu();
		}
		
	},
	initMenu: function(){

		this.eventRightMouseClick = this.showMenu.bindAsEventListener(this);
		this.eventClick = this.hideMenu.bindAsEventListener(this);
    
		Event.observe(this.area, "contextmenu", this.eventRightMouseClick);
      Event.observe(this.area, "click", this.eventClick);
		
      document.oncontextmenu = new Function("return false");
    
		//make tree context menu
		this.appendUnitMenu();
		
		//no realod erros in FF
		Event.observe(window, 'unload', Event.unloadCache, false); 
		
		if(this.options.AutoUpdate) {
			this.autoUpdater = setInterval(this.checkStatus.bind(this),10000);
		}
    
	},
	onMenu: function(){
		this.mouseOnMenu = true;
	},
	offMenu: function(){
		this.mouseOnMenu = false;
	},
	disableMenu: function(){
    
		Event.stopObserving(this.area, "contextmenu", this.eventRightMouseClick);
		
		Element.hide(this.menu);
		
		void(document.oncontextmenu=null);
	},
	enableMenu: function(){
		Event.observe(this.area, "contextmenu", this.eventRightMouseClick);
		document.oncontextmenu = new Function("return false");
	},
	resizeElement: function(){
		
		if(!this.resizerActive) {
		
			this.resizerActive = true;
			this.resizerId = this.activeElement;
			this.movingElement[this.activeElement] = true;

			var offset = Position.cumulativeOffset($('element_' + this.activeElement));
			var dim = $('element_' + this.activeElement).getDimensions();
			
			var icon_x = dim.width + offset[0];
			var icon_y = dim.height + offset[1];
			
			var type = this.arrElements[this.activeElement].type;
			var name = this.arrElements[this.activeElement].name;
			
			this.resizeOriginal = {name:name,type:type,icontype:1,icon_width: dim.width,icon_height:dim.height,icon_x:offset[0],icon_y:offset[1]};
			
			
			
			this.resizer = new Cropper.Img('backgroundImage',{
										displayOnInit: true,
										onloadCoords: { x1: offset[0], y1: offset[1] - this.delta, x2: icon_x, y2: icon_y - this.delta }});
										
			
			if($('element_' + this.activeElement))
				$('element_' + this.activeElement).remove();
			if($('info_' + this.activeElement))
				$('info_' + this.activeElement).remove();
			
			this.hideMenu();
			
		} else {
			alert('Du er allerede igang med en anden justering, afslut denne først.');
		}
		
	},
	resetResizer: function(){
		
		this.redrawElement(this.activeElement,this.resizeOriginal);
		this.checkStatus();
		this.hideAll();
		
	},
	redrawElement: function(id,options){
		
		var newElement = document.createElement('div');
		
		newElement.setAttribute('id','element_'+id);
			
		$('elements').appendChild(newElement);

		if(options.icontype == 2) {
			if(options.type == 'network')
				$('element_'+id).className = 'icon_ok_icon';
			else	
				$('element_'+id).className = 'icon_ok';
		} 
		
		Element.setStyle($('element_'+id),{	left:options.icon_x+'px',
														top:options.icon_y+'px',
														position:'absolute'});			
		if(options.icontype != 2) {
			
			Element.setStyle($('element_'+id),{width:options.icon_width+'px',height:options.icon_height+'px'});
			
			$('element_'+id).innerHTML = "<div id='classelement_"+id+"' class='ok'></div>";
			
			if(options.type == 'network') {	
				$('element_'+id).innerHTML+= '<div class="network"></div>';
			}
		}
		
		if(options.type == 'network') {
			Event.observe($('element_'+id), "click", function(){document.location='/?drawing='+id;});
		}
		
		Event.observe($('element_'+id), "contextmenu", this.showUnitMenu.bind(this,id));

		this.movingElement[this.activeElement] = false;
		
		if(this.resizerActive) {
			this.resizerActive = false;
			this.resizer.remove();
		}
		
	},
	saveResizeMenu: function(x,y){
		
		$('unitmenu_name').innerHTML = this.arrElements[this.activeElement].name;
		
		this.hideAll();
		
		this.menu_saveresizer();
		
		y+= this.delta;
		
		this.setPos(this.unitmenu,x,y);
		
		Element.show(this.unitmenu);
	
	},
	menu_maker:function(menu){
		
		var max = this.menuitems.length;
		for(var i = 0;i < max;i++) {
			if(menu.indexOf(this.menuitems[i]) > -1) {
				Element.show(this.menuitems[i]);	
			} else {
				Element.hide(this.menuitems[i]);	
			}
		}
	},
	menu_saveresizer: function(){
		if(this.resizerActive) {
			this.menu_maker(['menuitem_savemoveresize','menuitem_resetresize']);
		} else {
			this.menu_maker(['menuitem_savemove','menuitem_reset']);
		}
	},
	menu_rightmenu:function(){
		if(this.arrElements[this.activeElement].icontype == 1) {
			this.menu_maker(['menuitem_moveresize','menuitem_delete','menuitem_setup']);
		} else {
			this.menu_maker(['menuitem_move','menuitem_delete','menuitem_setup']);	
		}	
	},
	saveResizeElement:function(){
		
		var element = $('cropperSize');
		
		var offset = Position.positionedOffset(element);
		var dim = element.getDimensions();
		
		this.hideAll();
		
		this.movingElement[this.activeElement] = false;
		
		var url = "/services/ajaxhandler.php?action=element&save=true&id=" + this.activeElement;
		url+= "&icon_x="+offset[0]+"&icon_y="+offset[1];
		url+= "&icon_width="+dim.width+"&icon_height="+dim.height;
		
		var options = {icontype:1,type:this.resizeOriginal.type,icon_width:dim.width,icon_height:dim.height,icon_x:offset[0],icon_y:offset[1]+this.delta};
		
		new Ajax.Request(url,{method:'get',onComplete:this.saveElement.bind(this,options)});
		
	},
	showMenu: function(e,elementid){
		
		clearTimeout(this.timer);
		this.timer = setTimeout(this.hideMenu.bind(this),4000);
		
		this.hideStatus();
		
		this.rightmenuActive = true;
		
		//set name 
		var cursor = this.getPosition(e);
			
		this.currentX = cursor.x;
		this.currentY = cursor.y;
		
		var popupDim = this.getPopupPos(this.currentX,this.currentY);
			
		if(!this.resizerActive && !this.moverActive) {
		
			Element.hide(this.setupmenu);
			
			if(elementid > 0) {
				
				this.activeElement = elementid;
				
				$('unitmenu_name').innerHTML =this.arrElements[this.activeElement].name;
				
				this.hideAll();
				
				this.menu_rightmenu();
				
				this.setPos(this.unitmenu,this.currentX,this.currentY);
				
				Element.show(this.unitmenu);
			
			} else if(this.menuActive) {
				
				this.hideAll();
				
				this.setPos(this.menu,this.currentX,this.currentY);
				
				Element.show(this.menu);
			
			}
		} else {
			this.saveResizeMenu(popupDim[0],popupDim[1]);
		}
  	},
  	hideMenu: function(e){
		if(this.menuActive) {
			Element.hide(this.menu);
			Element.hide(this.unitmenu);
			this.unitMenuShowing = false;
		}
  	},
  	newHost: function(){
  		this.resetInput();
  		this.setupActive = true;
		this.hideAll();
		this.setPos($('step1host'),this.currentX,this.currentY);
		Element.show($('step1host'));
		new Draggable($('step1host'),{handle:'step1host_handle'});
  	},
  	resetInput: function(){
  		
  		$('host_name').value = '';
  		$('host_ip').value = '';
  		$('host_servicetype').selectedIndex = 0;
  		$('host_type').selectedIndex = 0;
  		
  		$('drawing_type').selectedIndex = 0;
  		$('drawing_name').value = '';
  	},
  	saveNewHost:function(){
		
  		if($F('host_name').length < 3) {
			alert('Navnet skal bestå af mindst 3 karakterer');
			return false;
  		}
  		
  		var regex = /(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
  		if(!regex.exec($F('host_ip'))) {
  			alert('Indtast venligst en valid ip.');
  			return;
  		} 
  		
  		var url = "/services/ajaxhandler.php?action=element&add=true";
  		url+= "&name="+escape($F('host_name'));
		url+= "&ip="+escape($F('host_ip'));
		url+= "&type=host";
		url+= "&icontypeid="+escape($F('host_type'));
		url+= "&servicetype="+escape($F('host_servicetype'));
		url+= "&parentid=" + this.options.drawingid;
		url+= "&icon_x=" + this.currentX + "&icon_y=" + (this.currentY-this.delta);
		
		new Ajax.Request(url,{method:'get',onComplete:this.addElement.bind(this)});
		
	},
	saveNewElement: function(r){
		alert(r.responseXml);
	},
  	newDrawing: function(){
  		
  		this.resetInput();
  		this.setupActive = true;
		this.hideAll();
		$('posy').value = this.currentY;
		$('posx').value = this.currentX;
		this.setPos($('step1drawing'),this.currentX,this.currentY);
		Element.show($('step1drawing'));
		new Draggable($('step1drawing'),{handle:'step1drawing_handle'});
  	
  	},
  	saveDrawing: function(status,options) {
  		
  		if(status > 0) {
  			
  			//sæt ingang
  			this.hideAll();
			this.activeElement = options.id;
			
			this.arrElements[this.activeElement] = {
				id:options.id,
				name:options.name,
				icontype:options.icontype,
				type:options.type
			}
			
			this.redrawElement(this.activeElement,options);
			
			if(options.icontype == 1) {
				this.resizeElement();
			}
			
  		} else {
  			alert(options.error);	
  		}
  	},
  	hideAll: function(){
  		this.rightmenuActive = false;
  		Element.hide(this.unitmenu);
		Element.hide(this.menu);
		Element.hide(this.setupmenu);
		Element.hide($('step1host'));
		Element.hide($('step1drawing'));
		this.setupActive = false;
  	},
	appendUnitMenu: function(){
	
		var arrElements = document.getElementsByTagName('div');
		var id = 0;
		for(var i = 0;i < arrElements.length;i++) {
			try {
				if(arrElements[i].id) {
					if(arrElements[i].id.substring(0,8) == 'element_') {
						id = arrElements[i].id.substring(8);
						Event.observe(arrElements[i].id, "contextmenu", this.showUnitMenu.bind(this,id));
						Event.observe(arrElements[i].id, "mousemove", this.showStatus.bind(this,id));
						Event.observe(arrElements[i].id, "mouseout", this.hideStatus.bind(this));
					}
				}
			} catch (e) {}
		}
	},
	showUnitMenu: function(id,e){
		this.unitMenuShowing = true;
		this.showMenu(e,id);
	},
	hideStatus: function(e){
		if(this.infoShowing > 0) {
			$('info_'+this.infoShowing).hide();
		}
		this.infoShowing = null;
	},
	showStatus: function(id,e){
		
		if(!this.unitMenuShowing) {
			if(this.infoShowing > 0 && this.infoShowing != id) {
				this.hideStatus();
			}
			
			this.infoShowing = id; 
			
			var cursor = this.getPosition(e);
			
			this.setPos($('info_'+id),cursor.x,cursor.y,{x:15,y:15});
			
			$('info_'+id).show();
		}
	},
	moveElement: function(){
		
		this.movingElement[this.activeElement] = true;
		this.moverActive = true;
		
		if(this.arrElements[this.activeElement].type == 'network') {
			$('element_'+this.activeElement).onclick = function(){return false;};
		}
		var offset = Position.cumulativeOffset($('element_' + this.activeElement));
		
		this.moveOriginal = {icon_x:offset[0],icon_y:offset[1]};
		
		$('element_' + this.activeElement).setStyle({cursor:'move'});
		this.activeDrag[this.activeElement] = new Draggable('element_' + this.activeElement);
		
		this.hideMenu();
	},
	resetMove: function(){
		
		this.movingElement[this.activeElement] = null;
		this.moverActive = null;
		
		this.hideAll();
		this.hideMenu();
		
		var id = this.activeElement;
		Event.observe($('element_'+this.activeElement), "click",function(){document.location="/?drawing="+id;});
		
		Element.setStyle($('element_' + this.activeElement),{left:this.moveOriginal.icon_x+'px',top:this.moveOriginal.icon_y+'px'});
		$('element_' + this.activeElement).setStyle({cursor:'pointer'});
		this.activeDrag[this.activeElement].destroy();
		
		
	},
	saveMoveElement: function(){
		
		this.movingElement[this.activeElement] = false;
		this.moverActive = false;
		
		var elementY = $('element_' + this.activeElement).getStyle('top').replace(/px/,'');
		var elementX = $('element_' + this.activeElement).getStyle('left').replace(/px/,'');
		
		if(this.arrElements[this.activeElement].icontype == 2) {
			$('element_' + this.activeElement).setStyle({cursor:'pointer'});
			elementY -= this.delta;
		}
		
		var url = "/services/ajaxhandler.php?action=element&save=true&id=" + this.activeElement;
		url+= "&icon_x="+elementX+"&icon_y="+elementY;
		url+= "&icon_width=50&icon_height=50";
		
		new Ajax.Request(url,{method:'get',onComplete:this.saveElement.bind(this)});
	},
	setupDrawing:function(id){
		
		this.setupActive = true;
		this.hideAll();
		
		var url = "/services/ajaxhandler.php?action=element&get=true&id=" + id;
		new Ajax.Request(url,{method:'get',onComplete:this.showSetupDrawing.bind(this,id)});
		
	},
	showSetupDrawing: function(id,r) {
		
		var xml = r.responseXML;
		var status = xml.getElementsByTagName('status').item(0).firstChild.data;
		
		if(status > 0) {
			
			var element = xml.getElementsByTagName('element');
			
			var name = element[0].getElementsByTagName('name').item(0).firstChild.data;
			var type = element[0].getElementsByTagName('type').item(0).firstChild.data;
			
			
			$('setup_type').value = 'network';
			$('setup_drawing').value = '1';
			
			$('setup_servicetype_layer').hide();
			$('setup_ip_layer').hide();	
			$('setup_uploader').show();	
			
			$('setup_id').value = id;
			$('setup_name').value = name;
			
			Element.setStyle(this.setupmenu, {position:'absolute',left:0,top:this.delta+'px'});			
			
			Element.show(this.setupmenu);
			new Draggable(this.setupmenu,{handle:'setupmenu_handle'});
		}
	},
	setupElement:function(id){
		
		this.setupActive = true;
		this.hideAll();
		var useId = this.activeElement;
		
		if(id > 0)
			useId = id;
		
		//get info
		var url = "/services/ajaxhandler.php?action=element&get=true&id=" + useId;
		new Ajax.Request(url,{method:'get',onComplete:this.showSetupElement.bind(this,useId)});
		
	},
	showSetupElement: function(id,r){
		
		var xml = r.responseXML;
		var status = xml.getElementsByTagName('status').item(0).firstChild.data;
		
		if(status > 0) {
			
			var element = xml.getElementsByTagName('element');
			
			var name = element[0].getElementsByTagName('name').item(0).firstChild.data;
			var type = element[0].getElementsByTagName('type').item(0).firstChild.data;
			
			if(type == 'host') {
			
				var ip = element[0].getElementsByTagName('ip').item(0).firstChild.data;
				var servicetype = element[0].getElementsByTagName('servicetype').item(0).firstChild.data;
				
				if(servicetype > 0)
					$('setup_servicetype').selectedIndex = servicetype-1;
				
				$('setup_ip').value = ip;
				$('setup_type').value = 'host';
				
				$('setup_servicetype_layer').show();
				$('setup_ip_layer').show();
				$('setup_uploader').hide();
				
			} else {
				
				$('setup_type').value = 'network';
				
				$('setup_servicetype_layer').hide();
				$('setup_ip_layer').hide();
				$('setup_uploader').show();
			
			}
			$('setup_id').value = id;
			$('setup_name').value = name;
			
			this.setPos(this.setupmenu,this.currentX,this.currentY);
			
			Element.setStyle(this.setupmenu, {position:'absolute'});			
			
			Element.show(this.setupmenu);
			new Draggable(this.setupmenu,{handle:'setupmenu_handle'});	
		}
	},
	saveSetup: function(options) {
		if(options.status) {
			this.setupActive = false;
			this.hideAll();	
			if(options.reloadDrawing && options.drawingSrc) {
				$('backgroundImage').src = '/pictures/'+options.drawingSrc;
			} 
		} else {
			alert(options.error);	
		}
	},
	saveElement: function(options){
		
		if(this.activeDrag[this.activeElement]) {
			this.activeDrag[this.activeElement].destroy();
		}
		
		if(options.icontype == 1) {
			this.redrawElement(this.activeElement,options);
			this.addInfo(this.activeElement);
		}
		
		this.checkStatus();
		
		this.hideMenu();
	
	},
	insertElement: function() {
		
		var url = "/services/ajaxhandler.php?action=element&add=true";
		url+= "&parentid=" + this.options.drawingid;
		url+= "&icon_x="+this.currentX+"&icon_y="+this.currentY;
		url+= "&type=network&icontypeid=1&icon_width=50&icon_height=50";
		
		new Ajax.Request(url,{method:'get',onComplete:this.addElement.bind(this)});

	},
	deleteElement: function(){
		
		var url = "/services/ajaxhandler.php?action=element&check_delete=true";
		url+= "&id=" + this.activeElement;
		
		new Ajax.Request(url,{method:'get',onComplete:this.confirmDeleteElement.bind(this,this.activeElement)});
		
	},
	confirmDeleteElement:function(id,r){
		
		var xml = r.responseXML
		var status = xml.getElementsByTagName('status').item(0).firstChild.data;
		
		if(status > 0) {
			if(confirm('Ønsker du at slette dette element?')) {
			
				var url = "/services/ajaxhandler.php?action=element&delete=true";
				url+= "&id=" + this.activeElement;
			
				new Ajax.Request(url,{method:'get',onComplete:this.elementDeleted.bind(this)});
			}	
		} else {
			var errorText = xml.getElementsByTagName('errorText').item(0).firstChild.data;
			alert("Du kan ikke slette dette element da det indeholder følgende:\n"+errorText);
		}
	},
	elementDeleted: function(r){
		var xml = r.responseXML
		var status = xml.getElementsByTagName('status').item(0).firstChild.data;
		if(status > 0) {
			if($('element_' + this.activeElement)) {
				$('element_' + this.activeElement).remove();	
			}
			if($('info_' + this.activeElement)) {
				$('info_' + this.activeElement).remove();
			}
			this.hideAll();
		} else {
			alert('Handlingen kunne ikke udføres');
		}
		
	},
	addElement: function(r) {
		
		var xml = r.responseXML;
		var status = xml.getElementsByTagName('status').item(0).firstChild.data;
		
		if(status > 0) {
			
			var id = xml.getElementsByTagName('id').item(0).firstChild.data;
			var type = xml.getElementsByTagName('type').item(0).firstChild.data;
			var icontype = xml.getElementsByTagName('icontype').item(0).firstChild.data;
			var name = xml.getElementsByTagName('name').item(0).firstChild.data;
		
			this.arrElements[id] = {
					id:id,
					name:name,
					icontype:icontype,
					type:type
			}
			
			this.hideAll();
			this.activeElement = id;
			
			var options = {name:name,icon_width:50,icon_height:50,icon_x:this.currentX,icon_y:this.currentY,type:type,icontype:icontype};

			this.redrawElement(id,options);
			
			if(icontype == 1) {
				this.resizeElement();
			} else {
				this.addInfo(id);
				this.checkStatus();
			}
		}
	},
	addInfo: function(id){
		
		var newElement = document.createElement('div');
		
		newElement.setAttribute('id','info_'+id);
		
		$('infos').appendChild(newElement);
		
		$('info_'+id).className = 'popup';
		
		Element.setStyle($('info_'+id),{display:'none',width:'150px'});
		
		var html = '<div id="infoname_'+id+'"></div>';
		html+= '<div id="infotxt_'+id+'"></div>';
		html+= '<div id="infoip_'+id+'"></div>';
		$('info_'+id).innerHTML = html;
		 
		Event.observe('element_'+id, "mousemove", this.showStatus.bind(this,id));
		Event.observe('element_'+id, "mouseout", this.hideStatus.bind(this));
	
	},
	checkStatus: function(){
		if(!this.setupActive && !this.resizerActive && !this.moverActive) {
			var url = "/services/ajaxhandler.php?action=status&id=" + this.options.drawingid;
			new Ajax.Request(url,{method:'get',onComplete:this.updateElements.bind(this)});
		} else {
			//busy not updating
		}
	},
	updateElements: function(r){
		var xml = r.responseXML;
		var status = xml.getElementsByTagName('status').item(0).firstChild.data;
		
		if(status > 0) {
			var elements = xml.getElementsByTagName('element');
			
			var usedElements = Array();
			
			for(var i = 0;i < elements.length;i++) {
			
				var elementInfo = {
					id:elements[i].getElementsByTagName('id').item(0).firstChild.data,
					name:elements[i].getElementsByTagName('name').item(0).firstChild.data,
					ip:elements[i].getElementsByTagName('ip').item(0).firstChild.data,
					status_all:elements[i].getElementsByTagName('status_all').item(0).firstChild.data,
					status_text:elements[i].getElementsByTagName('status_text').item(0).firstChild.data,
					icontype:elements[i].getElementsByTagName('icontype').item(0).firstChild.data,
					type:elements[i].getElementsByTagName('type').item(0).firstChild.data,
					icon_y:elements[i].getElementsByTagName('icon_y').item(0).firstChild.data,
					icon_x:elements[i].getElementsByTagName('icon_x').item(0).firstChild.data,
					icon_width:elements[i].getElementsByTagName('icon_width').item(0).firstChild.data,
					icon_height:elements[i].getElementsByTagName('icon_height').item(0).firstChild.data
				}
				
				this.arrElements[elementInfo.id] = elementInfo;
				this.updateElement(elementInfo.id);
				
				usedElements[elementInfo.id] = true;
			}
			
			//check if an element is deleted
			this.arrElements.each(function(item){
				if(item != undefined){
					if(!usedElements[item.id]) {
						if($('element_'+item.id)) 
							$('element_'+item.id).remove();
						if($('info_'+item.id)) 
							$('info_'+item.id).remove();
					}
				}
			});
		}
	},
	updateElement:function(id){
		
		if(!$('element_'+id)) {
			//add element ('new element');
			var top = Number(this.arrElements[id].icon_y) + Number(this.delta);
			var options = {name:	this.arrElements[id].name,icontype:this.arrElements[id].icontype,type:this.arrElements[id].type,width:this.arrElements[id].icon_width,height:this.arrElements[id].icon_height,icon_x:this.arrElements[id].icon_x,icon_y:top};
			this.redrawElement(id,options);
		} 

		if(!$('info_'+id)) {
			this.addInfo(id);//add info
		}
		
		//update element	
		var className = "ok";
		
		if(this.arrElements[id].status_all == 1) {
			className = 'warning';
		} else if(this.arrElements[id].status_all == 2) {
			className = "error";
		}
		
		if(this.arrElements[id].icontype == 2) {
			
			var top = Number(this.arrElements[id].icon_y) + Number(this.delta);
			Element.setStyle($('element_'+id),{top:top+'px',left:this.arrElements[id].icon_x+'px'});	
			className = "icon_"+className;
			if(this.arrElements[id].type == 'network') {
				className = className + "_network";
			}
			if($('element_'+id).className != className) {
				$('element_'+id).className = className;
			}
		} else {
			var top = Number(this.arrElements[id].icon_y) + Number(this.delta);
			Element.setStyle($('element_'+id),{top:top+'px',left:this.arrElements[id].icon_x+'px',width:this.arrElements[id].icon_width+'px',height:this.arrElements[id].icon_height+'px'});	
			if($('classelement_'+id).className != className) {
				$('classelement_'+id).className = className;
			}
		}
		
		$('infoname_'+id).innerHTML = this.arrElements[id].name;
		
		if(this.arrElements[id].status_text) {
			$('infotxt_'+id).innerHTML = "<b>Status:</b><br />"+this.arrElements[id].status_text;
		}
		if(this.arrElements[id].ip && this.arrElements[id].type == 'host') {
			$('infoip_'+id).innerHTML = "<b>IP:</b><br />" + this.arrElements[id].ip;
		}
	},
	getPosition:function(e) {
	    e = e || window.event;
	    var cursor = {x:0, y:0};
	    if (e.pageX || e.pageY) {
	        cursor.x = e.pageX;
	        cursor.y = e.pageY;
	    } 
	    else {
	        var de = document.documentElement;
	        var b = document.body;
	        cursor.x = e.clientX + 
	            (de.scrollLeft || b.scrollLeft) - (de.clientLeft || 0);
	        cursor.y = e.clientY + 
	            (de.scrollTop || b.scrollTop) - (de.clientTop || 0);
	    }
	    return cursor;
	},
	getPopupPos:function(mX,mY,oWidth,oHeight,offset){
		
		if(!offset) {
			offset = {x:0,y:0};			
		}
		
		var returnPos = [mX,mY];
		
		oWidth+=offset.x;
		oHeight+=offset.y;
		
		var pWidth = 130;
		
		if(oWidth)
			pWidth = oWidth;
		
		var pHeight = 50;
		
		if(oHeight)
			pHeight = oHeight;
		
		var wDim = this.getWindowDim();
		var wScroll = this.getWindowScroll();
		
		//check right;
		if(((wScroll[0] + wDim[0]) - returnPos[0]) < pWidth) {
			returnPos[0] = returnPos[0] - pWidth;
		} else if(offset.x) {
			returnPos[0]+= offset.x; 
		}
		//check bottom;
		if(((wScroll[1] + wDim[1]) - returnPos[1]) < pHeight) {
			returnPos[1] = returnPos[1] - pHeight;
		} else if(offset.y) {
			returnPos[1]+= offset.y; 
		}
		return returnPos;
	},
	setPos: function(obj,x,y,offset){
		
		var objWidth = obj.getDimensions().width;
		var objHeight = obj.getDimensions().height;

		var pos = this.getPopupPos(x,y,objWidth,objHeight,offset);

		Element.setStyle(obj,{top:(pos[1])+'px',left:(pos[0])+'px'});		
	}
}