<div class="mBoxItem mBoxItemFrontpageTop">
	<div class="boxItemHeader">
		<h6>{cmstext value="headline"}</h6>
	</div>
	
	<ul>
		<li><a href="http://{$conf.site.url_prodsite}" target="_blank">{cmstext value=productionsite}</a></li>
		<li><a href="http://{$conf.site.url_preview}" target="_blank">{cmstext value=devsite}</a></li>
		{if $sitelinks.url_stats}<li><a href="{$sitelinks.url_stats}" target="_blank">{cmstext value=statsite}</a></li>{/if}
	</ul>

	<div class="boxItemFooter"></div>
</div><!--//End mBoxItemFrontpageTop-->
