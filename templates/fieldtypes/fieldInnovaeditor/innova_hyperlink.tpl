<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html class="html_frame_right">
<head>
	<title>Innovaeditor</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="/themes/default/stylesheet/innova_editor_popup.css" rel="stylesheet" type="text/css">
{literal}
	<script type="text/javascript">

	if (navigator.appName.indexOf('Microsoft')!=-1) {
		isIE = true;
		isMOZ = false;
	} else {
	    isIE = false;
		isMOZ = true;    	
	}
	
	if (isMOZ)
		dialogArguments = window.opener;
	
			
	function GetElement(oElement,sMatchTag)
		{
		while (oElement!=null&&oElement.tagName!=sMatchTag)
			{
			if(oElement.tagName=="BODY")return null;
			oElement=oElement.parentElement;
			}
		return oElement;
	}
	
	function getSel() {
		var oEditor=dialogArguments.oUtil.oEditor;
		
		if (isIE) {
			var oSel = oEditor.document.selection.createRange();
		} else {
			var oSel = oEditor.getSelection();
		}
		return oSel;
	}
		
	function getEl() {
		oSel = getSel();
			
		if (isIE) {
			if (oSel.parentElement)	
				oEl =GetElement(oSel.parentElement(),"A");
			else 
				oEl=GetElement(oSel.item(0),"A");
		} else {
			var oEl = GetElement(window.opener.getSelectedElement(oSel),"A");//new
		}
		return oEl;
	}
	
	// window.opener	
	function doWindowFocus()
		{
		dialogArguments.oUtil.onSelectionChanged=new Function("realTime()");
	}
		
	function bodyOnLoad() {
		if (isIE) {
			window.onfocus=doWindowFocus;	
			dialogArguments.oUtil.onSelectionChanged=new Function("realTime()");
		} else {
		
			window.onfocus=doWindowFocus;   
    		window.opener.oUtil.onSelectionChanged=new Function("realTime()");
		}
	    

    	realTime();
    	setCorrectTab();
	}
	
	// Indsætter link i HTMLeditor
	function applyHyperlink() {
		if (isIE) {
			applyHyperlinkIE()
		} else {
			applyHyperlinkMoz();
		}
	}
	
	
	
	
	function applyHyperlinkIE() {
		if(!dialogArguments.oUtil.obj.checkFocus()){return;}//Focus stuff
		var oEditor=dialogArguments.oUtil.oEditor;
		var oSel=oEditor.document.selection.createRange();
		
		dialogArguments.oUtil.obj.saveForUndo();
		
		var sURL;
		sURL=inpType.value + inpURL.value;
		
		
		if(inpURL.value!=""){
			if (oSel.parentElement)
				{
				//if(btnInsert.style.display=="block")
				//	{
					if(oSel.text=="")//If no (text) selection, then build selection using the typed URL
						{
						var oSelTmp=oSel.duplicate();
						oSel.text=sURL;
						oSel.setEndPoint("StartToStart",oSelTmp);
						oSel.select();
						}
				//	}
				}
			
			oSel.execCommand("CreateLink",false,sURL);
	
			//get A element
			if (oSel.parentElement)	oEl=GetElement(oSel.parentElement(),"A");
			else oEl=GetElement(oSel.item(0),"A");
			if(oEl)
				{
				if(inpTarget.value=="" && inpTargetCustom.value=="") 
					oEl.removeAttribute("target",0);//target
				else 
					{

						oEl.target=inpTarget.value;
					}
				
			
				}
				
			dialogArguments.realTime(dialogArguments.oUtil.oName);
			dialogArguments.oUtil.obj.selectElement(0);
			}
		else
			{
			oSel.execCommand("unlink");//unlink
			
			dialogArguments.realTime(dialogArguments.oUtil.oName);
			dialogArguments.oUtil.activeElement=null;
			}	
		realTime();
	}
	
	
	function applyHyperlinkMoz()
    {
    //if(!window.opener.oUtil.obj.checkFocus()){return;}//Focus stuff
    
    var oEditor=window.opener.oUtil.oEditor;

    //var oSel=oEditor.document.selection.createRange();
    var oSel=oEditor.getSelection();
    var range = oSel.getRangeAt(0);
    window.opener.oUtil.obj.saveForUndo();
    
    
    var inpType = document.getElementById("inpType");   
    var inpURL = document.getElementById("inpURL");
    var inpTarget = document.getElementById("inpTarget");
    //var inpTitle = document.getElementById("inpTitle");
    
    var sURL;
   
    sURL=inpType.value + inpURL.value;
   
    
    if(inpURL.value != "") {
        var emptySel = false;
        //if(document.getElementById("btnInsert").style.display=="block" ||
         //   document.getElementById("btnInsert").style.display=="")
        //    {
            
            if(range.toString()=="") 
                { //If no (text) selection, then build selection using the typed URL
                if (range.startContainer.nodeType==Node.ELEMENT_NODE) 
                    {
                    if (range.startContainer.childNodes[range.startOffset].nodeType != Node.TEXT_NODE) 
                        { 
                        if (range.startContainer.childNodes[range.startOffset].nodeName=="BR") emptySel = true; else emptySel=false;  
                        } 
                        else 
                        { 
                        emptySel = true; 
                        }
                    } else {
                        emptySel = true;
                    }
                }
                
            if (emptySel) 
                {
                var node = oEditor.document.createTextNode(sURL);
                range.insertNode(node);
                oEditor.document.designMode = "on";
                
                range = oEditor.document.createRange();
                range.setStart(node, 0);
                range.setEnd(node, sURL.length);
                
                oSel = oEditor.getSelection();
                oSel.removeAllRanges();
                oSel.addRange(range);            
                }
                
           // }
        
        var isSelInMidText = (range.startContainer.nodeType==Node.TEXT_NODE) && (range.startOffset>0)
        
        oEditor.document.execCommand("CreateLink", false, sURL);
        
        oSel = oEditor.getSelection();
        range = oSel.getRangeAt(0);
        
        //get A element
        if (range.startContainer.nodeType == Node.TEXT_NODE) {
            var node = (emptySel || !isSelInMidText ? range.startContainer.parentNode : range.startContainer.nextSibling); //A node
            range = oEditor.document.createRange();
            range.selectNode(node);
            
            oSel = oEditor.getSelection();
            oSel.removeAllRanges();
            oSel.addRange(range);
            
        }
        
        var oEl = range.startContainer.childNodes[range.startOffset];
        if(oEl)
            {
            if(inpTarget.value=="") 
            	oEl.removeAttribute("target",0);//target
            else 
                {
                
                    oEl.target=inpTarget.value;
                        
            	//if(inpTitle.value=="") oEl.removeAttribute("title",0);//1.5.1
            	//else oEl.title=inpTitle.value;
            }
            }
            
        window.opener.realTime(window.opener.oUtil.obj);
        window.opener.oUtil.obj.selectElement(0);
        }
    else
        {
        oEditor.document.execCommand("unlink", false, null);//unlink
        window.opener.realTime(window.opener.oUtil.obj);
        window.opener.oUtil.activeElement=null;
        }   
   	 realTime();
   	 window.focus();
    }
	
	function realTimeIE() {
		if(!dialogArguments.oUtil.obj.checkFocus()){return;}//Focus stuff
		var oEditor=dialogArguments.oUtil.oEditor;
		var oSel=oEditor.document.selection.createRange();
		var sType=oEditor.document.selection.type;
		
		//If text or control is selected, Get A element if any
		if (oSel.parentElement)	oEl=GetElement(oSel.parentElement(),"A");
		else oEl=GetElement(oSel.item(0),"A");
	
		//Is there an A element ?
		if (oEl) {
				
			//~~~~~~~~~~~~~~~~~~~~~~~~
			sTmp=oEl.outerHTML;
			if(sTmp.indexOf("href")!=-1) //1.5.1
				{
				sTmp=sTmp.substring(sTmp.indexOf("href")+6);
				sTmp=sTmp.substring(0,sTmp.indexOf('"'));
				var arrTmp = sTmp.split("&amp;");
				if (arrTmp.length > 1) sTmp = arrTmp.join("&");		
				sURL=sTmp
				//sURL=oEl.href;
				}
			else
				{
				sURL=""
				}
	
			if(sType!="Control") {
				try	{			
					var oSelRange = oEditor.document.body.createTextRange()
					oSelRange.moveToElementText(oEl)
					oSel.setEndPoint("StartToStart",oSelRange);
					oSel.setEndPoint("EndToEnd",oSelRange);
					oSel.select();
				} catch(e){return;}
			}
			
			inpTarget.value="";
			//inpTargetCustom.value="";
			if(oEl.target=="_self" || oEl.target=="_blank")
				inpTarget.value=oEl.target;//inpTarget
			else
				inpTarget.value=  "_self";
			
			//inpTitle.value="";
			//if(oEl.title!=null) inpTitle.value=oEl.title;//inpTitle //1.5.1
	
	
			if(sURL.substr(0,7)=="http://") {
				inpType.value="http://";//inpType
				inpURL.value=sURL.substr(7);//idLinkURL
	
				
				}
			else if(sURL.substr(0,8)=="https://")
				{
				inpType.value="https://";
				inpURL.value=sURL.substr(8);
	
				
				}
			else if(sURL.substr(0,7)=="mailto:")
				{
				inpType.value="mailto:";
				inpURL.value=sURL.split(":")[1];
	
				
				}
			else if(sURL.substr(0,6)=="ftp://")
				{
				inpType.value="ftp://";
				inpURL.value=sURL.substr(6);
	
				
				}
			
			else if(sURL.substr(0,11).toLowerCase()=="javascript:")
				{
				inpType.value="javascript:";
				inpURL.value=sURL.split(":")[1];
				inpURL.value=sURL.substr(sURL.indexOf(":")+1);
	
				
			} else {
				inpType.value="";
	
				if(sURL.substring(0,1)=="#")
					{
					
					inpURL.value="";
					
					}
				else {
					
					inpURL.value=sURL;
					
					}
				}
			}
		else
			{
			
	
			//inpTarget.value="";
			//inpTargetCustom.value="";
			//inpTitle.value="";
			
			//inpType.value="";
			inpURL.value="";
			//inpBookmark.value="";
			
			
			}			
		
	} // End realtime IE
	
	function realTimeMoz() {
       
    var inpType = document.getElementById("inpType");  
    var inpURL = document.getElementById("inpURL");   
    var inpTarget = document.getElementById("inpTarget");
    //var inpTitle = document.getElementById("inpTitle");
    
   
    var oEditor=window.opener.oUtil.oEditor;   
    var oSel = oEditor.getSelection();
    var oEl = GetElement(window.opener.getSelectedElement(oSel),"A");//new
   
    if(!oEl) {
      
        inpTarget.value="";
       // inpTargetCustom.value="";
       // inpTitle.value="";
        
        inpType.value="";
        inpURL.value="";
       // inpBookmark.value="";
        
     
        return;
		}
		
    //Is there an A element ?
    if (oEl.nodeName == "A") {
        
        var range =oEditor.document.createRange();
        range.selectNode(oEl);
        oSel.removeAllRanges();
        oSel.addRange(range);
        
         

        var sURL = oEl.getAttribute("HREF");
        
        inpTarget.value="";
       // inpTargetCustom.value="";
        var trg = oEl.getAttribute("TARGET");
        //if(trg=="_self" || trg=="_blank" || trg=="_parent")
            inpTarget.value=trg;//inpTarget
        //else
         //   inpTargetCustom.value=trg;
        
    //    inpTitle.value="";
   //     if(oEl.getAttribute("TITLE")!=null) inpTitle.value=oEl.getAttribute("TITLE");//inpTitle //1.5.1

		if(sURL==null)sURL="";

        if(sURL.substr(0,7)=="http://")
            {
            inpType.value="http://";//inpType
            inpURL.value=sURL.substr(7);//idLinkURL

           
            }
        else if(sURL.substr(0,8)=="https://")
            {
            inpType.value="https://";
            inpURL.value=sURL.substr(8);
        
            }
        else if(sURL.substr(0,7)=="mailto:")
            {
            inpType.value="mailto:";
            inpURL.value=sURL.split(":")[1];
         
            }
        else if(sURL.substr(0,6)=="ftp://")
            {
            inpType.value="ftp://";
            inpURL.value=sURL.substr(6);
           
            }
        else if(sURL.substr(0,5)=="news:")
            {
            inpType.value="news:";
            inpURL.value=sURL.split(":")[1];
          
            }
        else if(sURL.substr(0,11).toLowerCase()=="javascript:")
            {
            inpType.value="javascript:";
            //inpURL.value=sURL.split(":")[1];
            inpURL.value=sURL.substr(sURL.indexOf(":")+1);
          
            }
        else
            {
            inpType.value="";
			inpURL.value=sURL;
            }
        }
    else
        {
    
        inpTarget.value="";
        inpTargetCustom.value="";
      //  inpTitle.value="";
        
        inpType.value="";
        inpURL.value="";
       // inpBookmark.value="";
        
        //inpBookmark.disabled=true;
       
        }           
    } // End realtime Moz
    
	// Event når der klikkes i htmleditor
	function realTime() {
		
		if (isIE) {
			realTimeIE()
		} else {
			realTimeMoz();
		}
		
		
	}
	
	function setCorrectTab() {
		
{/literal}
		{if ($use_tabs eq true) }
{literal}				
		document.getElementById("links").style.display = "none";
		document.getElementById("tab_links").className = "";
{/literal}		
		{if ($use_files eq true) }
{literal}		
		document.getElementById("filer").style.display = "none";
		document.getElementById("tab_filer").className = "";
{/literal}		
		{/if}
		{if ($use_pages eq true) }
{literal}		
		document.getElementById("interne_links").style.display = "none";
		document.getElementById("tab_interne_links").className = "";
{/literal}		
		{/if}
{literal}		
		if (checkAndSetFiles()) {
{/literal}
{if ($use_files eq true) }
{literal}			
			document.getElementById("filer").style.display = "block";
			document.getElementById("tab_filer").className = "active";
{/literal}
{/if}		
{literal}
		} else if (checkAndSetInternalPages()) {
{/literal}
{if ($use_pages eq true) }
{literal}
			document.getElementById("interne_links").style.display = "block";
			document.getElementById("tab_interne_links").className = "active";
{/literal}
{/if}	
{literal}		
		} else {			
			document.getElementById("links").style.display = "block";
			document.getElementById("tab_links").className = "active";
		}
{/literal}
		{/if}
{literal}		
	}
	
	function checkAndSetFiles() {
		var bFound = false;
		
		var inpURL = document.getElementById("inpURL");   
		var uploaded_files = document.getElementById("uploaded_files"); 
		var options = uploaded_files.options;
		
		if (inpURL.value == "") return(false);
		
		for (var i = 0; i < options.length; i++) {
			if (options[i].value == inpURL.value) {
				options[i].selected = true;
				bFound = true;
				break;
			}
		}
		return(bFound);
	}
	
	function checkAndSetInternalPages() {
		var bFound = false;
		
		var inpURL = document.getElementById("inpURL");   
		var internal_pages = document.getElementById("internal_pages"); 
		var options = internal_pages.options;
		
		if (inpURL.value == "") return(false);
		
		for (var i = 0; i < options.length; i++) {
			if (options[i].value == inpURL.value) {
				options[i].selected = true;
				bFound = true;
				break;
			}
		}
		return(bFound);
	}
	
	function onEnterTouch(evt){        				
		var charCode = (String(evt.which)!="undefined") ? evt.which : event.keyCode;
        if (charCode == 13) {
        	applyHyperlink();
        	self.close();
        }
	}
	
	function changeTab(changeToTab) {
		//var tabs = new Array("links", "filer", "interne_links");
		var tabs = new Array("links");
		{/literal}
		{if ($use_files eq true) }tabs[tabs.length] = "filer";{/if}
		{if ($use_pages eq true) }tabs[tabs.length] = "interne_links";{/if}
		{literal}
		
		/* Reset all tabs */
		for (var i = 0; i < tabs.length; i++) {
			var tabName = tabs[i];					
				if (tabName == changeToTab) {
					document.getElementById(tabName).style.display = "block";
					document.getElementById("tab_" + tabName).className = "active";
				} else {
					document.getElementById(tabName).style.display = "none";
					document.getElementById("tab_" + tabName).className = "";
				}			
		}
		
	}
