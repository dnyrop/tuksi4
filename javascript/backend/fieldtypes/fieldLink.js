var tuksi = tuksi || {}
tuksi.fieldLink = {
	onchange: function(id) {
		var select = document.getElementById(id),
				options = select.getElementsByTagName('option'),
				count = options.length,
				selected = null,
				i = null;
				
		for(i = 0; i < count; i++) {
			if(options[i].selected) {
				selected = options[i].value;
				break;
			}
		}
		
		if(selected) {
			tuksi.fieldLink.show(id.replace('_type',''), selected);
		}
	},
	show: function(id, show) {
		var div = document.getElementById('fieldLink_'+ id),
				tables = div.getElementsByTagName('table'),
				count = tables.length,
				i = null;
		
		for(i = 0; i < count; i++) {
			tables[i].style.display = (tables[i].id === id +'_'+ show) ? 'block' : 'none';
		}
		
	}
}