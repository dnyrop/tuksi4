<input type="hidden" value="" id="layoutid" name="layoutid">
<script type="text/javascript">
{literal}
function createNewsletter(layoutid){
	$('layoutid').value = layoutid;
	doAction('SAVE');
}
{/literal}
</script>

{foreach from=$layouts item=layout}
<h3>{$layout.name}</h3>
{$layout.description}
<br />
<a href="#" onclick="createNewsletter('{$layout.id}'); return false; return false;" class="buttonType3 itemAdd"><span><span>{cmstext value="createnewsletter"}</span></span></a>
<br /><br /><br />
{/foreach}
