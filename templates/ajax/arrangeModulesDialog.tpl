<div style="text-align:left;margin-bottom:4px;">
{cmstext value=dragndroptext}
			</div>
<ul id='modulearrange' style="height:300px; position:relative; overflow:auto;">
{foreach from=$elements item=e}
<li id='item_{$e.id}' class="{if !$e.placement}moveable{else}nonmovable{/if}">{$e.modname}{if $e.headline}: {$e.headline}{/if} {if $e.placement}({cmstext value=notmoveable}){/if}</li>
{/foreach}
</ul>
<table class="moduleElementRow" align="right">
	<tbody>
		<tr>
			<td><a id="arrangeModulesSubmitButton" onclick="tuksi.pagegenerator.saveArrangeModules();return false;" class="buttonType3 iconPositive" href="#"><span><span>{cmstext value=btnsave}</span></span></a></td>
			<td><a onclick="tuksi.window.close('#{ldelim}id{rdelim}');return false;" class="buttonType3 iconNegative" href="#"><span><span>{cmstext value=btncancel}</span></span></a></td>
		</tr>
	</tbody>
</table>
<script>
{literal}
Sortable.create('modulearrange',{only:'moveable',scroll:"modulearrange"});
{/literal}
</script>
