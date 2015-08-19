// Tuksi Frontend - JavaScript
// Version 2006-04-13-AMA

/*
Tuksi Frontend basic JavaScript functions

getElementHeight(sElementId)
getElementWidth(sElementId)
setElementHeight(sElementId, iHeight)
setElementWidth(sElementId, iWidth)
getWindowWidth()
getWindowHeight()
getScrollWindowHeight()
setPopup(oThis, sHeight, sWidth)
getMouseX(oEvent)
getMouseY(oEvent)
getFlashVersion()
getBrowserData()
setDisplayElement(sElementId)
setPreloadImages(sImageUrls (comma-separated))
setSwapImage(oThis, iSwapToImage)
setPng()
setAlert(aAlerts)
*/

//alert("bum");

function wak(){
	alert("wak");
}


function getElementsByClassName(oElm, sTagName, oClassNames){
    var aElements = (sTagName == "*" && document.all)? document.all : oElm.getElementsByTagName(sTagName);
    var aReturnElements = new Array();
    var aRegExpClassNames = new Array();
    if(typeof oClassNames == "object"){
        for(var i=0; i<oClassNames.length; i++){
            aRegExpClassNames.push(new RegExp("(^|\\s)" + oClassNames[i].replace(/\-/g, "\\-") + "(\\s|$)"));
        }
    }
    else{
        aRegExpClassNames.push(new RegExp("(^|\\s)" + oClassNames.replace(/\-/g, "\\-") + "(\\s|$)"));
    }
    var oElement;
    var bMatchesAll;
    for(var j=0; j<aElements.length; j++){
        oElement = aElements[j];
        bMatchesAll = true;
        for(var k=0; k<aRegExpClassNames.length; k++){
            if(!aRegExpClassNames[k].test(oElement.className)){
                bMatchesAll = false;
                break;                      
            }
        }
        if(bMatchesAll){
            aReturnElements.push(oElement);
        }
    }
    return (aReturnElements)
}


// Returns the height of an element with sElementId as id
function getElementHeight(sElementId){
	var oElement;
	if (typeof(sElementId) != "object"){
		oElement = document.getElementById(sElementId);
	} else {
		oElement = sElementId;
	}
	if (oElement){ return oElement.offsetHeight }
}

// Returns the width of an element with sElementId as id
function getElementWidth(sElementId){
	var oElement;
	if (typeof(sElementId) != "object"){
		oElement = document.getElementById(sElementId);
	} else {
		oElement = sElementId;
	}
	if (oElement){ return oElement.offsetWidth}
}

// Sets a specific height (iHeight) on an element with sElementId as id
function setElementHeight(sElementId, iHeight){
	if (typeof(sElementId) != "object"){
		oElement = document.getElementById(sElementId);
	} else {
		oElement = sElementId;
	}
	if (oElement){
		oElement.style.height = iHeight + "px";
	}
}

// Sets a specific width (iHeight) on an element with sElementId as id
function setElementWidth(sElementId, iWidth){
	if (typeof(sElementId) != "object"){
		oElement = document.getElementById(sElementId);
	} else {
		oElement = sElementId;
	}
	if (oElement){
		oElement.style.width = iWidth + "px";
	}
}

// Returns width of browser window
function getWindowWidth(){
	var oHtml = document.getElementsByTagName("HTML")[0];
	if (oHtml) {
		return oHtml.offsetWidth
	}
}

// Returns height of browser window
function getWindowHeight(){
	var oHtml = document.getElementsByTagName("HTML")[0];
	if (oHtml) {
		switch(getBrowserData()){
			case "Safari":
			case "MSIE_50":
			case "MSIE_55":
			case "MSIE_60":
				return oHtml.offsetHeight
			break;
			
			case "Firefox":
				return oHtml.clientHeight
			break;
			
			case "MSIE_MAC":
				return document.body.clientHeight;
			break;
		}
	}
}

