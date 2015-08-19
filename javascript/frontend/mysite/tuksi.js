// Tuksi JavaScript framework
// Requires: prototype.js
// Author: Andreas Mailand, ama@dwarf.dk


// opret funktion, der udvides nedenfor
function Tuksi(){}

// standard popup-script
// Arguments: objThis, intHeight, intWidth, blnResize, blnScroll, blnMenu, blnLocation, blnToolbar
Tuksi.prototype.popup = function(objArgs){
	var strUrl;
	if (typeof(objArgs.objThis) == "string") {strUrl = objArgs.objThis} else {strUrl=objArgs.objThis.href}
	if (objArgs.blnResize) {objArgs.blnResize="resizable"} else {objArgs.blnResize="resizable=0"}
	if (objArgs.blnScroll) {objArgs.blnScroll="scrollbars"} else {objArgs.blnScroll="scrollbars=0"}
	if (objArgs.blnMenu) {objArgs.blnMenu="menubar"} else {objArgs.blnMenu="menubar=0"}
	if (objArgs.blnLocation) {objArgs.blnLocation="location"} else {objArgs.blnLocation="location=0"}
	if (objArgs.blnToolbar) {objArgs.blnToolbar="toolbar"} else {objArgs.blnToolbar="toolbar=0"}
	//var dwarfPopup = window.open(strUrl, "dwarfPopup", "width=" + intWidth + ", height=" + intHeight + ", " + blnResize + ", " + blnScroll + ", " + blnMenu + ", " + blnLocation + ", " + blnToolbar + "");
	
	window.open(strUrl, "dwarfPopup", "width=" + objArgs.intWidth + ", height=" + objArgs.intHeight + ", " + objArgs.blnResize + ", " + objArgs.blnScroll + ", " + objArgs.blnMenu + ", " + objArgs.blnLocation + ", " + objArgs.blnToolbar + "");
	//return false;
}

// returnerer browserens flashversion
Tuksi.prototype.flashVersion = function(){
	var intMaxFlashVersion = 20;
	var intFlashVersion = 0;
	if (navigator.plugins && navigator.plugins.length) {
		for (var i = 0; i < navigator.plugins.length; i++) {
			if (navigator.plugins[i].name.indexOf('Shockwave Flash') != -1) {
				intFlashVersion = navigator.plugins[i].description.split('Shockwave Flash ')[1].charAt(0);
				break;
			}
		}
	} else if (window.ActiveXObject){
		for (var i = 2; i <= intMaxFlashVersion; i++){
			try {
				objFlash = eval("new ActiveXObject('ShockwaveFlash.ShockwaveFlash." + i + "');");
				if (objFlash) {
					intFlashVersion = i;
				}
			}
			catch(e) {}
		}
	}
	return intFlashVersion;
}

// loader flash object uden om explorers sikkerhedsblokering
//Tuksi.prototype.loadFlash = function(intVersion, strMovieUrl, intWidth, intHeight, blnClickable, strAlternativeImage, strTitle, strAlternativeUrl){
Tuksi.prototype.loadFlash = function(objArgs){
	var strReturn = "";
	// 	var myparameter = typeof oArg.myparameter != 'undefined'? oArg.myparameter : "";
	objArgs.intVersion = typeof objArgs.intVersion == "undefined" ? 0 : objArgs.intVersion;
	
	if(this.flashVersion() >= objArgs.intVersion && objArgs.intVersion == 0){
		if(objArgs.intVersion == 0) objArgs.intVersion = 7;
		strReturn += "\n<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=" + objArgs.intVersion + ",0,0,0\" width=\"" + objArgs.intWidth + "\" height=\"" + objArgs.intHeight + "\" id=\"flashloader\" align=\"middle\">\n";
		strReturn += "<param name=\"allowScriptAccess\" value=\"sameDomain\" />\n";
		strReturn += "<param name=\"movie\" value=\"" + objArgs.strMovieUrl + "\" />\n";
		strReturn += "<param name=\"quality\" value=\"high\" />\n";
		strReturn += "<param name=\"bgcolor\" value=\"#ffffff\" />\n";
		strReturn += "<param name=\"wmode\" value=\"transparent\" />\n";
		strReturn += "<embed src=\"" + objArgs.strMovieUrl + "\" quality=\"high\" bgcolor=\"#ffffff\" width=\"" + objArgs.intWidth + "\" height=\"" + objArgs.intHeight + "\" name=\"flashloader\" align=\"middle\" allowscriptaccess=\"sameDomain\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" wmode=\"transparent\" />\n";
		strReturn += "</object>\n";
		document.write(strReturn);
	} else {
		if (objArgs.strAlternativeImage != ""){
			if (objArgs.blnClickable) {
				strReturn += "<a href=\"" + objArgs.strAlternativeUrl + "\" title=\"" + objArgs.strTitle + "\">\n";
				strReturn += "<img src=\"" + objArgs.strAlternativeImage + "\" alt=\"" + objArgs.strTitle + "\" title=\"" + objArgs.strTitle + "\" />\n";
				strReturn += "</a>\n";
			}	else {
				strReturn += "<img src=\"" + objArgs.strAlternativeImage + "\" alt=\"" + objArgs.strTitle + "\" title=\"" + objArgs.strTitle + "\" />\n";	
			}
			document.write(strReturn);
		} else if (objArgs.strAlternativeUrl && objArgs.strAlternativeUrl != ""){
			document.location.href = objArgs.strAlternativeUrl;
		}
	}
}