</script>
{/literal}
</head>
{if ($use_tabs eq true) }
<body onload="bodyOnLoad()" id="frame_right" class="link_popup">
<div id="head">
	<!-- Start: contentmenu -->
<div class="tabsBox">
	<ul class="tabs">	
		{if ($use_files eq true) }<li class="" id="tab_filer"><span class="left"></span><a href="javascript:void(0);" onclick="changeTab('filer');return(false);" title="Filer">Filer</a><span class="right"></span></li>{/if}
		{if ($use_pages eq true) }<li class="" id="tab_interne_links"><span class="left"></span><a href="javascript:void(0);" onclick="changeTab('interne_links');return(false);" title="Sider">Sider</a><span class="right"></span></li>{/if}
		<li class="" id="tab_links"><span class="left"></span><a href="javascript:void(0);" onclick="changeTab('links');return(false);" title="Links">Links</a><span class="right"></span></li>	
	</ul>
</div>
	<!-- End: contentmenu -->
</div>
{else}
<body onload="bodyOnLoad()">
{/if}	

<div id="content">
{if ($use_tabs eq true) }
<div id="links" style="display:none;">
{else}
<div id="links" style="display:block;">
{/if}
	<table width=100%>
		<tr>
			<td width="50"><label for="inpURL">Url:</label></td>
			<td widht="50">
			 <select id="inpType" name="inpType" class="inpSel">
	                <option value=""></option>
	                <option value="http://">http://</option>
	                <option value="https://">https://</option>
	                <option value="mailto:">mailto:</option>
	                <option value="javascript:">javascript:</option>
	            </select>&nbsp;
				<input type="text" id="inpURL" name="inpURL" style="width:135px;" class="inpTxt" onkeypress="onEnterTouch(event); ">		
			</td>
		</tr>
		<tr>
			<td nowrap="nowrap"><label for="inpTarget">{cmstext value=choose_target}:</label></td>
			<td colspan="2">
			<select id="inpTarget" name="inpTarget" class="inpSel">
				<option value="_self" id="optLang" name="optLang">{cmstext value=choose_self}</option>
				<option value="_blank" id="optLang" name="optLang">{cmstext value=choose_blank}</option>
				
			</select></td>
			
		</tr>
	</table>
	
	<div class="actionButtons">
		<ul class="ul">
			<li class="li"><a href="#" onclick="self.close()" class="buttonType1"><span><span>{cmstext value=btncancel}</span></span></a></li>
			<li class="li"><a href="#" onclick="applyHyperlink(); self.close();" class="buttonType1"><span><span>{cmstext value=btnok}</span></span></a></li>
		</ul>
	</div>
	
