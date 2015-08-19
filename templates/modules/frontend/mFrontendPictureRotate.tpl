<script>
var myImages = [	
{foreach from=$module.content item=picture name=pictures}
{ldelim}name:'{$picture.value2}',src:'/uploads/{$picture.value1}'{rdelim}
{if !$smarty.foreach.pictures.last},{/if}
{/foreach}
];
var slideshow = new tuksi.slideshow(myImages,'slideDiv_{$module.id}',{ldelim}transition:'fade'{rdelim});
</script>
<table width="439" border="0" cellspacing="0" cellpadding="0" class="tilewhite">
	<tr>
		<td colspan="3" height="10"></td>
	</tr>
	<tr>
		<td width="16"></td>
		<td colspan="2">
			<div style="width:400px;height:290px;" id="slideDiv_{$module.id}"></div>
		</td>
	</tr>
	<tr>
		<td colspan="3" height="15"></td>
	</tr>
</table>
<table width="439" border="0" cellspacing="0" cellpadding="0" class="tilewhite">
	<tr>
		<td colspan="3" height="10"></td>
	</tr>
	<tr>
		<td width="16"></td>
		<td colspan="2">
			<a href="javascript:slideshow.prev();">prev</a>
			<a href="javascript:slideshow.start();">play</a>
			<a href="javascript:slideshow.stop();">stop</a>
			<a href="javascript:slideshow.next();">next</a>
		</td>
	</tr>
	<tr>
		<td colspan="3" height="15"></td>
	</tr>
</table>