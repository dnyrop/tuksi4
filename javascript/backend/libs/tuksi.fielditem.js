function showArrangewindow(title,relationid,type,tablename){
	
	url = '/services/ajax/fielditemDialogs.php?action=arrangedialog&relationid='+relationid + '&type=' + type + '&tablename=' + tablename;
		
	tuksi.window.popup({
		title:title,
		ajax: true,
		url: url,
		placement:'center',
		options:{
			width:350,
			id:'arrangeItems'
		}
	});
}

function saveArrangeItems(){

	var serItems = Sortable.serialize('itemarrange',{ name: 'item' });
	
	var arrVal = $H({
			saveItemSeq:1,
			items:serItems
		});

		$('json').value = Object.toJSON(arrVal);
		doAction('SAVE');
}
