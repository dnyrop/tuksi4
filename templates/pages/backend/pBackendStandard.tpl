{if $showcontent}
<div class="mContentDefault">
{if $tabs}
	<div class="tabNavigation">
		<ul>
		{foreach from=$tabs item=tab}	
			<li {if $tab.isactive}class="active"{/if}><a href="#" onclick="changeTab('{$tab.id}');">{$tab.name}</a></li>
		{/foreach}
		</ul>
	</div>
{/if}	
	<div class="moduleTop"><div class="moduleTopInner"></div></div>
		<div class="moduleMiddle">
			<div class="moduleMiddleInner">
			{$area.content.content}
			<br class="clr">
			</div>
	</div>
	<div class="moduleBottom">
		<div class="moduleBottomInner">
		</div>
	</div>
</div>
<div class="clr"></div>
{/if}