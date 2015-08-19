<div class="m18">
	<h5>{$module.headline} </h5><br/>
	<ul class="baseUl">
	        
		{foreach from=$arrList key=k item=p}
			<li class="baseLi">
			    <a href="#" class="notActive" onclick="listToggle('{$p.id}{$k}',this); return false;">{$p.date|date_format} - {$p.name}</a>
			    <div class="item" id="item{$p.id}{$k}" style="display:none;">
		            <div class="itemInner">
		                    {$p.html}
		            </div>
			    </div>
			</li>
		{/foreach}      
	</ul>
	<br class="clr">
</div>
<script>
{literal}
function listToggle(id,obj){
	var doDisplay = document.getElementById('item'+id).style.display == 'none' ? 'block' : 'none';
	 document.getElementById('item'+id).style.display = doDisplay;
}
{/literal}
</script>
