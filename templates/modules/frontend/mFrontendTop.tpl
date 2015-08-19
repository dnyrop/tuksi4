{if $module.image}
<div class="mTopImage">
	<img src="{$path.upload}/{$module.image}" alt="" title="">
	<div class="overlayTop"></div>
	<div class="overlayBottom"></div>
</div>
{/if}

{if $module.headline}
<div class="mHeadline">
{if $module.value1}{* flerfarvet overskrift *}
	<h1 class="firstWord">{$module.headline}</h1>
	<h1>{$module.value1}</h1>
{else}
	<h1>{$module.headline}</h1>
{/if}
</div><!--//End mHeadline-->
{/if}
{if $module.value2}
<div class="mIntroText">
	<strong>
		{$module.value2}
	</strong>
</div>
{/if}