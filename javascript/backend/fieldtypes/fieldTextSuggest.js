function editTextVal(id,lang,obj){
	if($('textsuggestnew_'+lang+'_'+id)) {
		//ajaxsave
		var newVal = $F('textsuggestnew_'+lang+'_'+id);
		url = "/services/ajax/updatetextval.php?id="+id+"&lang="+lang+"&value="+newVal;
		new Ajax.Request(url,{
			method:'get',
			onSuccess:updateTextVal.bind(1,id,lang,obj,newVal)
		});
	} else {
		value = $F('textsuggestvalue_'+lang+'_'+id);
		strInput = "<input class='text' style='width:150px;' type='text' value='' id='textsuggestnew_"+lang+"_"+id+"' />";
		$('textsuggest_'+lang+'_'+id).innerHTML = strInput;
		$('textsuggestnew_'+lang+'_'+id).value = value;
		obj.innerHTML = 'save';
	}

}

function updateTextVal(id,lang,obj,newVal,r){
	if(r.responseText == 1) {
		$('textsuggestvalue_'+lang+'_'+id).value = newVal;
		$('textsuggest_'+lang+'_'+id).innerHTML = $F('textsuggestnew_'+lang+'_'+id);
		obj.innerHTML = 'edit';
	} else {
		$('textsuggest_'+lang+'_'+id).innerHTML = "error";
		obj.innerHTML = 'edit';
	}
}