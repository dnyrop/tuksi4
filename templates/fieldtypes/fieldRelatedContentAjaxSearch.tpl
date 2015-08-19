{literal}
<script type="text/javascript">
	function doAjaxSearch(fieldid, htmltagname) {
		var url = '/services/ajax/backend/fieldRelatedContentAjaxSearch.php';
		new Ajax.Request(url, {	
			method: "get",
			parameters: { 
				action: "search",
				query: escape($F(htmltagname + '_query')),  
				fieldid: fieldid,
				htmltagname: htmltagname
			}, 
			onComplete: function(r) {
				if (r.status == 200) {
					$(htmltagname + '_searchresults').innerHTML = r.responseText;
					$(htmltagname + '_searchresults').show();
				}
			}
		});
		return false;
 	}
 	
 	function showText(eId) {
 		if ($(eId + '_text').getStyle('display') == "none") {
 			$(eId + '_text').show();
 		} else {
 			$(eId + '_text').hide();
 		}
 	}
 	
 	function removeRelation(eId, fieldId, htmltagname) {
 		var url = '/services/ajax/backend/fieldRelatedContentAjaxSearch.php';
		new Ajax.Request(url, {	
			method: "get",
			parameters: { 
				action: "removeRelation",
				elementId: eId,
				fieldid: fieldId,
				htmltagname: htmltagname,
				value: $F(htmltagname + '_relatedLinks')
			},
			onComplete: function(r) {
				if (r.status == 200) {
					var json = r.responseText.evalJSON();
						
					$(htmltagname + '_relations').innerHTML = json.data;
					$(htmltagname + '_relatedLinks').value = json.value;
					$(htmltagname + '_relationsView').show();
				}
			}
		});
	}
	
	function moveRelation (eId, fieldId, htmltagname, direction) {
 		var url = '/services/ajax/backend/fieldRelatedContentAjaxSearch.php';
		new Ajax.Request(url, {	
			method: "get",
			parameters: { 
				action: "moveRelation",
				elementId: eId,
				fieldid: fieldId,
				htmltagname: htmltagname,
				direction: direction,
				value: $F(htmltagname + '_relatedLinks')
			},
			onComplete: function(r) {
				if (r.status == 200) {
					var json = r.responseText.evalJSON();
					
					$(htmltagname + '_relations').innerHTML = json.data;
					$(htmltagname + '_relatedLinks').value = json.value;
					$(htmltagname + '_relationsView').show();
				}
			}
		});
	}
	
 	function addRelation (eId, fieldid, htmltagname, isactive) {
 		var url = '/services/ajax/backend/fieldRelatedContentAjaxSearch.php';
		new Ajax.Request(url, {	
			method: "get",
			parameters: { 
				action: "addRelation",
				elementId: eId,
				fieldid: fieldid,
				htmltagname: htmltagname,
				active: isactive,
				value: $F(htmltagname + '_relatedLinks')
			},
			onComplete: function(r) {
				if (r.status == 200) {
					var json = r.responseText.evalJSON();
						
					$(htmltagname + '_relations').innerHTML = json.data;
					$(htmltagname + '_relatedLinks').value = json.value;
					$(htmltagname + '_relationsView').show();
				}
			}	
		});
 	}

	function addSelectBoxElements(fieldid, htmltagname) {
		var selectBox = document.getElementById(htmltagname + '_selectbox');

		var str = "";
		for (var i = 0; i < selectBox.options.length; i++) {
			if (selectBox.options[ i ].selected) {
				str += selectBox.options[ i ].value + ",";
			}
		}
 		addRelation(str, fieldid, htmltagname, '0');	
	}
</script>
{/literal}
{if $mode == 'search'}
<div style="margin-bottom: 10px;">
	<div style="float: left;">		
		<input type="text" name="query" id="{$htmltagname}_query" value="" class="text" onkeypress="keyCode=(event.which)?event.which:event.keyCode;if(keyCode==13)doAjaxSearch({$fieldid}, '{$htmltagname}');">
		<img src="/images/ajax-loader.gif" style="display: none;" id="{$htmltagname}_relations_loading"> 
	</div>
	<div style="float: left; margin-left: 10px; margin-top: -2px;">
		<a href="#" class="buttonType3" onclick="return doAjaxSearch({$fieldid}, '{$htmltagname}');"><span style="margin-right: 5px;"><span>{cmstext value="search"}</span></span></a>
		<a href="#" class="buttonType3 itemDelete" onclick="$('{$htmltagname}_searchresults').hide();"><span><span>{cmstext value="close_search"}</span></span></a>
	</div>
</div>
<div style="border: #e6e6e6 1px solid; padding: 10px; height: 200px; width:95%; display: none; overflow: auto;  margin-top: 10px;" id="{$htmltagname}_searchresults">
</div>
{/if}

{if $mode == 'selectbox'}
<a href='#' onclick="addSelectBoxElements({$fieldid}, '{$htmltagname}')" class='buttonType3 itemAdd'><span style="margin-bottom: 10px;"><span>{cmstext value="add"}</span></span></a>
<select name="{$htmltagname}_selectbox" id="{$htmltagname}_selectbox" style="width: auto; margin-bottom: 10px; margin-left: 10px; height: 125px;" multiple="multiple">
	{foreach name=clip item=clip from=$data}
		<option value="{$clip.id}">{$clip.name}</option>
	{/foreach}
	</select>
	
{/if}
<br></br>
<div style="width: 100%;" id="{$htmltagname}_relationsView">
	<input type="hidden" name="{$htmltagname}_relatedLinks" id="{$htmltagname}_relatedLinks" value="{$list_values}">
	
	<div style="border-bottom: #e6e6e6 1px solid;"><strong>{cmstext value="related_elements"}</strong></div>
		<div style="padding: 10px; width: 100%;" id="{$htmltagname}_relations">{$datalist}</div>
	</div>
</div>
