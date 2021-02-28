{?$pageTitle = $post.title . ' — Apex Sport — Экипировка для активного отдыха'}
{?$pageDescription = $post.title . ' — Apex Sport — Экипировка для активного и экстримального отдыха: куртки, шлема, штаны, защита'}

<div class="page-title">
	<div class="page-center">
		{*include file="components/breadcrumbs.tpl"*}
		<nav class="breadcrumbs" itemprop="breadcrumb">
			<ul>
				<li class="a-inline-block"><a href="/">Apex Sport</a></li>
			</ul>
		</nav>
		<h1>Статьи</h1>
	</div>
</div>
		
<div class="main-content">
	<div class="page-center">
		<div class="post-cont clearbox">
			<article class="edited-text a-left">
				<h1>{$post.title}</h1>
				{if !empty($post.annotation)}
					<p class="main">{$post.annotation}</p>
				{/if}
				{$post.text|html}
			</article>
			<aside class="aside-content a-right">
				{if !empty($types)}
					<section class="types-list-cont">
						<h3><div class="a-inline-block">Товары по теме</div></h3>
						<ul class="types-list">
							{foreach from=$types item=t}
								{if $t.id != 1}
									<li class="related-type">
										<a href="{$t->getUrl()}">
											<span class="hatch"></span>
											{if !empty($t.cover)}
												<span class="cover">
													<img src="{$t.cover->getUrl(66, 80)}" />
												</span>
											{/if}
											<span class="title">{$t.title}</span>
											<span class="count">{$t.counters.visible_items|plural_form:'модель':'модели':'моделей'}</span>										
										</a>
									</li>
								{/if}
							{/foreach}
						</ul>
					</section>
					<div class="verticalList clearbox" data-type_ids="{implode(',', array_keys($types))}"></div>
				{/if}				
			</aside>
			{if !empty($articles)}					
				<section class="float-articles m-fixed">
					<div class="close"></div>
					<h4>Ещё по теме</h4>
					<ul class="art-list">
						{foreach from=$articles item=ar}
							<li><a href="{$ar.url}">{$ar.title}</a></li>
						{/foreach}
					</ul>
				</section>
				<div class="bottom-line"></div>
			{/if}
		</div>	
		{include file="components/banners.tpl"}
	</div>
</div>