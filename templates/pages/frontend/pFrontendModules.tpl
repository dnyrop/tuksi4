<div class="pageType threeColumnPage">
	<div class="pageInner">
		<div class="pageColumn1">
			<div class="subMenu">
{if $sideMenu}				
				<ul>
					{foreach from=$sideMenu item=menu name=menu}
						<li>
							<a href="/{$menu.pg_urlpart_full}" class="{$menu.pg_icontype}">{$menu.menuname}</a>
							{if $menu.haveMenuSubNodes && $menu.selected}
								<ul>
								{foreach from=$menu.nodes item=submenu name=submenu}
									<li><a href="/{$submenu.pg_urlpart_full}">{$submenu.menuname}</a></li>
								{/foreach}
								</ul>
							{/if}
						</li>
					{/foreach}
				</ul>
{/if}
				<div class="bottom"></div>
			</div><!--//End subMenu-->
					
		</div><!--//End pageColumn1-->
					
		<div class="pageColumn2">
						
			{$area.content.content}
						
		</div><!--//End pageColumn2-->
					
		<br class="clr">
					
		<div class="addressFooter">
			<address>
				<strong>Tuksi</strong>
				<span class="line1">Bernhard Bangs Allé 25, 1.sal</span>
				<span class="line2">2000 Frederiksberg</span>
				<span class="line3">Tlf.: 38 16 04 20</span>
				<span class="line4">E-mail: <a href="mailto:info@tuksi.com">info@tuksi.com</a></span>
			</address>
		</div><!--//End addressFooter-->
	</div><!--//End pageInner-->
</div><!--//End pageType-->