{if $module.headline}<h2>{$module.headline}</h2>{/if}

<table style="width:100%" class="mTable">
        <tbody>
{foreach item=i name=i from=$module.value1}
 <tr{cycle name="oddEven" values=' class="odd",'}>
{foreach item=j  from=$i.cols}
<td class="{cycle name="columns" values="label, value"}"{if $j.width} style="width:{$j.width}px;"{/if}><span
{if $j.width} style="text-align:right;"{/if}>{$j.content}</span></td>
{/foreach}
</tr>
{/foreach}

 </tbody>
</table>