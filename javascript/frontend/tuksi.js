var tuksi = {};

tuksi = Base.extend({
	constructor:function(options){
		if(options !== undefined) {
			tuksi.util.addLoadEvent(this.loadEmptyDebug());
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