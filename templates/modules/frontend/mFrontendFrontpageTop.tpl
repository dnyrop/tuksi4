<div class="mFrontpageImage">
	<img src="/uploads/{$module.image}" alt="Forside billede" title="">
	<div class="textBox{if $module.value2 == 2} right{/if}">
		<div class="boxBg"></div>
		<div class="textLine">
			{$module.content} <a href="{$module.link.url}">{$module.value1}</a>
		</div>
	</div>
	<div class="overlay"></div>
</div><!--//End mFrontpageImage-->