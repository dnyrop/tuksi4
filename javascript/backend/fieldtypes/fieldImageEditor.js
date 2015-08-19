var imageEditor = {
	setup:function(options){
		this._popup = false;
		this.doFullRotate = options.doFullRotate || false;
		this.isSaveable = options.isSaveable || false;
		this.notSaveableMessage = options.notSaveableMessage || 'couldt save';
		this.rotateimages = options.rotateimages || false;
		this._initvalue = 1;
		this.currentRotateImg = 1;
		if(options.cropperActive) {
			this.cropper = cropImageManager;
			this.cropperSetup(options.cropper);
		}
	},	
	doAction:function (action) {
		if(this._popup == false) {
			if(action == 'showadjust'){
				this.showAdjust();
			} else if(action == 'showrotate'){
				this.showRotate();
			}else if(action == 'showcropper') {
				this.cropper.attachCropper(this.cropperOptions);
				$('cropperPreviewBtn').show();
			}else if(action == 'save') {
				this.save();
			}else if(action == 'crop'){
				if(this.cropper && this.cropper.curCrop != null) {
					document.sizeForm.editorAction.value = 'crop';
					document.sizeForm.submit();
				}
		 	} else {
				document.sizeForm.editorAction.value = action;
				document.sizeForm.submit();
			}
		}
		return false;
	},
	save:function(){
		if(this.cropper && this.cropper.curCrop != null) {
			document.sizeForm.editorAction.value = "savecrop";
			document.sizeForm.submit();
		} else if(this.isSaveable) {
			document.sizeForm.editorAction.value = "save";
			document.sizeForm.submit();
		} else {
			tuksi.window.alert(this.notSaveableMessage);
		}
	},
	showRotate:function() {
		//new Effect.Opacity('content',{ duration: 0.0, from: 0.0, to: 0.3 });
		Element.show('rotateContainer');
		if(this.cropper) {
			this.cropper.removeCropper();
			$('cropperPreviewBtn').hide();
		}
		this._popup = true;
	},
	hideRotate:function(){
		
		//new Effect.Opacity('content',{ duration: 0.0, from: 0.3, to: 1.0 });
		Element.hide('rotateContainer');
		this._popup = false;
		
	},
	doRotate:function(){
		if(this.currentRotateImg != this._initvalue) {
			$( 'rotatedegrees' ).value = this.currentRotateImg;
			document.sizeForm.editorAction.value='rotate';
			document.sizeForm.submit();
		} else {
			this.hideRotate();
		}
	},
	rotate:function(type) {
		var next = 0;
		if(this.doFullRotate == 1) {
			if(type == 'clock') {
				next = (this.currentRotateImg == (this.rotateimages.length-1)) ? 1 : this.currentRotateImg + 1;
			}else {
				next = (this.currentRotateImg == 1) ? this.rotateimages.length-1 : this.currentRotateImg - 1;
			}
		} else {
			if(type == 'clock') {
				next = (this.currentRotateImg == 3) ? 1 : 3;
			}else {
				next = (this.currentRotateImg == 1) ? 3 : 1;
			}	
		}
		
		$('rotateImage').src = this.rotateimages[next];
		this.currentRotateImg = next;
	},
	cropperSetup:function(options){
		this.cropperOptions = {};
		if(options.ratio) {
			this.cropperOptions.ratioDim = {x:options.ratio.x, y:options.ratio.y};
		}
		if(options.minWidth) {
			this.cropperOptions.minWidth = options.minWidth;
		}
		if(options.minHeight) {
			this.cropperOptions.minHeight = options.minHeight;
		}
		if(options.displayOnInit) {
			this.cropperOptions.displayOnInit = true;
		}
		this.cropperOptions.onloadCoords = options.onloadCoords;
		this.cropperOptions.onEndCrop = onEndCrop; 
	}
};

var cropImageManager = {
	curCrop: null,
	init: function() {
		this.attachCropper();
	},
	onChange: function( e ) {
		var vals = $F( Event.element( e ) ).split('|');
		this.setImage( vals[0], vals[1], vals[2] ); 
	},
	setImage: function( imgSrc, w, h ) {
		$( 'testImage' ).src = imgSrc;
		$( 'testImage' ).width = w;
		$( 'testImage' ).height = h;
		this.attachCropper();
	},
	attachCropper: function(options) {
		Element.hide('rotateContainer');
		//new Effect.Opacity('content',{ duration: 0.0, from: 0.0, to: 1.0 });
		if( this.curCrop == null ) {
			this.curCrop = new Cropper.Img( 'testImage', options);
		} else {
			this.curCrop.reset();
		}
	},
	removeCropper: function() {
		if( this.curCrop != null ) {
			this.curCrop.remove();
		}
	},
	resetCropper: function() {
		this.attachCropper();
	}
};

function onEndCrop( coords, dimensions ) {
	$( 'x1' ).value = coords.x1;
	$( 'y1' ).value = coords.y1;
	$( 'x2' ).value = coords.x2;
	$( 'y2' ).value = coords.y2;
	$( 'width' ).value = dimensions.width;
	$( 'height' ).value = dimensions.height;
}