<div class="mTuksiList">
	<ul>
{foreach from=$modules item=group name=group}	
		<li>
			<img title="" alt="Icon" src="/images/graphics/gx_blank.gif" class="icon type{$group.icon}"/>
			<h3>{$group.name}</h3>
			<ul>
{foreach from=$group.modules item=mdl name=mdl}			
				<li onclick="document.location.href = this.getElementsByTagName('a')[0].href;">
					<h4><a href="/{$mdl.link}">{$mdl.name}</a></h4>
					<p>
						{$mdl.teaser} 
						<span> </span>
					</p>
				</li>
{/foreach}
			</ul>
		</li>
{/foreach}
	</ul>
</div>
