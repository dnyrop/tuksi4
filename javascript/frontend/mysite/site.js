// Site functionality
// Requires: prototype.js + tuksi.js
// Author: Andreas Mailand, ama@dwarf.dk



// sIFR setup and replacements
if(typeof sIFR == "function"){
		sIFR.setup();
		sIFR.replaceElement(".mSifrQuoteImage .text h2", named({sFlashSrc: "/flash/HelveticaNeueLightItalic.swf", sColor: "#181818", sWmode: "Transparent"}));
		sIFR.replaceElement(".mSifrQuoteImage .text h3", named({sFlashSrc: "/flash/HelveticaNeueItalic.swf", sColor: "#383838", sWmode: "Transparent", sFlashVars: "textalign=right"}));
	}


/* aktiver javascript - inden indhold er loadet ind
Tuksi.prototype.preInit = function(){

}

// aktiver javascript - efter indhold er loadet ind
Tuksi.prototype.postInit = function(){
tuksi.sifrLoad();
}
*/
