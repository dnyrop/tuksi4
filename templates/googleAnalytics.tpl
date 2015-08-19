{if $accountid|default}
<script type="text/javascript">
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', '{$accountid}']);
{if $setLocal|default}
	_gaq.push(['_setLocalRemoteServerMode']);
{/if}
{if $domain|default}
	_gaq.push(['_setDomainName', '{$domain}']);
{/if}
{if $replace|default}
	_gaq.push(['_trackPageview', '{$replace}']);
{else}
	_gaq.push(['_trackPageview']);
{/if}

	(function(d,t) {ldelim}
		var g=d.createElement(t),s=d.getElementsByTagName(t)[0];g.async=1;g.type="text/javascript";
		g.src=("https:"==location.protocol?"//ssl":"//www")+".google-analytics.com/ga.js";
		s.parentNode.insertBefore(g,s)
	{rdelim})(document,"script");
</script>
{/if}