</div>

<div id="filer" style="display:none;">
<form enctype="multipart/form-data" name="file" action="filepost.php" method="POST" target="filepost">
	<table border="0"  width=100%>
	<tr>
		<td width="50"><label for="linkupload">Upload fil:</label></td>
		<td widht="50">
		 <input type="file" size="40" name="linkupload" id="linkupload" style="width:278px;" class="inpTxt">
		</td>
	</tr>
	<tr>
		<td width="50"><label for="uploaded_files">Filliste:</label></td>
		<td widht="50">
		 <select name="uploaded_files" id="uploaded_files" style="width:150px" class="inpTxt" onchange="document.getElementById('inpURL').value = this[this.selectedIndex].value;">
		 	<option></option>
		 	{section name=files loop=$arr_uploadedfiles}
		 	<option value="{$arr_uploadedfiles[files].filepath}">{$arr_uploadedfiles[files].filename}</option>
		 	{/section}
		 </select>
		</td>
	</tr>
	<!--<tr>
		<td nowrap>&nbsp;<span id="txtLang" name="txtLang">Title</span>:</td>
		<td><INPUT type="text" ID="inpTitle" NAME="inpTitle" style="width:160px" class="inpTxt"></td>
	</tr>-->
	</table>
	
	<div class="actionButtons">
		<ul class="ul">
			<li class="li"><a href="#" onclick="self.close()" class="buttonType1"><span><span>{cmstext value=btncancel}</span></span></a></li>
			<li class="li"><a href="#" onclick="document.forms['file'].submit();" class="buttonType1"><span><span>{cmstext value=btnok}</span></span></a></li>
		</ul>
	</div>
	
