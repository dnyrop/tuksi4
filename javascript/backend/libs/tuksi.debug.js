tuksi.debug = Base.extend({
	options:Array(),
	constructor:function(info){
		return "";
		this.base();
		this.info = info;
		this.activePane = "1";
		this.debugDiv = {};
		this.currentRow = 0;
		this.panes = Array('log','sql','tpl','error','warning','js');
		var initDate = new Date();
		this.zeroTime = initDate.getTime(); 
		
		if (!window.console || !console.firebug) {
			this.firebug = false;
		} else {
			this.firebug = true;
		}
		
		this.buildDebugPanel();
		
		if(this.info.num_errors > 0 || this.info.num_warnings > 0) {
			this.showAlert();
		}
		this.appendMenu();
	},
	buildDebugPanel: function(){
		
		this.contentDiv = $('tuksi-debug-content');
		
		//add onlick event
		this.eventClick = this.togglePanel.bindAsEventListener(this);
		//Event.observe("tuksi-debug", "click", this.eventClick);
	},
	appendMenu:function(){
		Event.observe($('tuksi-debug-link-log'), "click",this.setPane.bind(this,'log'));
		Event.observe($('tuksi-debug-link-sql'), "click",this.setPane.bind(this,'sql'));
		Event.observe($('tuksi-debug-link-tpl'), "click",this.setPane.bind(this,'tpl'));
		Event.observe($('tuksi-debug-link-warning'), "click",this.setPane.bind(this,'warning'));
		Event.observe($('tuksi-debug-link-error'), "click",this.setPane.bind(this,'error'));
		Event.observe($('tuksi-debug-link-js'), "click",this.setPane.bind(this,'js'));
	},
	showAlert:function(){
		return "";
		if(this.info.num_errors > 0) {
			var pError = document.createElement('p');
			pError.innerHTML = "<b>"+this.info.num_errors + '</b> error(s)';
			pError.style.cursor = 'pointer';
			Event.observe(pError, "click", this.showContentPane.bind(this,'error'));
			$('tuksi-debug-alert').appendChild(pError);
		} 
		if(this.info.num_warnings > 0) {
			var pWarning = document.createElement('p');
			pWarning.innerHTML = "<b>"+this.info.num_warnings + '</b> warning(s)';
			pWarning.style.cursor = 'pointer';
			Event.observe(pWarning, "click", this.showContentPane.bind(this,'warning'));
			$('tuksi-debug-alert').appendChild(pWarning);
		}
		$('tuksi-debug-alert').show();
		setTimeout(this.hideAlert.bind(this),5000);
	},
	hideAlert:function(){
		new Effect.Opacity($('tuksi-debug-alert'),{from:1.0,to:0.0,duration:0.5});
	},
	setInfo:function(info) {
		this.info = info;
	},
	setDebugInfo:function(debuginfo) {
		this.debuginfo = debuginfo;
	},
	showConsole:function(){
		this.setPane('js');
		this.showPanel();
	},
	showContentPane:function(type){
		this.setPane(type);
		if(!this.contentDiv.visible()){
			this.showPanel();
		}
	},
	togglePanel:function(){
		
		if(!this.contentDiv) {
			this.createContentDiv();
			this.showPanel();
		} else {
			if(this.contentDiv.visible()) {
				this.contentDiv.hide();
			} else {
				this.showPanel();
			}
		}
	},
	showPanel: function(){
		var dim = tuksi.util.getWindowDimensions();
		var left = (dim.width/2) - 400;
		this.contentDiv.setStyle({display:'none',left:left+'px',top:'20px'});
		this.contentDiv.setStyle({display:'block'});
		//new Draggable(this.contentDiv,{handle:'tuksi-debug-drag-handler'});
	},
	setPane: function(pane){
		for(var i = 0,ln = this.panes.length;i < ln;i++) {
			if($('tuksi-debug-'+this.panes[i]).visible()) {
				$('tuksi-debug-'+this.panes[i]).hide();
				$('tuksi-debug-tab-'+this.panes[i]).className= "";
			}
		}
		if(!$('tuksi-debug-'+pane).visible()) {
			$('tuksi-debug-'+pane).show();
			$('tuksi-debug-tab-'+pane).className= "active";
			//$('tuksi-debug-'+pane).scrollTop = 100000000;
		}
	},
	addDebug:function(msg,type){
		if($('tuksi-debug-js')) {
			
			var date = new Date();
			var msTime = date.getTime() - this.zeroTime;
			var p = document.createElement('p');
			var bgcolor = (this.currentRow % 2) ? '#EFEFEF' : '';
			p.className = type;
			//p.style.background = bgcolor;
			p.innerHTML = type + ": <span class=msg>" + msg + "</span><span class=time>" + msTime+"</span>";
			$('tuksi-debug-js').appendChild(p);
			$('tuksi-debug-js').scrollTop = 100000000;
			this.currentRow++;
		}
	},
	log:function(msg){
		if(this.firebug) {
			console.log(msg);
		}
		this.addDebug(msg,'log');
	},
	warning:function(msg){
		
		if(this.firebug) {
			console.warn(msg);
		}
		this.addDebug(msg,'warning');
	},
	error:function(msg){
		
		if(this.firebug) {
			console.error(msg);
		}
		this.addDebug(msg,'error');
	},
	clear:function(){
		$('tuksi-debug-js').innerHTML = "";
	},
	toggleExplain:function (strId){
		if($(strId).visible()) {
			$(strId).style.display = 'none';
		} else {
			$(strId).style.display = 'block';
		}
	},
	showExplain:function (strId){
		$(strId).style.display = 'block';
	},
	hideExplain:function(strId){
		$(strId).style.display = 'none';
	},
	showDebugPopup:function(){
		window.open('/services/popup/debug.php','tuksiDebug',"scrollbars=1,width=600,height=800");
	}
});