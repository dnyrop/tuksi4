{literal}
<style>
.idioti {
	border-collapse:collapse;
}
.idioti td{
	border: 1px solid white;
}
</style>
{/literal}
<script type="text/javascript">
	if(typeof DatePicker !== 'undefined') {ldelim} 
		DatePicker.months =  '{cmstext value="month_short"}'.split(',');
		DatePicker.days = '{cmstext value="day_short"}'.split(',');
		DatePicker.time = '{cmstext value="time"}';
 {rdelim}	
</script>
<table>
<tr>
	<td valign="middle">
		<input {if $options.error}style="border:1px solid #7F1301;"{/if} class="text" type="text" name="{$options.htmltagname}" id="{$options.htmltagname}" value="{$options.value}" style="width:{if $options.usetime || $options.usehour}100{else}70{/if}px;">
		<a href="#"  id="_{$options.htmltagname}_link"></a>		
	</td>
	<td valign="middle">
	
	<div class="mCalenderPicker">
					<div class="positionAbsolute">
						<a href="#" onclick="DatePicker.toggleDatePicker('{$options.htmltagname}',{$options.usetime},{$options.usehour});return false;" id="datespick_btn_{$options.htmltagname}" class="buttonType3 iconCalender"><span><span>{cmstext value="choosedate"}</span></span></a>						
						<div class="tableWrapper" style="display:none;"  style="z-index:10000;position:relative;" id="datespick_{$options.htmltagname}">
						</div><!--//End tableWrapper-->
					</div><!--//End positionAbsolute-->
				</div><!--//End mCalenderPicker-->
	</td>
</tr>
</table>
