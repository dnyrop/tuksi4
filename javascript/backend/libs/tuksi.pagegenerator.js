tuksi.pagegenerator = {
	arrUpload: new Hash(),
	toggleSelectedModules: function(objChk){
		$$('.itemHeader .checkbox').each(function(e){
			e.checked = objChk.checked;
		});
	},
	addTabDialog:function(){
		
		var tplAddTabDialog = $('addTabDialog').innerHTML.interpolate({
				formid:'_addtabdialog',
				id:'#{id}'
		});
		tuksi.window.popup({
			title:'Add new tab',
			content:tplAddTabDialog,
			placement:'center',
			options:{
				width:350,
				id:'addTab'
			}
		});
	},
	addTab:function(){
		var arrVal = $H({
			addTab:1,
			addTabName:$F('addTabName_addtabdialog'),
			addTabTemplate: $F('addTabTemplate_addtabdialog')
		});
		$('json').value = Object.toJSON(arrVal);
		doAction('SAVE');
	},
	deleteTabDialog:function(){
		
		tuksi.window.popup({
			title:'Delete tab',
			content:$('deleteTabDialog').innerHTML,
			placement:'center',
			options:{
				width:350,
				id:'deleteTab'
			}
		});
	},
	deleteTab:function(){
		var arrVal = $H({
			deleteTab:1
		});
		$('json').value = Object.toJSON(arrVal);
		doAction('SAVE');
	},
	addNodeDialog:function(){
		
		var tplAddTabDialog = $('addNodeDialog').innerHTML.interpolate({
				formid:'_addnodedialog',
				id:'#{id}'
		});
		
		tuksi.window.popup({
			title:'Add node',
			content:tplAddTabDialog,
			placement:'center',
			options:{
				width:350,
				id:'addNode'
			}
		});
	},
	addNode:function(){
		var arrVal = $H({
			addNode:1,
			addNodeName:$F('addNodeName_addnodedialog'),
			addNodeParent: $F('addNodeParent_addnodedialog'),
			addNodeTab: $F('addNodeTab_addnodedialog')
		});
		$('json').value = Object.toJSON(arrVal);
		doAction('ADD');
	},
	deleteNodeDialog:function(){
		
		tuksi.window.popup({
			title:'Delete node',
			content:$('deleteNodeDialog').innerHTML,
			placement:'center',
			options:{
				width:350,
				id:'deleteNode'
			}
		});
	},
	deleteNode:function(){
		doAction('DELETE');
	},
	doActionOnModule: function(moduleid,objSelect,modname){
		switch($F(objSelect)) {
			case 'delete': 
				this.deleteModuleDialog(moduleid,modname);
				break;
			case 'show': 
				$('activatemodule').value = moduleid;
				saveData();
				break;
			case 'hide': 
				$('deactivatemodule').value = moduleid;
				saveData();
				break;
			case 'setup': 
				document.location = $F('setupLink_'+moduleid);
				/*$('setupmodule').value = moduleid;
				saveData();*/
				break;
			case 'release': 
				$('releasemodule').value = moduleid;
				saveData();
				break;
			default:
				break;
		}
	},
	deleteModuleDialog: function(moduleid,modname){
		var strMessage = 'Delete';
		if(modname) {
			strMessage+= ' '+modname;
		}
		strMessage+= '?';
		tuksi.window.confirm(strMessage,{
														callback:this.deleteModule.bind(this,moduleid),
														callbackCancel:this.resetModuleAction.bind(this,moduleid)
		});
	},
	deleteModule: function(moduleid) {
		$('deletemodule').value = moduleid;
		saveData();
	},
	deleteSelectedModulesDialog: function(){
		var selChk = $$('.itemHeader .checkbox').find(function(e){
			if(e.checked) {
				return true;
			}
		});
		if(typeof(selChk) == 'object') {
			tuksi.window.confirm('Delete selected modules?',{callback:tuksi.pagegenerator.deleteSelectedModules});	
		} else {
			tuksi.window.alert('No modules selected');
		}
	},
	deleteSelectedModules: function(){
		var arrSelected = Array();
		$$('.itemHeader .checkbox').each(function(e){
			if(e.checked) {
				arrSelected.push($F(e));
			}
		});
		$('deletemodule').value = arrSelected.join(', ');
		saveData();
	},
	openSelectedModules: function(){
		$('openselected').value = 1;
		saveData();
	},
	closeSelectedModules: function(){
		$('closeselected').value = 1;
		saveData();
	},
	addModuleDialog:function(){
		tuksi.window.popup({
			title:'Add new element',
			content:$('newModuleContent').innerHTML,
			placement:'center',
			options:{
				width:350,
				id:'addModule'
			}
		});
	},
	updateNewModule:function(objSelect){
		$('newmodule_tmp').value = $F(objSelect);
	},
	updateNewModulePlacement:function(objSelect){
		$('newmoduleplacement_tmp').value = $F(objSelect);
	},
	addModule:function(objSelect){
		if($F('newmodule_tmp') > 0) {
			$('newmodule').value = $F('newmodule_tmp');
			$('newmoduleplacement').value = $F('newmoduleplacement_tmp');
			doAction('SAVE');
		} else {
			tuksi.window.alert('Please choose a module before saving');
		}
		return false;
	},
	resetModuleAction:function(moduleid) {
		$('moduleActionSelect_' + moduleid).selectedIndex = 0;
	},
	arrangeModulesDialog:function(treeid,tabid,areaid,title){

		url = '/services/ajax/pageDialogs.php?action=arrangemodulesdialog&treeid=' + treeid + '&tabid=' + tabid + '&areaid=' + areaid + '&id=_arrangemodulesdialog';
		
		tuksi.window.popup({
			title:title,
			ajax: true,
			url: url,
			placement:'center',
			options:{
				width:350,
				id:'arrangeModules'
			}
		});
	},
	saveArrangeModules:function(){
		Sortable.create('modulearrange');
		var t = Sortable.serialize('modulearrange');
		
		$('json').value = Object.toJSON(t);
		$('savemodulearrange').value = 1;
		doAction('SAVE');
	},
	copyPageDialog:function(treeid,title){

		url = '/services/ajax/pageDialogs.php?action=copypagedialog&treeid='+treeid + '&id=_copydialog';
		
		tuksi.window.popup({
			title:title,
			ajax: true,
			url: url,
			placement:'center',
			options:{
				width:350,
				id:'copyPage'
			}
		});
	},
	copyPage: function() {
		var arrVal = $H({
			copyPagePlacement:$F('copyPagePlacement_copydialog'),
			copyPageTreeid: $F('copyPageTreeid_copydialog'),
			copyPageName : $F('copyPageName_copydialog'),
			copyPageSubpages : $F('copyPageSubpages_copydialog')
		});
		$('json').value = Object.toJSON(arrVal);
		doAction('COPY_PAGE');
	},
	releasePageDialog:function(treeid,title){

		url = '/services/ajax/pageDialogs.php?action=releasepagedialog&treeid='+treeid + '&id=_releasedialog';
		
		tuksi.window.popup({
			title:title,
			ajax: true,
			url: url,
			placement:'center',
			options:{
				width:350,
				id:'releasePage'
			}
		});
	},
	releasePage: function() {
		var arrVal = $H({
			releasePageSubpages: $F('releasePageSubpages_releasedialog')
		});
		$('json').value = Object.toJSON(arrVal);
		doAction('RELEASE_PAGE');
	},
	addPageDialog:function(treeid,title){

		this.addPageParentStatus = false;
		
		url = '/services/ajax/pageDialogs.php?action=addpagedialog&treeid='+treeid + '&id=_adddialog';
		
		tuksi.window.popup({
			title:title,
			ajax: true,
			url: url,
			placement:'center',
			options:{
				width:350,
				id:'addPage'
			}
		});
	},
	addPage: function() {
		
		if(this.addPageParentStatus) {
		
			var arrVal = $H({
				addPageName:$F('addPageName_adddialog'),
				addPagePlacement:$F('addPagePlacement_adddialog'),
				addPageTreeid: $F('addPageTreeid_adddialog')
			});
			$('json').value = Object.toJSON(arrVal);
			doAction('ADD_PAGE');
			
		} 
	},
	checkParent: function(placementInput,parentInput,statusDiv,submitBtn){
		
		$(submitBtn).addClassName('disabledButton');
		
		var placement = $F(placementInput);
		var parent = $F(parentInput);
		
		if(placement > 0 && parent > 0){
			
			url = '/services/ajax/pageDialogs.php?action=checkparent&treeid='+parent+'&placement=' + placement;
			
			new Ajax.Request(url,{
				method:'get',
				onSuccess:this.checkParentUpdateStatus.bind(this,statusDiv,submitBtn)
			});
		} 
	},
	checkParentUpdateStatus: function(statusDiv,submitBtn,t){
		
		var status = t.responseText;
		if(status == 1){
			$(statusDiv).hide();
			this.addPageParentStatus = true;
			$(submitBtn).removeClassName('disabledButton');
		} else {
			$(statusDiv).show();
			this.addPageParentStatus = false;
			$(submitBtn).addClassName('disabledButton');
		}
		
		
	},
	setParentStatus:function(status){
		this.addPageParentStatus = status;
	},
	movePageDialog: function(treeid,title) {
		
		url = '/services/ajax/pageDialogs.php?action=movepagedialog&treeid='+treeid + '&id=_movedialog';
		
		tuksi.window.popup({
			title:title,
			ajax: true,
			url: url,
			placement:'center',
			options:{
				width:350,
				id:'movePage'
			}
		});
	},
	movePage: function() {
		
		if(this.addPageParentStatus) {
		
			var arrVal = $H({
				movePagePlacement:$F('movePagePlacement_movedialog'),
				movePageTreeid: $F('movePageTreeid_movedialog')
			});
			$('json').value = Object.toJSON(arrVal);
			doAction('MOVE_PAGE');
		}
	},
	moveTabDialog: function(treeid,tabid,title) {
		
		url = '/services/ajax/pageDialogs.php?action=movetabdialog&treeid='+treeid + '&tabid='+tabid+'&id=_movetabdialog';
		
		tuksi.window.popup({
			title:title,
			ajax: true,
			url: url,
			placement:'center',
			options:{
				width:350,
				id:'moveTab'
			}
		});
	},
	moveTab: function() {
		var arrVal = $H({
			moveTabId:$F('moveTabId_movetabdialog'),
			moveTabPlacement:$F('moveTabPlacement_movetabdialog'),
			moveTabTreeid: $F('moveTabTreeid_movetabdialog')
		});
		$('json').value = Object.toJSON(arrVal);
		doAction('MOVE_TAB');
	},
	previewPage:function(url){
		if(!window.open(url)) {
			$('contentNotice').update('<div class="contentNotice">Der ser ud til at previewet blev forhindret. Tjek evt. om der er installeret en popup-blokker. Tryk <a href="'+url+'" target="_blank">her</a> for at se previewet.</div>');
			$('contentNotice').show();
		}
	},
	deletePageDialog:function(text){
		tuksi.window.confirm(text,{callback:this.deletePage.bind(this)});	
	},
	deletePage:function(){
		doAction('DELETE_PAGE');
	},
	arrangePages:function(){
		tuksi.window.alert('mangler');
	/*	var queryString = {	rootid:$F('SITEROOTID'),
									treeid:$F('TREEID'),
									areaid:$F('AREAID')};
		tuksi_divPopup.show({	fromajax:true,
													ajaxelement:'arrangemodules_popup',
													placement:'center',
													querystring:queryString,
													callback:pg.setArrangeSort});*/
	},
	addUpload: function(objUpload) {
		this.arrUpload.set(objUpload.id,objUpload);
	},
	uploadFinished: function(objUpload) {
		this.arrUpload.unset(objUpload.id);
	},
	pendingUploads: function(){
		if(this.arrUpload.keys().length > 0) {
			return true;
		} else {
			return false;
		}
	},
	uploadFiles:function(){
		if(this.arrUpload.keys().length > 0) {
			this.makeUploadQueueWindow();
			this.totalUploads = this.arrUpload.keys().length;
			this.arrUpload.each(function(pair){
				pair.value.initUpload();
			});
			this.queueChecker = setInterval(this.checkUploadQueue.bind(this),250);
		} else {
			document.tuksiForm.submit();
		}
	},
	checkUploadQueue:function(){
		if(this.arrUpload.keys().length == 0) {
			clearInterval(this.queueChecker);
			document.tuksiForm.submit();
		} else {
			var uploaded = this.totalUploads - this.arrUpload.keys().length;
			$('uploadQueueStatus').update('Uploaded ' + uploaded + ' of '+ this.totalUploads);
		}
	},
	makeUploadQueueWindow:function(){
		
		var strUploadItemTpl = '<table class="moduleElementRow"><tbody><tr><td>';
		strUploadItemTpl+= '<div class="graphLine" style="width:0px;background:#0DB8E2;height:5px;" id="uploadProgress#{id}"></div>';
		strUploadItemTpl+= '</td></tr><tr><td>';
		strUploadItemTpl+= '<span id="uploadProgressTextPopup#{id}"></span>';
		strUploadItemTpl+= '</td></tr></tbody></table>';
		
		var mainTpl = "";
		
		var itemTpl = new Template(strUploadItemTpl);
		
		this.arrUpload.each(function(pair) {
			mainTpl+= itemTpl.evaluate({id:pair.key});
		});
		mainTpl+= '<table class="moduleElementRow"><tbody><tr><td><span id="uploadQueueStatus"></span></td></tr></tbody></table>';
		tuksi.window.popup({title:'Processing upload queue',
												content:mainTpl,
												id:'uploadQueue',
												options:{width:325,close:false}
		});
	},
	updateSortorder:function(){
		tuksi.pagegenerator.saveSortable = true;
	},
	saveSortable: false
}
