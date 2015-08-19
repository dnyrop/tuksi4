<div class="mCtrlModules">
	<ul class="items">
	{foreach from=$panels item=panel}
	<li class="item">
		<div class="icon iconType{$panel.image}"><!--Png24--></div>
			<ul>
				<li><a href="{$panel.url}"><strong>{$panel.name}</strong></a></li>
				{foreach from=$panel.links item=link}
				<li><a href="{$link.url}">{$link.name}</a></li>
				{/foreach}
			</ul>
		</li>
	{/foreach}
	</ul>
</div>