
<div class="mainContent">
{if $page.treeid != 238}
	<div class="mBreadcrumbs">
		<ul>
			<li><a href="/" title="Gå til: Forsiden">Forside</a> /</li>
{foreach name=sitemap item=sitemap from=$sitemap name=sitemap}
					
{if $smarty.foreach.sitemap.last}
			<li><span title="Du er her">{$sitemap.name}</span></li>
{else}
			<li><a href="{$sitemap.url}" title="Gå til siden: {$sitemap.name}">{$sitemap.name}</a> /</li>
{/if}
			
{/foreach}
		</ul>
	</div>
{/if}
	<div class="mainInnerContent">
			
{$area.content.content}
				
	</div>
</div>	