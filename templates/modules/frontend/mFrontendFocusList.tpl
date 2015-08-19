<div class="mSubRightList">
	<div class="mInner">
{if $module.headline}		
		<h1>{$module.headline}</h1>
{/if}	
		<ul>
{foreach from=$module.content item=i}			
			<li>{$i.name}</li>
{/foreach}
		</ul>
		<br class="clr">
	</div>
</div><!--//End mSubRightLinkList-->