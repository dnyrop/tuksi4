<div class="mHeadline">
	<h1>{$module.headline}</h1>
</div><!--//End mHeadline-->
			
<div class="mFrontReferences">
	<ul>
{foreach from=$module.content item=item}		
		<li><a href="{$item.link.url}" target="{$item.link.target}"><img src="{$path.upload}/{$item.image}" alt="{$item.name}" title="{$item.description}"></a></li>
{/foreach}
		<li>
			<div class="link">
				<a href="{$module.link.url}">{$module.value1}</a>
			</div>
		</li>
	</ul>
</div><!--//End mFrontReferences-->