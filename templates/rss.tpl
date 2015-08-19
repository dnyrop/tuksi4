<?xml version="1.0" encoding="utf-8" ?>
<rss version="2.0">
	<channel>
		<title>{$header.title}</title>
		<link>{$header.link}</link>
		<description>{$header.description}</description>
		{if $header.lang}<language>{$header.lang}</language>{/if}
		{if $header.lastBuildDate}<lastBuildDate>{$header.lastBuildDate}</lastBuildDate>{/if}
		{if $header.pubDate}<lastBuildDate>{$header.pubDate}</pubDate>{/if}
		{if $header.docs}<docs>{$header.docs}/docs>{/if}
		<generator>Tuksi.com</generator>
		{if $header.managingEditor}<managingEditor>{$header.managingEditor}</managingEditor>{/if}
		{if $header.webMaster}<webMaster>{$header.webMaster}</webMaster>{/if}
		{foreach from=$items item=i}
			<item>
      {if $i.title}<title>{$i.title}</title>{/if}
      {if $i.link}<link>{$i.link}</link>{/if}
      {if $i.description}<description>{$i.description}</description>{/if}
      {if $i.pubDate}<pubDate>{$i.pubDate}</pubDate>{/if}
      {if $i.guid}<guid {if $i.guid.perma}isPermaLink="true"{/if}>{$i.guid.link}</guid>{/if}
    </item>
    {/foreach}
	</channel>
</rss>