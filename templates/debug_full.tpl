<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>{$page.title|default}</title>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta name="keywords" content="{$page.metakeywords|default}" />
	<meta name="description" content="{$page.metadescription|default}" />
	<link type="text/css" rel="stylesheet" href="/stylesheet/tuksi_debug.css" />
	
	<script type="text/javascript" src="/javascript/backend/libs/prototype.js"></script>
	
	<script type="text/javascript">
	var showDefault = '{$showtab}';
	{literal}
		
		document.observe("dom:loaded", function() {
			tabs();
		});

		// activate tabs
		function tabs(){
			
			var allTabs = $$('div.headerTabs a');
			$$('div.tabContent').invoke('hide');
			
			$('tuksi-debug-'+showDefault).show();
			$('tuksi-debug-link-'+showDefault).addClassName('active');

			// set events on tabs and (de)activate the relevant tabs  			
			allTabs.each(function(e){
				e.observe('click', function(){
					$$('div.tabContent').invoke('hide');
					$$('div.headerTabs a').each(function(i){
						i.removeClassName('active');
					})
					var tabId = e.id.replace('-link','');
					$(tabId).show();
					e.addClassName('active');	
					return false;
				});
			});
		}
		
	{/literal}
	</script>
	
</head>
<body>

<div class="mainHeader">
	<img src="/themes/default/images/TuksiLogo.png" />
	<div class="headerTabs" id="tabs">
		<ul>
			<li><a href="#" id="tuksi-debug-link-log" title="log">Log</a></li>
			<li><a href="#" id="tuksi-debug-link-sql" title="SQL">SQL</a></li>
			<li><a href="#" id="tuksi-debug-link-tpl" title="Template">Template</a></li>
			<li><a href="#" id="tuksi-debug-link-error" title="Errors">Errors</a></li>
			<li><a href="#" id="tuksi-debug-link-warning" title="Warnings">Warnings</a></li>
		</ul>
	</div>
</div>

