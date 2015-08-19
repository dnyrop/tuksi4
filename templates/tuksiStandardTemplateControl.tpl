<script>
{literal}
    Event.observe(window, 'load', function(){
		
    	hide_elms(($("isHidden")));
    	
    }); 
{/literal}
</script>
{foreach from=$hiddens item=hidden name=hiddens}
<input type="hidden" name="{$hidden.name}" id="{$hidden.name}" value="{$hidden.value}">
{/foreach}

{foreach from=$group item=item name=group}
	{if $item.headline}
		<div class="mHeader ">
			<h6>{if $item.collapsible == 1}<span id="{if $item.is_collapsed == 1}isHidden{/if}" class="mExpand " onclick="hide_elms(this);"><img src="../../themes/default/images/icons/tuksi_minus.gif"></span>{/if}{$item.headline}</h6>
		</div>
	{/if}

	{if $item.fullwidthelement}
		{$item.fullwidthelement}
	{/if}
	{if $item.element}
	{if $item.element.options.fullwidth}
		
			{if $item.element.name}
				<table class="moduleElementRow">
					<tr>
						<td class="column1"><label>{$item.element.name}:</label></td>
					</tr>
				</table>
			{/if}
			<table class="moduleElementRow" width="100%">
				<tr>
					{foreach from=$item.element.html item=html}
						<td {if $item.element.options.align == 'right'}align='right'{/if}>{$html}</td>
					{/foreach}
				</tr>
			</table>
		{elseif $item.element.name}
			<table class="moduleElementRow moduleElementRowFullWidth">
				<tbody>
					<tr>
						<td class="column1"><label>{$item.element.name}</label></td>
						{foreach from=$item.element.html item=html}
						<td>{$html}</td>
						{/foreach}
					</tr>
				</tbody>
			</table>	
		{else}
			<table class="moduleElementRow" width="100%">
				<tbody>
					<tr>
					{foreach from=$item.element.html item=html}
						<td>{$html}</td>
						{/foreach}
					</tr>
				</tbody>
			</table>	
		{/if}
		
	{/if}
{/foreach}

{foreach from=$rawhtml item=h name=html}
{$h.html}
{/foreach}