// Returns amount of pixels scrolled from the top
function getScrollWindowHeight(){
	var oBody = document.getElementsByTagName("BODY")[0];
	var oHtml = document.getElementsByTagName("HTML")[0];
	if (oBody && oHtml) {
		switch(getBrowserData()){
			case "Firefox":
			case "MSIE_60":
				return oHtml.scrollTop;
			break;
			
			default:
				return oBody.scrollTop;
		}
	}
}

// Opens a popup - <a href="http://dwarf.dk/ onclick="return setPopup(this, 300, 300)">
function setPopup(oThis, sHeight, sWidth, bResize, bScroll, bMenu, bLocation, bToolbar){
	var sUrl;
	if(typeof(oThis) == "string"){sUrl = oThis}else{sUrl=oThis.href}
	if(bResize){bResize="resizable"}else{bResize="resizable=0"}
	if(bScroll){bScroll="scrollbars"}else{bScroll="scrollbars=0"}
	if(bMenu){bMenu="menubar"}else{bMenu="menubar=0"}
	if(bLocation){bLocation="location"}else{bLocation="location=0"}
	if(bToolbar){bToolbar="toolbar"}else{bToolbar="toolbar=0"}
	var dwarfPopup = window.open(sUrl, "dwarfPopup", "width=" + sWidth + ", height=" + sHeight + ", " + bResize + ", " + bScroll + ", " + bMenu + ", " + bLocation + ", " + bToolbar + "");

	return false;
}

// Returns x coordinate of mousepointer
function getMouseX(oEvent){
	var x = oEvent.clientX;
	if (!x){x = 0}
	return x;
}

// Returns y coordinate of mousepointer
function getMouseY(oEvent){
	var y = oEvent.clientY;
	if (!y){y = 0}
	return y;
}

// Returns major flash version
function getFlashVersion(){
	var iMaxFlashVersion = 20;
	var iFlashVersion = 0;
	if (navigator.plugins && navigator.plugins.length) {
		for (var i = 0; i < navigator.plugins.length; i++) {
			if (navigator.plugins[i].name.indexOf('Shockwave Flash') != -1) {
				iFlashVersion = navigator.plugins[i].description.split('Shockwave Flash ')[1].charAt(0);
				break;
			}
		}
	} else if (window.ActiveXObject) {
		for (var i = 2; i <= iMaxFlashVersion; i++) {
			try {
				oFlash = eval("new ActiveXObject('ShockwaveFlash.ShockwaveFlash." + i + "');");
				if( oFlash ) {
					iFlashVersion = i;
				}
			}
			catch(e) {}
		}
	}
	return iFlashVersion
}

// Show or hides an element with sElementId as id
function setDisplayElement(sElementid){
	var oElement = document.getElementById(sElementid);
	if (oElement){
		if(oElement.style.display == "block" || oElement.style.display == ""){
			oElement.style.display = "none";
		} else {
			oElement.style.display = "block";
		}
	}
}

// Returns browser name
function getBrowserData(){
	var sBrowserData = navigator.userAgent + " " + navigator.appVersion
	if (sBrowserData.indexOf("MSIE") != -1 && sBrowserData.indexOf("Macintosh") != -1) { return "MSIE_MAC" }
	else if (sBrowserData.indexOf("MSIE 5.0") != -1 && sBrowserData.indexOf("Windows") != -1) {	return "MSIE_50" }
	else if (sBrowserData.indexOf("MSIE 5.5") != -1 && sBrowserData.indexOf("Windows") != -1) { return "MSIE_55" } 
	else if (sBrowserData.indexOf("MSIE 6.0") != -1 && sBrowserData.indexOf("Windows") != -1) { return "MSIE_60" } 
	else if (sBrowserData.indexOf("Firefox") != -1) { return "Firefox" } 
	else if (sBrowserData.indexOf("Safari") != -1) { return "Safari" } 
	else { return "Unknown Browser"	}
}