<div class="tuksi-debug-content" id="tuksi-debug-content">
	<div id="tuksi-debug-log" class="tabContent">
		<div class="debug">
			<div class="debug_inner">
				<!-- <h1>Log</h1> -->
				<table>
         	{foreach item=row from=$tuksi_debug.log name=debug}
					<tr class="odd">
						<td><strong>{$row.name}</strong><br /> Called from {$row.trace}</td>
						<td align="right">Time&nbsp;executed:&nbsp;<strong>{$row.time}</strong></td>
					</tr>
					<tr>
						<td colspan="2">
							{$row.message|default}
						</td>
         	</tr>
					{/foreach}
				</table>
			</div>
		</div>
	</div>
	<div id="tuksi-debug-sql" class="tabContent">
		<div class="debug">
    	<div class="debug_inner">
			<table>
	  		{foreach item=row from=$tuksi_debug.sql name=debug}
				<tr class="odd {if $row.alert|default}alert{/if}">
					<td><strong>{$row.name}</strong><br /> called from {$row.trace}</td>
					<td align="right">Time&nbsp;executed:<strong>{$row.time}</strong><br>Execution&nbsp;time:<strong>{$row.exectime}</strong></td>
				</tr>
				<tr>
					<td colspan="2">
					{$row.sql|default}
					<br /><em>returned {$row.numrows} {if $row.numrows == 1}row{else}rows{/if}</em>
					</td>
				</tr>
				{if $row.alert|default}
					<tr>
						<td colspan="2">
						<strong>Message:</strong><br />
						{foreach from=$row.alert item=alert}
						{$alert.text}<br />
						{/foreach}
						</td>
					</tr>
					{if $row.explain|default}
						<tr>
							<td colspan="2">
								<div id="moreinfo_{$smarty.foreach.debug.iteration}">
								<table class="explain">
									<tr>
										<th>select_type</th>
										<th>table</th>
										<th>type</th>
										<th>possible_keys</th>
										<th>key</th>
										<th>key_len</th>
										<th>ref</th>
										<th>rows</th>
										<th>Extra</th>
									</tr>
									{foreach name=item item=item from=$row.explain}
									<tr>	
										<td>{$item.select_type}</td>
										<td>{$item.table}</td>
										<td>{$item.type}</td>
										<td>{$item.possible_keys}</td>
										<td>{$item.key}</td>
										<td>{$item.key_len}</td>
										<td>{$item.ref}</td>
										<td>{$item.rows}</td>
										<td>{$item.Extra}</td>
									</tr>
								{/foreach}
								</table>
								</div>
							</td>
						</tr>
					{/if}
				{else}
					<tr>
						<td colspan="2" align="right">
						<a href="#" onclick="Element.toggle('moreinfo_{$smarty.foreach.debug.iteration}');return false;">show/hide explain</a>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<div id="moreinfo_{$smarty.foreach.debug.iteration}" style="display: none;">
							<table class="explain">
								<tr>
									<th>select_type</th>
									<th>table</th>
									<th>type</th>
									<th>possible_keys</th>
									<th>key</th>
									<th>key_len</th>
									<th>ref</th>
									<th>rows</th>
									<th>Extra</th>
								</tr>
								{foreach name=item item=item from=$row.explain}
								<tr>	
									<td>{$item.select_type}</td>
									<td>{$item.table}</td>
									<td>{$item.type}</td>
									<td>{$item.possible_keys}</td>
									<td>{$item.key}</td>
									<td>{$item.key_len}</td>
									<td>{$item.ref}</td>
									<td>{$item.rows}</td>
									<td>{$item.Extra}</td>
								</tr>
							{/foreach}
							</table>
							</div>
						</td>
					</tr>
				{/if}
 				{/foreach}
			</table>
			</div>
		</div>
	</div>
	<div id="tuksi-debug-tpl" class="tabContent">
		<div class="debug">
			<div class="debug_inner">
				<table>
					{foreach item=row from=$tuksi_debug.tpl name=tpl}
					<tr class="odd">
						<td><strong>{$row.name}</strong></td>
						<td align="right">Time executed:<strong>{$row.time}</strong><br>Execution time:<strong>{$row.exectime}</strong></td>
					</tr>
					<tr>
						<td colspan="2">
							{$row.message|default}
						</td>
         	</tr>
 					{/foreach}
				</table>
			</div>
		</div>
	</div>		
	<div id="tuksi-debug-warning" class="tabContent">
		<div class="debug">
			<div class="debug_inner">
			<table>
					{foreach item=row from=$tuksi_debug.warning name=debug}
					<tr class="odd">
						<td><strong>{$row.name}</strong><br /> called from {$row.trace}</td>
						<td align="right">{if $row.time|default}time executed:<strong>{$row.time}</strong>{/if}</td>
					</tr>
					<tr>
						<td colspan="2">
						{$row.message|default}
						{$row.sql|default}
						</td>
					</tr>
					{if $row.alert|default}
					<tr>
						<td colspan="2">
						{foreach from=$row.alert item=alert}
						{$alert.text}<br />
						{/foreach}
						</td>
					</tr>
					{/if}
					{if $row.explain|default}
						<tr>
							<td colspan="2">
								<div id="moreinfo_{$smarty.foreach.debug.iteration}">
								<table class="explain">
									<tr>
										<th>select_type</th>
										<th>table</th>
										<th>type</th>
										<th>possible_keys</th>
										<th>key</th>
										<th>key_len</th>
										<th>ref</th>
										<th>rows</th>
										<th>Extra</th>
									</tr>
									{foreach name=item item=item from=$row.explain}
									<tr>	
										<td>{$item.select_type}</td>
										<td>{$item.table}</td>
										<td>{$item.type}</td>
										<td>{$item.possible_keys}</td>
										<td>{$item.key}</td>
										<td>{$item.key_len}</td>
										<td>{$item.ref}</td>
										<td>{$item.rows}</td>
										<td>{$item.Extra}</td>
									</tr>
								{/foreach}
								</table>
								</div>
							</td>
						</tr>
					{/if}
 					{/foreach}
				</table>
			</div>
		</div>
	</div>
	<div id="tuksi-debug-error" class="tabContent">
		<div class="debug">
			<div class="debug_inner">
			<table cellpadding="0" cellspacing="0" border="0" width="762">
					{foreach item=row from=$tuksi_debug.error name=debug}
					<tr class="odd">
						<td><strong>{$row.name}</strong><br /> called from {$row.trace}</td>
						<td align="right">time executed:<strong>{$row.time}</strong> in file: <strong>{$row.file}</strong></td>
					</tr>
					<tr>
						<td colspan="2">
						{$row.message|default}
						</td>
         	</tr>
					{/foreach}
				</table>
			</div>
		</div>
	</div>
</div>

<div id="tuksi-debug-alert" class="tuksi-debug-alert" style="display:none;">
tuksi.debug: 
</div>
</body>
</html>
