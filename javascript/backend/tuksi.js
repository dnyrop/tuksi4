if(!console) {
	var console = {};
	console.log = function(){};
}

var tuksi = {};
tuksi = Base.extend({
	constructor:function(options){
		if(options !== undefined) {
			if(options.debug) {
				tuksi.util.addLoadEvent(this.loadDebug.bind(this));
			} else {
				tuksi.util.addLoadEvent(this.loadEmptyDebug);
			}
			
      // document.observe('dom:loaded',tuksi.util.setPageHeight);
      document.observe('dom:loaded', tuksi.util.setContentSize);
      Event.observe(window, 'resize', tuksi.util.setContentSize);
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

//need to be moved!

// Gerenal saving function
function saveData() {
	
	//$('saving_progress').setStyle({visibility:'visible'});
	//tuksi.window.showOverlay();
	
	if (arguments[0]) {
		document.tuksiForm.action = '#' + arguments[0];
	}
	var containerArray = ['moduleContainer'];
	for (var i = 0; i<containerArray.length; i++) {
		if(document.getElementById(containerArray[i])) {
			if(tuksi.pagegenerator.saveSortable){
				Sortable.create(containerArray[i],{
					tag:'div'
				});
			
				$('sortorder').value =  Sortable.serialize(containerArray[i]);
			}
		}
	}
	
	if(document.tuksiForm.userAction.value == '')
		document.tuksiForm.userAction.value = "SAVE";

	if(tuksi.pagegenerator.pendingUploads()) {
		tuksi.pagegenerator.uploadFiles()
	} else {
		if(document.tuksiForm.onsubmit) {
			document.tuksiForm.onsubmit();
		}
		document.tuksiForm.submit();
	}
}

function openModule(treeid, tabid, areaid, moduleid) {
  var module = $('module_'+ moduleid);
  if(module) {
    var content = $('module_content_'+ moduleid);
    if(content) {
      if(content.visible()) {
        $('module_content_'+ moduleid).style.display = 'none';
        $('opento').value = moduleid;
        $('module_isopen_'+ moduleid).value = '';
      } else {
        $('module_content_'+ moduleid).style.display = 'block';
        $('module_isopen_'+ moduleid).value = moduleid;
      }
    } else { 
      new Ajax.Request('/services/ajax/backend/loadModule.php?treeid='+ treeid +'&areaid='+ areaid +'&tabid='+ tabid +'&moduleid='+ moduleid +'', {
        method: 'get',
        onSuccess: function(transport) {
          if(transport.responseText) {
						var data = new Element('div');
						
						data.update(transport.responseText);
						$(module).insert(data);
				  } else {
            saveData();
          }
        }
      });
      $('opento').value = moduleid;
      $('module_isopen_'+ moduleid).value = moduleid;
      $('module_isopen_'+ moduleid +'_old').value = 1;
    }
    return false;
  }
  saveData();
}

function closeMenuNode(currentId,closeId){
	url = "/services/ajax/menuHandler.php?treeid="+currentId+"&close="+closeId;
	new Ajax.Request(url,{
		method:'get',
		onSuccess:updateMenu
	});
}

function openMenuNode(currentId,openId){
	url = "/services/ajax/menuHandler.php?treeid="+currentId+"&open="+openId;
	new Ajax.Request(url,{
		method:'get',
		onSuccess:updateMenu
	});
}

function updateMenu(r){
	$('tree').innerHTML = r.responseText;
}

function changeTab(id) {
	document.tuksiForm.areaid.value = id;
	doAction('update');
}

function editRowB(treeid,tabid,moduleid,rowid){
	window.location = "?treeid="+treeid+"&tabid="+tabid+"&moduleid="+moduleid+"&showrowid="+rowid;
}

function deleteRowDialog(moduleid,rowid){
	
	$("deleteRow_"+moduleid).value = rowid;
	
	tuksi.window.popup({
		title:'Delete Row',
		content:$('deleteRowDialog').innerHTML,
		placement:'center',
		options:{
			width:350,
			id:'rowTab'
		}
	});
}

function deleteRow(){
	doAction('DELETE');
}

function doDelete(){
	doAction('DELETE');
}

function doAction(action) {
	document.tuksiForm.userAction.value = action;
	saveData();	
}

function setValue(id,value){
	document.getElementById(id).value = value;
}

function reloadPage(){
	doAction('RELOAD');
}

function toggleDebug(){
	new Ajax.Request('/services/toggleDebug.php',
										{method:'get',
										onSuccess:reloadPage}
	);
}

document.observe('dom:loaded',function(){
	if($('saving_progress')) {
		$('saving_progress').setStyle({visibility:'hidden'});
	}
});



function hide_elms(obj) {
	if (!$(obj))
		return;

	var l = $(obj).up().up().next();
	
	if(obj.hasClassName("show")) {
		show_elms(obj);
		
	}
	else {
		while(l.next()) {
			
			l.hide();
			//Effect.SlideUp(l, {duration: 0.5});
			l = l.next();
			
			if(l.hasClassName("mHeader")) {
				obj.addClassName("show");
			var s = obj.down();

			s['src'] = "../../themes/default/images/icons/tuksi_plus.gif";		
				break;
			}
		}

		var s = obj.down();
	
		s['src'] = "../../themes/default/images/icons/tuksi_plus.gif";	
		obj.addClassName("show");
	}

	
}
function show_elms(obj) {
	var l = $(obj).up().up().next();
	
	while(l.next()) {
		//Effect.SlideDown(l, {duration: 0.5});
		l.show();
		
		l = l.next();
		if(l.hasClassName("mHeader")) {
			obj.removeClassName("show");
			var s = obj.down();

			s['src'] = "../../themes/default/images/icons/tuksi_minus.gif";	
			break;
		}
	}
	
	var s = obj.down();
	
	s['src'] = "../../themes/default/images/icons/tuksi_minus.gif";	
	obj.removeClassName("show");

	
}
