tuksi.slideshow = tuksi.extend({
	options:Array(),
	constructor:function(images,container,options){
		this.base();
		
		this.images = images;
		this.nbImages = this.images.length;
		this.container = container;
		this.currentImage = 0;
		this.setOptions(options);
		tuksi.util.addLoadEvent(this.init.bind(this));
		this.active = true;
		this.layerBehind = 2;
		this.layerFront = 1;
		this.transActive = false;
		this.timer = null;
		this.id = Math.floor(Math.random()*10000);
		this.transitions = ['fade','blind','slide'];
		this.activeEffects = Array();
		
	},
	setOptions: function(){
		options = arguments[0] || {};
		this.time = options.time || 3000;
		this.transtime = options.transtime || 2;
		this.transition = options.transition || 'fade';
		if(options.startatonce !== undefined) {
			this.startatonce = options.startatonce;
		} else {
			this.startatonce = true;
		}
	},
	clear:function(){
		//stop pending transitions
		clearInterval(this.timer);
		//stop current transitions
		for(var i = 0,ln = this.activeEffects.length;i < ln;i++){
			this.activeEffects[i].cancel();
		}
		//empty active transitions
		this.activeEffects = Array();
	},
	changeTransition:function(trans){
		this.transition = trans;
		this.clear();
		this.rebuild();
		this.start();
	},
	changeToRandom:function(){
		//Math.floor(Math.random()*3);
	},
	init:function(){
		//always load next image for smooth trans
		this.rebuild();
		if(this.startatonce)
			this.start();
	},
	rebuild:function(){
		var nextImage = this.getNext();
		var imgHtml = '<div id="'+this.id+'_div2" style="position:absolute;z-index:1000;"><div><img id="'+this.id+'_img2" src="' + this.images[nextImage].src + '" title="' + this.images[nextImage].name + '" alt="' + this.images[nextImage].name + '"></div></div>';
		imgHtml+= '<div id="'+this.id+'_div1"  style="position:absolute;z-index:1100;"><div><img id="'+this.id+'_img1" src="' + this.images[this.currentImage].src + '" title="' + this.images[this.currentImage].name + '" alt="' + this.images[this.currentImage].name + '"></div></div>';
		$(this.container).innerHTML = imgHtml;
	},
	start:function(){
		this.active = true;
		this.timer = setTimeout(this.doTransition.bind(this),this.time);
	},
	stop:function(){
		this.active = false;
		this.clear();
		this.rebuild();
	},
	doTransition:function(){
		if(this.active) {
			this.transActive = true;
			this.currentImage = this.getNext();
			$(this.id+'_div'+this.layerBehind).show();
			switch(this.transition) {
				case 'blind': 
					this.activeEffects.push(Effect.BlindUp($(this.id+'_div'+this.layerFront),{
						duration:this.transtime,
						afterUpdate:this.checkBlind.bind(this),
						afterFinish:this.afterTransition.bind(this)
					}));
					break;
				case 'slide':
					this.activeEffects.push(Effect.SlideUp($(this.id+'_div'+this.layerFront),{
						duration:this.transtime,
						afterFinish:this.afterTransition.bind(this)
					}));
					break;
				case 'fade':
					this.activeEffects.push(Effect.Fade($(this.id+'_div'+this.layerBehind),{
						from:0,
						to:1,
						duration:this.transtime
					}));
					this.activeEffects.push(Effect.Fade($(this.id+'_div'+this.layerFront),{
						from:1,
						to:0,
						duration:this.transtime,
						afterFinish:this.afterTransition.bind(this)
					}));
					break;
				default:
					this.afterTransition();
			}
		}	
	},
	afterTransition:function(){
		this.transActive = false;
		this.activeEffects = Array();
		$(this.id+'_div'+this.layerFront).hide();
		this.setImage();
		if(this.active) {
			this.timer = setTimeout(this.doTransition.bind(this),this.time);
		}
	},
	checkBlind:function(obj){
		if((obj.currentFrame / obj.totalFrames) > 0.95) {
			$(this.id+'_div'+this.layerFront).hide();
		}
	},
	getNext:function(){
		if(this.currentImage == (this.nbImages - 1)) {
			return 0;
		} else {
			return (this.currentImage + 1);
		}
	},
	next:function(){
		this.clear();
		this.currentImage = this.getNext();
		this.setCurrentImage();
		this.setImage();
	},
	getPrev:function(){
		if(this.currentImage == 0) {
			return (this.nbImages - 1);
		} else {
			return (this.currentImage - 1);
		}
	},
	prev:function(){
		this.currentImage = this.getPrev();
		this.setCurrentImage();
		this.setImage();
	},
	goto:function(nb){
		nb = nb - 1;
		if(nb >= 0 && nb <= this.nbImages) {
			this.active = false;
			this.clear();
			this.currentImage = nb;
			this.rebuild();
			this.setCurrentImage();
			this.setImage();
		}
	},
	setCurrentImage:function(){
		$(this.id+'_img'+this.layerBehind).src = this.images[this.currentImage].src;
		$(this.id+'_img'+this.layerBehind).alt = this.images[this.currentImage].name;
		$(this.id+'_img'+this.layerBehind).title = this.images[this.currentImage].name;
	},
	setImage: function() {
		
		var tmpVal = this.layerFront;
		this.layerFront = this.layerBehind;
		this.layerBehind = tmpVal;
		
		var nextImage = this.getNext();
		
		$(this.id+'_div'+this.layerFront).setStyle({zIndex:1100});
		$(this.id+'_div'+this.layerBehind).setStyle({zIndex:1000});
		
		var nextImage = this.getNext();
		
		$(this.id+'_img'+this.layerBehind).src = this.images[nextImage].src;
		$(this.id+'_img'+this.layerBehind).alt = this.images[nextImage].name;
		$(this.id+'_img'+this.layerBehind).title = this.images[nextImage].name;
		
		objTuksi.debug.log("tuksi.slideshow[changing to:"+this.images[nextImage].src+"]");
	}
});