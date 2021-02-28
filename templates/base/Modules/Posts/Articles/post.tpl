<div class="news-page justify">
	<div class="post-page main-col">
		<div class="content-white-block">
			<div class="white-block-inner">
				{?$years = $post.timestamp|date_format_lang:'%Y', 'ru'}
				{*{include file="components/buyers-breadcrumb.tpl"}*}
				{if !empty($post)}
					<div class="info-block">
						<h1>{$post.title}</h1>
						{if !empty($post.annotation)}
							<div class="annotation">{$post.annotation}</div>
						{/if}
						{if !empty($post.text)}
							{$post.text|html}
						{/if}
					</div>
				{/if}
			</div>
		</div>
	</div>
</div>




{*{?$pageTitle = $post.title . ' — Apex Sport — Экипировка для активного отдыха'}
{?$pageDescription = $post.title . ' — Apex Sport — Экипировка для активного и экстримального отдыха: куртки, шлема, штаны, защита'}

<div class="page-title">
	<div class="page-center">
		{*include file="components/breadcrumbs.tpl"*}
	{*	<nav class="breadcrumbs" itemprop="breadcrumb">
			<ul>
				<li class="a-inline-block"><a href="/">Apex Sport</a></li>
				<li class="a-inline-block"><a href="/news/">Новости</a></li>
			</ul>
		</nav>
		<h1>Новости</h1>
	</div>
</div>
		
<div class="main-content">
	<div class="page-center">
		<div class="post-cont clearbox">
			<article class="edited-text a-left">
				<a href="/news/" class="back-to-news a-inline-block">Ко всем новостям</a>
				<h1>{$post.title}</h1>
				<div class="post-header clearbox">
					{?$cover = $post.gallery->getCover()}
					{if !empty($cover)}
						<figure class="content-image a-left">
							<img src="{$cover->getUrl(185, 300)}" alt="{$post.title}">
						</figure>
					{/if}
					{if !empty($post.annotation)}
						<p class="main">{$post.annotation}</p>
					{/if}
					{if !empty($post.timestamp)}
						<p class="date">{$post.timestamp|date_format_lang:'%d %B %Y', 'ru'}</p>
					{/if}
				</div>
				{$post.text|html}
			</article>
			<aside class="aside-content a-right">
				{if !empty($last_news)}
					<ul class="news-list">
						{foreach from=$last_news item=n}
							<li class="news-item link-wrap">
								{?$cover = $n.gallery->getCover()}
								{if !empty($cover)}
									<a href="{$n.url}" class="cover">
										<img src="{$cover->getUrl(187, 150, 90, true)}" alt="{$n.title}" />
									</a>
								{/if}
								<a href="{$n.url}" class="title link-target">{$n.title}</a>
								<div class="date">{$n.timestamp|date_format_lang:'%d %B %Y', 'ru'}</div>
							</li>		
						{/foreach}
					</ul>
				{/if}	
			</aside>
		</div>	
		{include file="components/banners.tpl"}
	</div>
</div>*}