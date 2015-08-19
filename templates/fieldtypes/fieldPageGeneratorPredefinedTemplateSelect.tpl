<script>
	function alert_{$fieldcolname}_{$fieldid}(){ldelim}
		var change = confirm('Hvis du ændrer til prædifineret skabelon vil alt nuværende data blive slettet.Er du sikker på du vil skifte?');
		if(change) {ldelim}
			changed = 1;
			pressButton('SAVE');
		{rdelim}
	{rdelim}
</script>

<SELECT CLASS="forminput200" NAME="{$fieldcolname}_{$fieldid}" onchange="alert_{$fieldcolname}_{$fieldid}();">
{foreach from=$pretpl name=tpl item=item}
	<OPTION {if $item.selected}SELECTED{/if} VALUE="{$item.id}">{$item.name}</OPTION>
{/foreach}
</SELECT>