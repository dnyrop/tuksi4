{if $error}
{cmstext value="no_results"}
{else}
{foreach from=$arrSearch key=idx item=search}
<div style="padding: 7px;{cycle values=" background: #D6E9EE;,''"}">
	{if $search.date}{$search.date}: {/if}
	<a href="#" onclick="showText(this.id)" id="{$htmltagname}_eid_{$idx}">{$search.name}</a>
	<a href="#" onclick="addRelation('{$search.id}', {$fieldid}, '{$htmltagname}', '');" class="buttonType3" style="position: static;">
		<span style="float: right;"><span>{cmstext value="add"}</span></span>
	</a>
	<div style="display: none; padding: 2px;" id="{$htmltagname}_eid_{$idx}_text">{$search.text}</div>
</div>
{/foreach}
{/if}