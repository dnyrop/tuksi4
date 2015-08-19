var tuksi = {};

tuksi = Base.extend({
	constructor:function(options){
		if(options !== undefined) {
			if(options.debug) {
				tuksi.util.addLoadEvent(this.loadDebug.bind(this));
			} else {
				tuksi.util.addLoadEvent(this.loadEmptyDebug());
			}
		}
	},
	loadDebug:function(){
		this.debug = new tuksi.debug(this.info);	
	},
	loadEmptyDebug:function(){
		this.debug = {};	
		this.debug.log = function(){};	
		this.debug.error = function(){};	
		this.debug.warning = function(){};	
	},
	setInfo:function(info) {
		this.info = info;
	}
});