// Preload all images in sImageUrls - url absolute to root e.g. '/images/menu/bn_buttonActive_1.gif'
var aImageObjects = [];
var bLoaded = false;
function setPreloadImages(sImageUrls){
	if(sImageUrls){
		var aImageUrls = sImageUrls.split(",")
		for (var i=0; i<aImageUrls.length; i++){
			var oTemp = new Image();
			oTemp.src = aImageUrls[i].replace(" ", "");
			aImageObjects[aImageObjects.length] = oTemp;
		}
		bLoaded = true;
	}
}

// Swaps source of oThis to a preloaded image in aImageObjects[] - either by directly referring or by image name
function setSwapImage(oThis, iSwapToImage){
	if(oThis && bLoaded){
		if(typeof(iSwapToImage) == "number"){
			oThis.src = aImageObjects[iSwapToImage].src;
		} else {
			for(var i = 0; i<aImageObjects.length; i++){
				if(aImageObjects[i].src.indexOf(iSwapToImage) != -1 ){
					oThis.src = aImageObjects[i].src;
				}
			}
		}
	}
}

// Replace all inline PNGs with filter property and set clear gif as src (only works in ie6)
function setPng(){
	if(getBrowserData().indexOf("MSIE") != -1 && getBrowserData() != "MSIE_MAC"){
		var aImgs = document.getElementsByTagName("IMG");
		var sImgSrc;
		for(var i = 0; i<aImgs.length; i++){
			sImgSrc = aImgs[i].src.toLowerCase()
			if(sImgSrc.lastIndexOf(".png") != -1){
				aImgs[i].style.filter = 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src=' + aImgs[i].src + ', sizingMethod="image")';
				aImgs[i].src = "/images/graphics/gx_blank.gif";
			}
		}
	}
}

// Display inline error popup
function setAlert(aAlerts){
	var oBody = document.getElementsByTagName("BODY")[0];
	var oAlert = document.getElementById("alert");
	
	if(oBody){
		// remove existing alerts
		if(oAlert){
			oBody.removeChild(oAlert);
		}
	
		// is alerts array or string?
		// generate html for displaying the errors
		var sAlert = "";
		if(typeof(aAlerts) == "object"){
			for(var i = 0; i<aAlerts.length; i++){
				sAlert += "<li>" + aAlerts[i] + "</li>";
			}
		} else {
			sAlert += "<li>" + aAlerts + "</li>";
		}
		
		// create elements for alert
		var oAlert = document.createElement("DIV");
		oAlert.id = "alert";
		
		var oAlertContentList = document.createElement("UL");
		oAlertContentList.innerHTML = sAlert;
		
		var oAlertContentClose = document.createElement("A");
		oAlertContentClose.innerHTML = "luk";
		oAlertContentClose.onclick = function(){
			document.getElementById("alert").style.display = "none";
			return false;
		}
		
		var oAlertContent = document.createElement("DIV");
		oAlertContent.className = "alertcontent";
		oAlertContent.appendChild(oAlertContentList);
		oAlertContent.appendChild(oAlertContentClose);
		
		var oAlertShadow = document.createElement("DIV");
		oAlertShadow.className = "alertshadow";
		
		// append elements for alert
		oAlert.appendChild(oAlertContent);
		oAlert.appendChild(oAlertShadow);
				
		// append alert to body	
		oBody.appendChild(oAlert);
		
		// placement of error
		oAlert.style.visibility = "hidden";	
		oAlert.style.display = "block";
		oAlert.style.top = getScrollWindowHeight() + 150 + "px";
		oAlert.style.left = (getWindowWidth() / 2) - (getElementWidth(oAlert.getElementsByTagName("DIV")[0]) / 2) + "px";
		
		// update the height of the shadow
		oAlertShadow.style.height = getElementHeight(oAlertContent) + "px";
		
		// make alert visible
		oAlert.style.visibility = "visible";
	}
}

