<div class="m18">
	<h5>{$module.headline} </h5><br/>
	<ul class="baseUl">
	        
		{foreach from=$arrList name=item item=item}
			<li class="baseLi">
			    <a href="/{$baseurl}/{$item.urlpart}.html" class="notActive">{$item.date|date_format} - {$item.name}</a>
			   
		            <div class="itemInner">
		                    {$item.teaser}
		            </div>
			    
			    
			</li>
		{/foreach}      
	</ul>
	<br class="clr">
</div>