// browser detection
Tuksi.prototype.browserData = function(){
	var strBrowserData = navigator.userAgent + " " + navigator.appVersion;
	if (strBrowserData.indexOf("MSIE") != -1 && strBrowserData.indexOf("Macintosh") != -1) {return "MSIE_MAC"}
	else if (strBrowserData.indexOf("MSIE 5.0") != -1 && sBrowserData.indexOf("Windows") != -1) {return "MSIE_50"}
	else if (strBrowserData.indexOf("MSIE 5.5") != -1 && sBrowserData.indexOf("Windows") != -1) {return "MSIE_55"}
	else if (strBrowserData.indexOf("MSIE 6.0") != -1 && sBrowserData.indexOf("Windows") != -1) {return "MSIE_60"}
	else if (strBrowserData.indexOf("Firefox") != -1) {return "Firefox"}
	else if (strBrowserData.indexOf("Safari") != -1) {return "Safari"}
	else {return "Unknown Browser"}
}

// er billeder preloadet?
Tuksi.prototype.blnImagesLoaded = false;

// array med preloaded billeder
Tuksi.prototype.arrImageObjects = [];

// preload images
Tuksi.prototype.preloadImages = function(strImageUrls){
	if(strImageUrls){
		var arrImageUrls = strImageUrls.split(",")
		for (var i=0; i<arrImageUrls.length; i++){
			var objTemp = new Image();
			objTemp.src = arrImageUrls[i].replace(" ", "");
			this.arrImageObjects[this.arrImageObjects.length] = objTemp;
		}
		this.blnImagesLoaded = true;
	}
}

// udskift billede med preloadet billede
Tuksi.prototype.swapImage = function(objThis, intSwapToImage){
	if (objThis && this.blnImagesLoaded){
		if (typeof(intSwapToImage) == "number"){
			objThis.src = this.arrImageObjects[intSwapToImage].src;
		} else {
			for (var i = 0; i<this.arrImageObjects.length; i++){
				if (this.arrImageObjects[i].src.indexOf(intSwapToImage) != -1 ){
					objThis.src = this.arrImageObjects[i].src;
				}
			}
		}
	}
}

// udskift billede med preloadet billede
Tuksi.prototype.replacePng = function(){
	if (this.browserData().indexOf("MSIE") != -1 && this.browserData() != "MSIE_MAC"){
			var arrImgs = document.getElementsByTagName("img");
			var strImgSrc;
			for(var i = 0; i<arrImgs.length; i++){
				strImgSrc = arrImgs[i].src.toLowerCase()
			if(strImgSrc.lastIndexOf(".png") != -1){
				arrImgs[i].style.filter = 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src=' + arrImgs[i].src + ', sizingMethod="image")';
				arrImgs[i].src = "/images/graphics/gx_blank.gif";
			}
		}
	}
}
Tuksi.prototype.showExplain = function (strId){
	$(strId).style.display = 'block';
}
Tuksi.prototype.hideExplain = function(strId){
	$(strId).style.display = 'none';
}

// instantier objekt
var tuksi = new Tuksi();


