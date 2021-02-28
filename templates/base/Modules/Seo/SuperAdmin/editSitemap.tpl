{?$pageTitle = 'Просмотр sitemap.xml — ' . (!empty($confTitle) ? $confTitle : '')}
<div class="content-top">
	<h1>Просмотр sitemap.xml</h1>
</div>
<div class="content-scroll">
	<div class="white-blocks viewport">
		{if !empty($sitemaps)}
			<ul>
				{foreach from=$sitemaps item=sitemap}
					<li><a href="{$sitemap}" target="_blank">http://{$quicky.server.HTTP_HOST}{$sitemap}</a> </li>
				{/foreach}
			</ul>
		{else}
			Файл sitemap не созданы
		{/if}
	</div>
</div>	