<iframe width="0" height="0" name="filepost" src="blank.php" style="display: none;"></iframe>
</form>
</div>


<div id="interne_links" style="display:none;">
	<table border="0"  width=100%>
	<tr>
		<td width="50"><label for="internal_pages">Sider:</label></td>
		<td widht="50">
		 <select name="internal_pages" id="internal_pages" style="width:280px" class="inpTxt" onchange="document.getElementById('inpURL').value = this[this.selectedIndex].value;document.getElementById('inpType').value = '';">
		 	<option>{cmstext value=choosepage}</option>
		 	{$htmlpageoption}
		 </select>
		</td>
	</tr>
	<!--<tr>
		<td nowrap>&nbsp;<span id="txtLang" name="txtLang">Title</span>:</td>
		<td><INPUT type="text" ID="inpTitle" NAME="inpTitle" style="width:160px" class="inpTxt"></td>
	</tr>-->
	</table>
	
	<div class="actionButtons">
		<ul class="ul">
			<li class="li"><a href="#" onclick="self.close()" class="buttonType1"><span><span>{cmstext value=btncancel}</span></span></a></li>
			<li class="li"><a href="#" onclick="applyHyperlink(); self.close();" class="buttonType1"><span><span>{cmstext value=btnok}</span></span></a></li>
		</ul>
	</div>
		
</div>

</div>

</body>
</html>